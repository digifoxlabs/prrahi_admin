@extends('admin.admin-layout')

@section('page-content')
<div x-data="posOrder()" class="flex flex-col lg:grid lg:grid-cols-12 gap-4">

    <!-- MOBILE PRODUCT BUTTON -->
    <div class="lg:hidden mb-4">
        <button @click="showProductPopup=true"
                type="button"
                class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg">
            📦 Browse Products
        </button>
    </div>

    <!-- DESKTOP PRODUCT LIST -->
    <div class="hidden lg:block lg:col-span-4 bg-white rounded-xl p-4 h-[85vh] overflow-y-auto border">
        <input type="text"
               x-model="search"
               placeholder="Search products..."
               class="w-full border rounded-lg p-2 mb-4 text-sm">

        <template x-for="product in filteredProducts()" :key="product.id">
            <div class="mb-3">

                <!-- SIMPLE -->
                <template x-if="product.type === 'simple'">
                    <button @click="addProduct(product)"
                            class="w-full flex gap-3 p-3 border rounded-lg hover:bg-indigo-50">
                        <div class="w-10 h-10 bg-indigo-100 rounded flex items-center justify-center font-bold"
                             x-text="product.name.charAt(0).toUpperCase()"></div>
                        <div class="flex-1 text-left">
                            <p class="font-semibold text-sm" x-text="product.name"></p>
                            <p class="text-xs text-gray-500" x-text="product.code"></p>
                        </div>
                    </button>
                </template>

                <!-- VARIABLE -->
                <template x-if="product.type === 'variable'">
                    <div class="border rounded-lg bg-gray-50 p-2">
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

    <!-- POS -->
    <div class="lg:col-span-8">
        <form method="POST"
              action="{{ route('admin.orders.update', $order) }}"
              class="bg-white rounded-xl p-4 border">
            @csrf
            @method('PUT')

            <!-- ORDER META -->
            <div class="grid grid-cols-12 gap-3 mb-3">
                <div class="col-span-12 lg:col-span-6 space-y-3">

                    <select name="distributor_id"
                            x-model="selectedDistributorId"
                            class="w-full border rounded-lg p-2 text-sm">
                        @foreach($distributors as $d)
                            <option value="{{ $d->id }}">{{ $d->firm_name }}</option>
                        @endforeach
                    </select>

                    <input type="text"
                           name="order_number"
                           value="{{ $order->order_number }}"
                           class="w-full border rounded-lg p-2 text-sm">

                    <input type="date"
                           name="order_date"
                           value="{{ $order->order_date }}"
                           class="w-full border rounded-lg p-2 text-sm">
                </div>

                <div class="col-span-12 lg:col-span-6">
                    <textarea name="billing_address"
                              x-model="billingAddress"
                              rows="5"
                              class="w-full h-full border rounded-lg p-2 text-sm resize-none"></textarea>
                </div>
            </div>

            <!-- TABLE -->
            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">Product</th>
                    <th class="p-2">Code</th>
                    <th class="p-2">Qty</th>
                    <th class="p-2">Rate</th>
                    <th class="p-2">Per</th>
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
                            <input type="number"
                                   x-model.number="item.qty"
                                   @input="recalculate()"
                                   class="w-16 border rounded p-1 text-sm">
                        </td>
                        <td class="p-2" x-text="item.rate.toFixed(2)"></td>
                        <td class="p-2" x-text="item.base_unit"></td>
                        <td class="p-2" x-text="item.discount"></td>
                        <td class="p-2 text-right" x-text="item.amount.toFixed(2)"></td>
                        <td class="p-2 text-center">
                            <button type="button" @click="removeItem(index)" class="text-red-500">✕</button>
                        </td>

                        <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                        <input type="hidden" :name="`items[${index}][quantity]`" :value="item.qty">
                        <input type="hidden" :name="`items[${index}][rate]`" :value="item.rate">
                        <input type="hidden" :name="`items[${index}][base_unit]`" :value="item.base_unit">
                         <input type="hidden" :name="`items[${index}][discount_percent]`" :value="item.discount">
                        <input type="hidden" :name="`items[${index}][amount]`" :value="item.amount">
                    </tr>
                </template>
                </tbody>
            </table>

            <!-- TOTALS -->
            <div class="mt-4 text-right space-y-1">
                <p>Sub Total: <span x-text="subtotal.toFixed(2)"></span></p>

                <div class="flex justify-end gap-2">
                    <span>Discount:</span>
                    <input type="number"
                           x-model.number="orderDiscount"
                           @input="recalculate()"
                           class="w-24 border rounded p-1 text-right">
                </div>

                <p>CGST: <span x-text="cgst.toFixed(2)"></span></p>
                <p>SGST: <span x-text="sgst.toFixed(2)"></span></p>

                <div class="flex justify-end gap-2">
                    <span>Round Off:</span>
                    <input type="number"
                           step="0.01"
                           x-model.number="roundOff"
                           @input="applyRoundOff()"
                           class="w-24 border rounded p-1 text-right">
                </div>

                <p class="font-bold text-lg">
                    Total: <span x-text="total.toFixed(2)"></span>
                </p>

                <input type="hidden" name="subtotal" :value="subtotal">
                <input type="hidden" name="discount_amount" :value="orderDiscount">
                <input type="hidden" name="cgst" :value="cgst">
                <input type="hidden" name="sgst" :value="sgst">
                <input type="hidden" name="round_off" :value="roundOff">
                <input type="hidden" name="total_amount" :value="total">


                <div class="mt-4 flex justify-end gap-3">
                    <!-- Back -->
                    <a href="{{ url()->previous() }}"
                    class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                        Back
                    </a>

                    <!-- Update -->
                    <button class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Update Order
                    </button>
                </div>



            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')

    <script>
        window.pageXData = {
            page: 'editeOrder',
        };
    </script>

<script>
function posOrder(){
    return {
        products: @json($products),
        distributors: @json($distributors),

        selectedDistributorId: {{ $order->distributor_id }},
        billingAddress: @json($order->billing_address),
        search: '',

        cart: @json($cartItems),

        subtotal: {{ $order->subtotal }},
        orderDiscount: {{ $order->discount }},
        cgst: {{ $order->cgst }},
        sgst: {{ $order->sgst }},
        roundOff: {{ $order->round_off }},
        total: {{ $order->total_amount }},

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
