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
        @include('orders.partials.product-popup')

    </div>

    <div class="lg:col-span-12 lg:grid lg:grid-cols-12 gap-4">

        {{-- ================= DESKTOP PRODUCT LIST ================= --}}
        @include('orders.partials.product-list')

        {{-- ================= RIGHT POS PANEL ================= --}}
        <div class="lg:col-span-8">
            <form method="POST" action="{{ $action }}" class="bg-white rounded-xl p-4 border">
                @csrf
                @if($method === 'PUT') @method('PUT') @endif

                {{-- ================= ORDER META ================= --}}
                <div class="grid grid-cols-12 gap-3 mb-3">
                    <div class="col-span-12 lg:col-span-6 space-y-3">

                        {{-- Distributor --}}
                        {{-- <select name="distributor_id"
                                x-model="selectedDistributorId"
                                @change="fillAddress()"
                                required
                                class="w-full border rounded-lg p-2 text-sm"
                                @if(auth('distributor')->check()) disabled @endif>

                            <option value="">Select Distributor</option>

                            @foreach($distributors as $d)
                                <option value="{{ $d->id }}" >{{ $d->firm_name }}</option>
                            @endforeach
                        </select> --}}

                        <!-- Distributor -->
                        <div    x-data
                                x-init="
                                    selectedDistributorId = '{{ old('distributor_id', $order->distributor_id ?? auth('distributor')->id() ?? '') }}';
                                    fillAddress();
                                ">
                            <label class="block font-medium mb-1">Distributor</label>

                            <select name="distributor_id" x-model="selectedDistributorId" @change="fillAddress()"
                                class="w-full border rounded-lg p-2 text-sm" @if(auth('distributor')->check()) disabled
                                @endif
                                required>

                                <option value="">Select Distributor</option>

                                @foreach($distributors as $d)
                                <option value="{{ $d->id }}">
                                    {{ $d->firm_name }}
                                </option>
                                @endforeach
                            </select>

                            {{-- IMPORTANT: disabled fields are NOT submitted --}}
                            @if(auth('distributor')->check())
                                <input type="hidden"
                                    name="distributor_id"
                                    value="{{ auth('distributor')->id() }}">
                            @endif
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
                                  rows="5"
                                  class="w-full h-full border rounded-lg p-2 text-sm resize-none"></textarea>
                    </div>
                </div>

                {{-- ================= POS TABLE ================= --}}
                @include('orders.partials.pos-table')

                {{-- ================= TOTALS ================= --}}
                @include('orders.partials.totals')

            </form>
        </div>
    </div>
</div>
