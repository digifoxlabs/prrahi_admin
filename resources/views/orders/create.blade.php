@extends($layout)

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

@include('orders._form', [
    'action' => route($routePrefix.'.orders.store'),
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
            distributors: @json($distributors),

            selectedDistributorId: '',
            billingAddress: '',
            search: '',
            cart: [],
            showProductPopup: false,

            subtotal: 0,
            orderDiscount: 0,
            cgst: 0,
            sgst: 0,
            isIntraState: true,
            igst: 0,
            roundOff: 0,
            total: 0,

            fillAddress(){
                let d = this.distributors.find(x => x.id == this.selectedDistributorId);
                if(!d) return;
                this.billingAddress =
                    `${d.firm_name}\n` +
                    `${d.address_line_1 ?? ''} ${d.address_line_2 ?? ''}\n` +
                    `${d.town ?? ''}, ${d.district ?? ''}\n` +
                    `State: ${d.state ?? ''} - ${d.pincode ?? ''}`;

                // ✅ GST LOGIC
                this.isIntraState = (d.state || '').toLowerCase() === 'assam';

                // reset taxes
                this.recalculate();


            },

            filteredProducts(){
                return this.products.filter(p =>
                    p.name.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            addProduct(product){
                let item = this.cart.find(i => i.id === product.id);
                if(item){
                    item.qty++;
                } else {

                    let label = product.attributes
                    ? product.parent.name
                    : product.name;

                    if(product.attributes){
                        label += ' - ' + (product.attributes.fragrance ?? '');
                        if(product.attributes.size){
                            label += ' (' + product.attributes.size + ')';
                        }
                    }
                    this.cart.push({
                        id: product.id,
                        name: label,
                        code: product.code,
                        qty: 1,
                        rate: Number(product.ptd_per_dozen) || 0,
                        discount: Number(product.distributor_discount_percent) || 0,
                        base_unit: product.attributes
                                    ? product.parent.base_unit
                                    : product.base_unit,
                        amount: 0
                    });
                }
                this.recalculate();
            },

            removeItem(index){
                this.cart.splice(index,1);
                this.recalculate();
            },

            recalculate(){
                this.subtotal = 0;
                this.cart.forEach(item => {
                    let gross = item.qty * item.rate;
                    let disc = gross * (item.discount / 100);
                    item.amount = gross - disc;
                    this.subtotal += item.amount;
                });

                let taxable = this.subtotal - this.orderDiscount;
                // this.cgst = taxable * 0.025;
                // this.sgst = taxable * 0.025;

                // let rawTotal = taxable + this.cgst + this.sgst;

                // RESET TAXES
                this.cgst = 0;
                this.sgst = 0;
                this.igst = 0;

                if (this.isIntraState) {
                    // Assam → CGST + SGST
                    this.cgst = taxable * 0.025;
                    this.sgst = taxable * 0.025;
                } else {
                    // Inter-state → IGST
                    this.igst = taxable * 0.05;
                }

                let rawTotal = taxable + this.cgst + this.sgst + this.igst;



                let decimal = rawTotal % 1;
                this.roundOff = decimal < 0.5 ? -decimal : (1 - decimal);

                this.roundOff = parseFloat(this.roundOff.toFixed(2));
                this.total = parseFloat((rawTotal + this.roundOff).toFixed(2));
            },

            applyRoundOff(){
                let base = (this.subtotal - this.orderDiscount) + this.cgst + this.sgst;
                this.roundOff = parseFloat(this.roundOff.toFixed(2));
                this.total = parseFloat((base + this.roundOff).toFixed(2));
            }
        }
    }
    </script>







@endpush
