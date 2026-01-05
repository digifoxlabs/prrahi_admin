@extends('admin.admin-layout')

@section('page-content')
<div x-data="posOrder()" class="grid grid-cols-12 gap-4">

    <!-- ================= LEFT: PRODUCT SELECTION ================= -->
    <div class="col-span-4 bg-white rounded-xl p-4 h-[85vh] overflow-y-auto border">

        <div class="relative mb-4">
            <input type="text"
                   x-model="search"
                   placeholder="Search products..."
                   class="w-full border rounded-lg p-2 pr-8 text-sm">
            <button x-show="search.length"
                    @click="search=''"
                    type="button"
                    class="absolute right-2 top-2 text-gray-400 hover:text-gray-700">
                ✕
            </button>
        </div>

        <template x-for="product in filteredProducts()" :key="product.id">
            <div class="mb-3">

                <!-- SIMPLE -->
                <template x-if="product.type === 'simple'">
                    <button @click="addProduct(product)"
                        class="w-full flex items-center gap-3 p-2 border rounded-lg hover:bg-indigo-50">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center font-bold">P</div>
                        <div>
                            <p class="font-semibold text-sm" x-text="product.name"></p>
                            <p class="text-xs text-gray-500" x-text="product.code"></p>
                        </div>
                    </button>
                </template>

                <!-- VARIABLE -->
                <template x-if="product.type === 'variable'">
                    <div class="border rounded-lg p-2 bg-gray-50">
                        <p class="font-semibold text-sm mb-1" x-text="product.name"></p>
                        <template x-for="variant in product.variants" :key="variant.id">
                            <button @click="addProduct(variant)"
                                class="block w-full text-left text-xs px-2 py-1 rounded hover:bg-indigo-100">
                                →
                                <span x-text="variant.attributes?.fragrance"></span>
                                <span x-show="variant.attributes?.size">
                                    ( <span x-text="variant.attributes.size"></span> )
                                </span>
                            </button>
                        </template>
                    </div>
                </template>

            </div>
        </template>
    </div>

    <!-- ================= RIGHT: POS ================= -->
    <form method="POST"
          action="{{ route('admin.orders.store') }}"
          class="col-span-8 bg-white rounded-xl p-4 border">
        @csrf

        {{-- <!-- ================= ORDER HEADER ================= -->
        <div class="grid grid-cols-4 gap-3 mb-3">

            <!-- Distributor -->
            <select name="distributor_id"
                    x-model="selectedDistributorId"
                    @change="fillAddress()"
                    required
                    class="border rounded-lg p-2 text-sm">
                <option value="">Select Distributor</option>
                @foreach($distributors as $d)
                    <option value="{{ $d->id }}">{{ $d->firm_name }}</option>
                @endforeach
            </select>

            <input type="text"
                   name="order_number"
                   placeholder="Order Number (optional)"
                   class="border rounded-lg p-2 text-sm">

            <!-- Order Date -->
            <div class="relative">
                <input type="date"
                       name="order_date"
                       value="{{ now()->toDateString() }}"
                       class="border rounded-lg p-2 pr-10 text-sm w-full">
                <button type="button"
                        onclick="this.previousElementSibling.showPicker && this.previousElementSibling.showPicker()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                    📅
                </button>
            </div>

            <div></div>
        </div>

        <!-- ================= ADDRESS (NEW) ================= -->
        <textarea name="billing_address"
                  x-model="billingAddress"
                  rows="3"
                  placeholder="Billing Address"
                  class="w-full border rounded-lg p-2 text-sm mb-4"></textarea> --}}

