@extends($layout)

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

@include('retailers.orders._form', [
    'action' => route($routePrefix.'.retail.orders.update',$order),
    'method' => 'PUT',
    'order'  => $order,
])
@endsection

</div>

@push('scripts')
<script>
    window.pageXData = { page: 'updateOrder' };
</script>

<script>
function posOrder(){
    return {
        products: @json($products),
        retailers: @json($retailers),
        previewUrl: @json(route($routePrefix.'.retail.orders.preview')),
        csrfToken: @json(csrf_token()),

        selectedRetailerId: @json($order->retailer_id),
        billingAddress: @json($order->billing_address),
        search: '',

        cart: @json($cartItems),
        showProductPopup: false,
        isIntraState: {{ $order->igst > 0 ? 'false' : 'true' }},
        subtotal: {{ $order->subtotal }},
        orderDiscount: {{ $order->discount }},
        cgst: {{ $order->cgst }},
        sgst: {{ $order->sgst }},
        igst: {{ $order->igst }},
        roundOff: {{ $order->round_off }},
        total: {{ $order->total_amount }},

        previewLoading: false,
        previewError: '',
        previewDebounceTimer: null,
        previewRequestId: 0,

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

        fillAddress(){
            const retailer = this.retailers.find(x => x.id == this.selectedRetailerId);
            if (!retailer) return;

            this.billingAddress =
                `${retailer.retailer_name}\n` +
                `${retailer.address_line_1 ?? ''} ${retailer.address_line_2 ?? ''}\n` +
                `${retailer.town ?? ''}, ${retailer.district ?? ''}\n` +
                `State: ${retailer.state || '-'} - ${retailer.pincode || '-'}\n` +
                `GST: ${retailer.gst || '-'}`;

            this.schedulePreview();
        },

        filteredProducts(){
            return this.products.filter(p =>
                p.name.toLowerCase().includes(this.search.toLowerCase())
            );
        },

        addProduct(product){
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                existing.qty = this.toNumber(existing.qty) + 1;
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
                    rate: this.toNumber(product.ptr_per_dozen),
                    discount: this.toNumber(product.retailer_discount_percent),
                    base_unit: product.attributes
                        ? (product.parent?.base_unit ?? product.base_unit)
                        : product.base_unit,
                    amount: 0
                });
            }

            this.schedulePreview();
        },

        removeItem(index){
            this.cart.splice(index, 1);
            this.schedulePreview();
        },

        recalculate(){
            this.schedulePreview();
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
            this.cart = this.cart.map(item => ({ ...item, amount: 0 }));
        },

        async fetchPreview(){
            this.previewError = '';

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
                if (!previewItem) return item;

                return {
                    ...item,
                    qty: this.toNumber(previewItem.quantity),
                    rate: this.toNumber(previewItem.price),
                    discount: this.toNumber(previewItem.discount_percent),
                    base_unit: previewItem.base_unit ?? item.base_unit,
                    amount: this.toNumber(previewItem.total),
                };
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
    };
}
</script>


@endpush
