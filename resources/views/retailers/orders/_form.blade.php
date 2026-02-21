    @if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
     </div>
    @endif

<div x-data="posOrder()" class="flex flex-col lg:grid lg:grid-cols-12 gap-4">

    @php
    $isEdit = isset($order);
    @endphp

    {{-- ================= MOBILE PRODUCT BUTTON ================= --}}
    <div class="lg:hidden mb-4">
        <button @click="showProductPopup = true"
                type="button"
                class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg font-medium">
            📦 Browse Products
        </button>


        {{-- ================= MOBILE PRODUCT POPUP ================= --}}
        @include('retailers.orders.partials.product-popup')

    </div>

    <div class="lg:col-span-12 lg:grid lg:grid-cols-12 gap-4">

        {{-- ================= DESKTOP PRODUCT LIST ================= --}}
        @include('retailers.orders.partials.product-list')

        {{-- ================= RIGHT POS PANEL ================= --}}
        <div class="lg:col-span-8">
            <form method="POST" action="{{ $action }}" class="bg-white rounded-xl p-4 border">
                @csrf
                @if($method === 'PUT') @method('PUT') @endif

                {{-- ================= ORDER META ================= --}}
                <div class="grid grid-cols-12 gap-3 mb-3">
                    <div class="col-span-12 lg:col-span-6 space-y-3">



                        <!-- Retailer -->
                        <div>
                            <label class="block font-medium mb-1">Retailers</label>
                            @php
                                $selectedRetailer = old('retailer_id', $order->retailer_id ?? '');
                            @endphp

                            <select name="retailer_id"
                                x-model="selectedRetailerId"
                                x-init="
                                    selectedRetailerId = @js($selectedRetailer);
                                    fillAddress();
                                    $nextTick(() => {
                                        if (!window.TomSelect || $el.tomselect) return;
                                        const tomSelect = new TomSelect($el, {
                                            create: false,
                                            allowEmptyOption: true,
                                            placeholder: 'Select Retailer',
                                        });
                                        if (selectedRetailerId) {
                                            tomSelect.setValue(String(selectedRetailerId), true);
                                        }
                                    });
                                "
                                @change="fillAddress()"
                                class="w-full border rounded-lg p-2 text-sm" 
                                required>

                                <option value="">-- Select Retailer --</option>

                                @foreach($retailers as $d)
                                <option value="{{ $d->id }}" @selected((string) $selectedRetailer === (string) $d->id)>
                                    {{ $d->retailer_name }}
                                </option>
                                @endforeach
                            </select>

                            {{-- IMPORTANT: disabled fields are NOT submitted --}}
                            {{-- @if(auth('sales')->check())
                                <input type="hidden"
                                    name="sales_persons_id"
                                    value="{{ auth('distributor')->id() }}">
                            @endif --}}
                        </div>                        

                        {{-- Force distributor for distributor guard --}}
                        @if(auth('distributor')->check())
                            <input type="hidden" name="distributor_id"
                                   value="{{ auth('distributor')->id() }}">
                        @endif

                        <input type="text"
                               name="order_number"
                               value="{{ old('order_number', $order->order_number ?? '') }}"
                               placeholder="Order Number (optional)"
                               class="w-full border rounded-lg p-2 text-sm">

                        <!-- Order Date -->
                        <div class="relative">
                            <input type="date"
                                name="order_date"
                                value="{{ old('order_date', isset($order) ? $order->order_date : now()->toDateString()) }}"
                                class="w-full border rounded-lg p-2 pr-10 text-sm">

                            <button type="button"
                                    onclick="this.previousElementSibling.showPicker && this.previousElementSibling.showPicker()"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                                📅
                            </button>
                        </div>

                    </div>

                    <div class="col-span-12 lg:col-span-6">
                        <textarea name="billing_address"
                                  x-model="billingAddress"
                                  readonly
                                  rows="5"
                                  class="w-full h-full border rounded-lg p-2 text-sm resize-none bg-gray-50"></textarea>
                    </div>
                </div>

                {{-- ================= POS TABLE ================= --}}
                @include('retailers.orders.partials.pos-table')

                {{-- ================= TOTALS ================= --}}
                @include('retailers.orders.partials.totals')

            </form>
        </div>
    </div>
</div>
