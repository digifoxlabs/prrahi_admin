@extends('admin.admin-layout')

@section('page-content')
<div x-data="posOrder()" class="flex flex-col lg:grid lg:grid-cols-12 gap-4">

    <!-- ================= MOBILE: PRODUCT SELECTION BUTTON ================= -->
    <div class="lg:hidden mb-4">
        <button @click="showProductPopup = true"
                class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
            📦 Browse Products
        </button>
    </div>

    <!-- ================= MOBILE: PRODUCT POPUP ================= -->
    <div x-show="showProductPopup"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 lg:hidden"
         style="display: none;">
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="showProductPopup = false"></div>
        
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-2xl h-[85vh] overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="flex justify-between items-center p-4 border-b">
                    <h2 class="text-lg font-semibold">Select Products</h2>
                    <button @click="showProductPopup = false"
                            class="text-gray-500 hover:text-gray-700 text-xl">
                        ✕
                    </button>
                </div>
                
                <!-- Search -->
                <div class="p-4 border-b">
                    <div class="relative">
                        <input type="text"
                               x-model="search"
                               placeholder="Search products..."
                               class="w-full border rounded-lg p-3 pr-10 text-sm">
                        <button x-show="search.length"
                                @click="search=''"
                                type="button"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-700">
                            ✕
                        </button>
                    </div>
                </div>
                
                <!-- Products List -->
                <div class="flex-1 overflow-y-auto p-4">
                    <template x-for="product in filteredProducts()" :key="product.id">
                        <div class="mb-3">

                            <!-- SIMPLE -->
                            <template x-if="product.type === 'simple'">
                                <button @click="addProduct(product); showProductPopup = false;"
                                    class="w-full flex items-center gap-3 p-3 border rounded-lg hover:bg-indigo-50 transition-colors">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center font-bold text-lg"
                                         x-text="product.name.charAt(0).toUpperCase()">
                                    </div>
                                    <div class="text-left flex-1">
                                        <p class="font-semibold text-sm" x-text="product.name"></p>
                                        <p class="text-xs text-gray-600" x-text="product.category?.name || 'No Category'"></p>
                                        <p class="text-xs text-gray-500 mt-1" x-text="product.code"></p>
                                    </div>
                                </button>
                            </template>

                            <!-- VARIABLE -->
                            <template x-if="product.type === 'variable'">
                                <div x-data="{ showVariants: false }" class="border rounded-lg overflow-hidden">
                                    <!-- Variable Product Header -->
                                    <div class="bg-gray-50 p-3 cursor-pointer hover:bg-gray-100 transition-colors"
                                         @click="showVariants = !showVariants">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center font-bold"
                                                     x-text="product.name.charAt(0).toUpperCase()">
                                                </div>
                                                <div class="text-left">
                                                    <p class="font-semibold text-sm" x-text="product.name"></p>
                                                    <p class="text-xs text-gray-600" x-text="product.category?.name || 'No Category'"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                    <span x-text="product.variants?.length || 0"></span> variants
                                                </span>
                                                <svg class="w-4 h-4 transition-transform" 
                                                     :class="{ 'rotate-180': showVariants }" 
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Variants List (Collapsible) -->
                                    <div x-show="showVariants" x-collapse class="bg-white border-t">
                                        <div class="p-3 space-y-2">
                                            <template x-for="variant in product.variants" :key="variant.id">
                                                <button @click="addProduct(variant); showProductPopup = false;"
                                                    class="w-full text-left px-3 py-2 rounded hover:bg-indigo-50 transition-colors text-sm border flex items-center justify-between">
                                                    <div>
                                                        <span class="text-gray-700">
                                                            <span x-text="variant.attributes?.fragrance || 'Variant'"></span>
                                                            <span x-show="variant.attributes?.size">
                                                                (<span x-text="variant.attributes.size"></span>)
                                                            </span>
                                                        </span>
                                                        <span class="text-xs text-gray-500 block mt-1" x-text="variant.code"></span>
                                                    </div>
                                                    <span class="text-xs text-green-600 font-medium">
                                                        ₹<span x-text="Number(variant.ptd_per_dozen || 0).toFixed(2)"></span>
                                                    </span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-12 lg:grid lg:grid-cols-12 gap-4">
        <!-- ================= DESKTOP: PRODUCT SELECTION ================= -->
        <div class="hidden lg:block lg:col-span-4 bg-white rounded-xl p-4 h-[85vh] overflow-y-auto border">
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

            <div class="space-y-3">
                <template x-for="product in filteredProducts()" :key="product.id">
                    <div class="mb-3">

                        <!-- SIMPLE -->
                        <template x-if="product.type === 'simple'">
                            <button @click="addProduct(product)"
                                class="w-full flex items-center gap-3 p-3 border rounded-lg hover:bg-indigo-50 transition-colors group">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center font-bold text-lg group-hover:bg-indigo-200 transition-colors"
                                     x-text="product.name.charAt(0).toUpperCase()">
                                </div>
                                <div class="text-left flex-1">
                                    <p class="font-semibold text-sm" x-text="product.name"></p>
                                    <p class="text-xs text-gray-600" x-text="product.category?.name || 'No Category'"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="product.code"></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-medium text-green-600">
                                        ₹<span x-text="Number(product.ptd_per_dozen || 0).toFixed(2)"></span>
                                    </span>
                                </div>
                            </button>
                        </template>

                        <!-- VARIABLE -->
                        <template x-if="product.type === 'variable'">
                            <div x-data="{ showVariants: false }" class="border rounded-lg overflow-hidden">
                                <!-- Variable Product Header -->
                                <div class="bg-gray-50 p-3 cursor-pointer hover:bg-gray-100 transition-colors"
                                     @click="showVariants = !showVariants">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center font-bold"
                                                 x-text="product.name.charAt(0).toUpperCase()">
                                            </div>
                                            <div class="text-left">
                                                <p class="font-semibold text-sm" x-text="product.name"></p>
                                                <p class="text-xs text-gray-600" x-text="product.category?.name || 'No Category'"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                <span x-text="product.variants?.length || 0"></span> variants
                                            </span>
                                            <svg class="w-4 h-4 transition-transform" 
                                                 :class="{ 'rotate-180': showVariants }" 
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Variants List (Collapsible) -->
                                <div x-show="showVariants" x-collapse class="bg-white border-t">
                                    <div class="p-3 space-y-2">
                                        <template x-for="variant in product.variants" :key="variant.id">
                                            <button @click="addProduct(variant)"
                                                class="w-full text-left px-3 py-2 rounded hover:bg-indigo-50 transition-colors text-sm border flex items-center justify-between">
                                                <div>
                                                    <span class="text-gray-700">
                                                        <span x-text="variant.attributes?.fragrance || 'Variant'"></span>
                                                        <span x-show="variant.attributes?.size">
                                                            (<span x-text="variant.attributes.size"></span>)
                                                        </span>
                                                    </span>
                                                    <span class="text-xs text-gray-500 block mt-1" x-text="variant.code"></span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs font-medium text-green-600">
                                                        ₹<span x-text="Number(variant.ptd_per_dozen || 0).toFixed(2)"></span>
                                                    </span>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                </template>
            </div>
        </div>

        <!-- ================= RIGHT: POS ================= -->
        <div class="lg:col-span-8">
            <form method="POST"
                  action="{{ route('admin.orders.store') }}"
                  class="bg-white rounded-xl p-4 border">
                @csrf

                <div class="grid grid-cols-12 gap-3 mb-3">
                    <!-- LEFT: Order Meta -->
                    <div class="col-span-12 lg:col-span-6 space-y-3">

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
                    <div class="col-span-12 lg:col-span-6">
                        <textarea name="billing_address"
                                  x-model="billingAddress"
                                  rows="5"
                                  placeholder="Billing Address"
                                  class="w-full h-full border rounded-lg p-2 text-sm resize-none"></textarea>
                    </div>
                </div>

                <!-- ================= POS TABLE ================= -->
                <div class="overflow-x-auto">
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
                                    <input type="number" min="1"
                                           x-model.number="item.qty"
                                           @input="recalculate()"
                                           class="w-16 border rounded p-1 text-sm">
                                </td>
                                <td class="p-2" x-text="item.rate.toFixed(2)"></td>
                                <td class="p-2" x-text="item.base_unit"></td>
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
                                <input type="hidden" :name="`items[${index}][discount_percent]`" :value="item.discount">
                                <input type="hidden" :name="`items[${index}][amount]`" :value="item.amount">
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>

                <!-- ================= TOTALS ================= -->
                <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-2 text-sm">
                    <!-- Mobile: Add Product Button -->
                    <div class="lg:hidden mb-4">
                        <button @click="showProductPopup = true"
                                type="button"
                                class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                            📦 Add More Products
                        </button>
                    </div>
                    
                    <div class="lg:col-start-2">
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

                            <p class="font-bold text-lg mt-2">
                                Total: <span x-text="total.toFixed(2)"></span>
                            </p>


                            <!-- ================= HIDDEN TOTAL FIELDS ================= -->
                            <input type="hidden" name="subtotal" :value="subtotal">
                            <input type="hidden" name="discount_amount" :value="orderDiscount">
                            <input type="hidden" name="cgst" :value="cgst">
                            <input type="hidden" name="sgst" :value="sgst">
                            <input type="hidden" name="round_off" :value="roundOff">
                            <input type="hidden" name="total_amount" :value="total">

                            <button class="mt-3 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                Save Order
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
            showProductPopup: false,

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