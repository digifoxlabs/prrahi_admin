@extends($layout)

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

@include('retailers.orders._form', [
    'action' => route($routePrefix.'.retail.orders.store'),
    'method' => 'POST',
    'order'  => null
])
@endsection

</div>

@push('scripts')
<script>
    window.pageXData = { page: 'createOrder' };
</script>

<script>
function posOrder(){
    return {
        products: @json($products),
        retailers: @json($retailers),
        retailOrderSettings: {
            checkStockBeforeOrder: @js(\App\Models\Setting::get('retail_orders', 'check_stock_before_order', '0') == '1'),
            allowZeroStockOrder: @js(\App\Models\Setting::get('retail_orders', 'allow_zero_stock_order', '0') == '1'),
        },
        previewUrl: @json(route($routePrefix.'.retail.orders.preview')),
        csrfToken: @json(csrf_token()),

        selectedRetailerId: '',
        billingAddress: '',
        search: '',
        cart: [],
        showProductPopup: false,

        subtotal: 0,
        orderDiscount: 0,
        cgst: 0,
        sgst: 0,
        igst: 0,
        isIntraState: true,
        roundOff: 0,
        total: 0,

        previewLoading: false,
        previewError: '',
        stockError: '',
        previewDebounceTimer: null,
        previewRequestId: 0,

        init(){
            this.syncCartStock();
        },

        toNumber(value){
            const parsed = Number(value);
            return Number.isFinite(parsed) ? parsed : 0;
        },

        firstErrorMessage(data){
            const errors = data?.errors;
            if (!errors || typeof errors !== 'object') return '';

            const firstFieldErrors = Object.values(errors).find(
                (fieldErrors) => Array.isArray(fieldErrors) && fieldErrors.length > 0
            );

            return firstFieldErrors ? String(firstFieldErrors[0]) : '';
        },

        resolveCsrfToken(){
            return this.csrfToken
                || document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('input[name="_token"]')?.value
                || '';
        },

        filteredProducts(){
            return this.products.filter(p =>
                p.name.toLowerCase().includes(this.search.toLowerCase())
            );
        },

        isStockCheckEnabled(){
            return Boolean(this.retailOrderSettings?.checkStockBeforeOrder);
        },

        canOrderZeroStock(){
            return Boolean(this.retailOrderSettings?.allowZeroStockOrder);
        },

        canBypassZeroStock(itemOrProduct){
            return this.isStockCheckEnabled()
                && this.canOrderZeroStock()
                && this.productStock(itemOrProduct) <= 0;
        },

        productStock(product){
            if (!product) return 0;

            if (product.type === 'variable') {
                return (product.variants || []).reduce((sum, variant) => sum + this.productStock(variant), 0);
            }

            return Math.max(0, parseInt(product.total_stock ?? product.available_stock ?? 0, 10) || 0);
        },

        canAddProduct(product){
            if (!this.isStockCheckEnabled()) {
                return true;
            }

            return this.productStock(product) > 0 || this.canBypassZeroStock(product);
        },

        findProductById(productId){
            for (const product of this.products) {
                if (String(product.id) === String(productId)) {
                    return product;
                }

                const variant = (product.variants || []).find(item => String(item.id) === String(productId));
                if (variant) {
                    return variant;
                }
            }

            return null;
        },

        itemStock(item){
            const product = this.findProductById(item.id);
            return this.productStock(product);
        },

        normalizeItem(item){
            const availableStock = this.itemStock(item);
            let normalizedQty = Math.max(1, parseInt(item.qty, 10) || 1);

            if (this.isStockCheckEnabled() && !(this.canOrderZeroStock() && availableStock <= 0)) {
                normalizedQty = Math.min(normalizedQty, Math.max(availableStock, 1));
            }

            return {
                ...item,
                qty: normalizedQty,
                available_stock: availableStock,
            };
        },

        syncCartStock(){
            this.cart = this.cart.map(item => this.normalizeItem(item));
        },

        validateStock(showMessage = true){
            this.syncCartStock();

            if (!this.isStockCheckEnabled()) {
                this.stockError = '';
                return true;
            }

            const outOfStockItem = this.cart.find(item => item.available_stock <= 0 && !this.canOrderZeroStock());
            if (outOfStockItem) {
                if (showMessage) {
                    this.stockError = `${outOfStockItem.name} is out of stock. Remove it to continue.`;
                }
                return false;
            }

            const invalidQtyItem = this.cart.find(item => item.available_stock > 0 && this.toNumber(item.qty) > item.available_stock);
            if (invalidQtyItem) {
                if (showMessage) {
                    this.stockError = `Quantity for ${invalidQtyItem.name} cannot exceed available stock (${invalidQtyItem.available_stock}).`;
                }
                return false;
            }

            this.stockError = '';
            return true;
        },

        hasStockIssues(){
            if (!this.isStockCheckEnabled()) {
                return false;
            }

            return this.cart.some(item => {
                const availableStock = this.itemStock(item);
                const quantity = Math.max(1, parseInt(item.qty, 10) || 1);

                if (availableStock <= 0) {
                    return !this.canOrderZeroStock();
                }

                return quantity > availableStock;
            });
        },

        fillAddress(){
            const retailer = this.retailers.find(x => x.id == this.selectedRetailerId);
            if (!retailer) {
                this.billingAddress = '';
                return;
            }

            this.billingAddress =
                `${retailer.retailer_name}\n` +
                `${retailer.address_line_1 ?? ''} ${retailer.address_line_2 ?? ''}\n` +
                `${retailer.town ?? ''}, ${retailer.district ?? ''}\n` +
                `State: ${retailer.state || '-'} - ${retailer.pincode || '-'}\n` +
                `GST: ${retailer.gst || '-'}`;

            this.schedulePreview();
        },

        addProduct(product){
            const availableStock = this.productStock(product);

            if (this.isStockCheckEnabled() && availableStock <= 0 && !this.canOrderZeroStock()) {
                this.stockError = `${product.name} is out of stock and cannot be added.`;
                return;
            }

            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                existing.qty = this.toNumber(existing.qty) + 1;

                if (this.isStockCheckEnabled() && !(this.canOrderZeroStock() && availableStock <= 0)) {
                    existing.qty = Math.min(existing.qty, availableStock);
                }

                existing.available_stock = availableStock;

                if (this.isStockCheckEnabled() && availableStock > 0 && this.toNumber(existing.qty) >= availableStock) {
                    this.stockError = `${existing.name} has only ${availableStock} units available.`;
                }
            } else {
                let label = product.attributes
                    ? (product.parent?.name || product.name)
                    : product.name;

                if (product.attributes) {
                    label += ' - ' + (product.attributes.fragrance ?? '');
                    if (product.attributes.size) {
                        label += ' (' + product.attributes.size + ')';
                    }
                }

                this.cart.push({
                    id: product.id,
                    name: label,
                    code: product.code,
                    qty: 1,
                    available_stock: availableStock,
                    rate: this.toNumber(product.ptr_per_dozen),
                    discount: this.toNumber(product.retailer_discount_percent),
                    base_unit: product.attributes
                        ? (product.parent?.base_unit ?? product.base_unit)
                        : product.base_unit,
                    amount: 0
                });
            }

            this.validateStock(false);
            this.schedulePreview();
        },

        removeItem(index){
            this.cart.splice(index, 1);
            this.validateStock(false);
            this.schedulePreview();
        },

        recalculate(){
            this.validateStock(false);
            this.schedulePreview();
        },

        recalculateItem(item){
            item.qty = Math.max(1, parseInt(item.qty, 10) || 1);

            if (this.isStockCheckEnabled() && !(this.canOrderZeroStock() && item.available_stock <= 0) && item.available_stock > 0) {
                item.qty = Math.min(item.qty, item.available_stock);
            }

            this.recalculate();
        },

        quantityInputMax(item){
            if (!this.isStockCheckEnabled()) {
                return null;
            }

            if (this.canOrderZeroStock() && this.toNumber(item.available_stock) <= 0) {
                return null;
            }

            return Math.max(this.toNumber(item.available_stock), 1);
        },

        shouldWarnMaxQuantity(item){
            return this.isStockCheckEnabled()
                && this.toNumber(item.available_stock) > 0
                && this.toNumber(item.qty) >= this.toNumber(item.available_stock);
        },

        shouldShowOutOfStockNotice(item){
            return this.toNumber(item.available_stock) <= 0;
        },

        outOfStockNotice(item){
            return this.canOrderZeroStock()
                ? 'This product is currently out of stock, but order creation is allowed by settings.'
                : 'This product is out of stock and cannot be ordered.';
        },

        schedulePreview(){
            clearTimeout(this.previewDebounceTimer);
            this.previewDebounceTimer = setTimeout(() => this.fetchPreview(), 250);
        },

        resetPreviewTotals(){
            this.subtotal = 0;
            this.cgst = 0;
            this.sgst = 0;
            this.igst = 0;
            this.roundOff = 0;
            this.total = 0;
            this.isIntraState = true;
            this.cart = this.cart.map(item => ({ ...item, amount: 0, available_stock: this.itemStock(item) }));
        },

        async fetchPreview(){
            this.previewError = '';

            if (!this.validateStock()) {
                this.resetPreviewTotals();
                return;
            }

            if (!this.selectedRetailerId || this.cart.length === 0) {
                this.resetPreviewTotals();
                return;
            }

            const payload = {
                _token: this.resolveCsrfToken(),
                retailer_id: this.selectedRetailerId,
                discount: this.toNumber(this.orderDiscount),
                items: this.cart.map(item => ({
                    product_id: item.id,
                    quantity: Math.max(1, parseInt(item.qty, 10) || 1),
                })),
            };

            const currentRequestId = ++this.previewRequestId;
            this.previewLoading = true;

            try {
                const response = await fetch(this.previewUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.resolveCsrfToken(),
                    },
                    body: JSON.stringify(payload),
                });
                const raw = await response.text();
                let data = {};

                try {
                    data = raw ? JSON.parse(raw) : {};
                } catch (e) {
                    data = {
                        message: response.status === 419
                            ? 'CSRF token mismatch. Refresh the page and try again.'
                            : 'Preview failed',
                    };
                }

                if (!response.ok) {
                    const errorMessage =
                        this.firstErrorMessage(data)
                        || data?.message
                        || `Preview failed (${response.status})`;

                    throw new Error(errorMessage);
                }

                if (currentRequestId !== this.previewRequestId) return;
                this.applyPreview(data.preview || {});
            } catch (error) {
                if (currentRequestId !== this.previewRequestId) return;
                this.previewError = error.message || 'Preview failed';
                this.resetPreviewTotals();
            } finally {
                if (currentRequestId === this.previewRequestId) {
                    this.previewLoading = false;
                }
            }
        },

        applyPreview(preview){
            const previewItemsByProductId = new Map(
                (preview.items || []).map(item => [String(item.product_id), item])
            );

            this.cart = this.cart.map(item => {
                const previewItem = previewItemsByProductId.get(String(item.id));
                if (!previewItem) return this.normalizeItem(item);

                return this.normalizeItem({
                    ...item,
                    qty: this.toNumber(previewItem.quantity),
                    rate: this.toNumber(previewItem.price),
                    discount: this.toNumber(previewItem.discount_percent),
                    base_unit: previewItem.base_unit ?? item.base_unit,
                    amount: this.toNumber(previewItem.total),
                });
            });

            this.isIntraState = Boolean(preview.is_intra_state);
            this.subtotal = this.toNumber(preview.subtotal);
            this.orderDiscount = this.toNumber(preview.discount);
            this.cgst = this.toNumber(preview.cgst);
            this.sgst = this.toNumber(preview.sgst);
            this.igst = this.toNumber(preview.igst);
            this.roundOff = this.toNumber(preview.round_off);
            this.total = this.toNumber(preview.total_amount);
        },

        handleSubmit(event){
            if (!this.validateStock() || this.previewLoading || this.previewError) {
                event.preventDefault();
            }
        },
    };
}
</script>







@endpush