<div class="grid grid-cols-12 gap-3 mb-3">
<!-- ================= ORDER HEADER ================= -->


    <!-- LEFT: Order Meta -->
    <div class="col-span-6 space-y-3">

        <!-- Distributor -->
        <select name="distributor_id"
                x-model="selectedDistributorId"
                @change="fillAddress()"
                required
                class="w-full border rounded-lg p-2 text-sm">
            <option value="">Select Distributor</option>
            @foreach($distributors as $d)
                <option value="{{ $d->id }}">{{ $d->firm_name }}</option>
            @endforeach
        </select>

        <!-- Order Number -->
        <input type="text"
               name="order_number"
               placeholder="Order Number (optional)"
               class="w-full border rounded-lg p-2 text-sm">

        <!-- Order Date -->
        <div class="relative">
            <input type="date"
                   name="order_date"
                   value="{{ now()->toDateString() }}"
                   class="w-full border rounded-lg p-2 pr-10 text-sm">
            <button type="button"
                    onclick="this.previousElementSibling.showPicker && this.previousElementSibling.showPicker()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                📅
            </button>
        </div>
    </div>

    <!-- RIGHT: Address -->
    <div class="col-span-6">
        <textarea name="billing_address"
                  x-model="billingAddress"
                  rows="5"
                  placeholder="Billing Address"
                  class="w-full h-full border rounded-lg p-2 text-sm resize-none"></textarea>
    </div>



</div>




        <!-- ================= POS TABLE ================= -->
        <table class="w-full text-sm border">
            <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Product</th>
                <th class="p-2">Code</th>
                <th class="p-2">Qty</th>
                <th class="p-2">Rate</th>
                <th class="p-2">Disc %</th>
                <th class="p-2 text-right">Amount</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(item,index) in cart" :key="item.id">
                <tr class="border-t">
                    <td class="p-2" x-text="item.name"></td>
                    <td class="p-2" x-text="item.code"></td>
                    <td class="p-2">
                        <input type="number" min="1"
                               x-model.number="item.qty"
                               @input="recalculate()"
                               class="w-16 border rounded p-1 text-sm">
                    </td>
                    <td class="p-2" x-text="item.rate.toFixed(2)"></td>
                    <td class="p-2" x-text="item.discount"></td>
                    <td class="p-2 text-right" x-text="item.amount.toFixed(2)"></td>
                    <td class="p-2 text-center">
                        <button type="button"
                                @click="removeItem(index)"
                                class="text-red-500 hover:text-red-700">
                            ✕
                        </button>
                    </td>

                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                    <input type="hidden" :name="`items[${index}][quantity]`" :value="item.qty">
                    <input type="hidden" :name="`items[${index}][rate]`" :value="item.rate">
                    <input type="hidden" :name="`items[${index}][base_unit]`" :value="item.base_unit">
                    <input type="hidden" :name="`items[${index}][amount]`" :value="item.amount">
                </tr>
            </template>
            </tbody>
        </table>

        <!-- ================= TOTALS ================= -->
        <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
            <div></div>
            <div class="space-y-1 text-right">

                <p>Sub Total: <span x-text="subtotal.toFixed(2)"></span></p>

                <div class="flex justify-end gap-2">
                    <span>Discount:</span>
                    <input type="number" step="0.01"
                           x-model.number="orderDiscount"
                           @input="recalculate()"
                           class="w-24 border rounded p-1 text-right">
                </div>

                <p>CGST (2.5%): <span x-text="cgst.toFixed(2)"></span></p>
                <p>SGST (2.5%): <span x-text="sgst.toFixed(2)"></span></p>

                <div class="flex justify-end gap-2">
                    <span>Round Off:</span>
                    <input type="number" step="0.01"
                           x-model.number="roundOff"
                           @input="applyRoundOff()"
                           class="w-24 border rounded p-1 text-right">
                </div>

                <p class="font-bold text-lg">
                    Total: <span x-text="total.toFixed(2)"></span>
                </p>

                <button class="mt-3 px-6 py-2 bg-indigo-600 text-white rounded-lg">
                    Save Order
                </button>
            </div>
        </div>
    </form>
</div>


@endsection


@push('scripts')
    

<!-- ================= ALPINE ================= -->


    <script>
        window.pageXData = {
            page: 'createOrder',
        };
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

        subtotal: 0,
        orderDiscount: 0,
        cgst: 0,
        sgst: 0,
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
                    base_unit: product.base_unit,
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
            this.cgst = taxable * 0.025;
            this.sgst = taxable * 0.025;

            let rawTotal = taxable + this.cgst + this.sgst;
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

