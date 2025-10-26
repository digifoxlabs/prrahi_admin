@if ($errors->any())
    <div
        class="mb-6 p-4 
bg-red-50 border-l-4 border-red-500 text-red-700 rounded 
dark:bg-red-900/30 dark:border-red-600 dark:text-red-300">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd">
                </path>
            </svg>
            <strong class="font-medium">Validation Errors</strong>
        </div>
        <ul class="mt-2 pl-5 list-disc text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


{{-- 
<style>
    /* Ensure select2 respects dark mode */
    .select2-container--default .select2-selection--single {
        background-color: #1f2937;
        /* dark:bg-gray-800 */
        border-color: #374151;
        /* dark:border-gray-700 */
        color: #f9fafb;
        /* text-white */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f9fafb;
        /* White text */
    }

    .select2-container--default .select2-dropdown {
        background-color: #111827;
        /* dark:bg-gray-900 */
        border-color: #374151;
        color: #f9fafb;
    }

    .select2-container--default .select2-results__option {
        color: #f9fafb;
        /* White text */
        background-color: #111827;
        /* dark dropdown background */
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .select2-container--default .select2-results__option--highlighted {
        background-color: #2563eb;
        /* blue-600 */
        color: #ffffff;
        /* white on hover */
    }
</style> --}}



@php
    $selected = old('distributor_id') ?? ($order->distributor_id ?? null);
@endphp


<!-- Order Header Section -->
{{-- <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6 overflow-hidden">
    <!-- Section Header -->
    <div class="bg-gray-80 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Order Details</h2>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Distributor Selection (Left Side) -->
            <div class="lg:col-span-1 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Distributor <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="distributor_id" id="distributor_id" 
                            class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm select2"
                            required>
                            <option value="">Select Distributor</option>
                            @foreach ($distributors as $distributor)
                                <option value="{{ $distributor->id }}"
                                    data-town="{{ $distributor->town }}"
                                    data-district="{{ $distributor->district }}"
                                    data-state="{{ $distributor->state }}"
                                    @selected(old('distributor_id', $order->distributor_id ?? '') == $distributor->id)>
                                    {{ $distributor->firm_name }} ({{ $distributor->contact_person }}) - {{ $distributor->town }}, {{ $distributor->district }}, {{ $distributor->state }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    @error('distributor_id') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
            </div>

              <div class="space-y-4"></div>

            <!-- Order Meta (Right Side) -->
            <div class="space-y-4">
                <!-- Order Date -->
                <div>
                    <label for="order_date_picker" class="block text-sm font-medium text-gray-700 mb-1">
                        Order Date
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="order_date_picker"
                            name="order_date" 
                            value="{{ old('order_date', isset($order) && $order->created_at ? $order->created_at->format('Y-m-d') : now()->format('Y-m-d')) }}" 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Select date" />
                    </div>
                </div>

                <!-- Order Number -->
                <div>
                    <label for="order_number" class="block text-sm font-medium text-gray-700 mb-1">
                        Order Number
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="order_number" 
                            id="order_number"
                            placeholder="Auto-generate"
                            value="{{ old('order_number', $order->order_number ?? '') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}


<!-- Order Header Section -->
<div
    class="bg-white dark:bg-white/[0.03] rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mb-6 overflow-hidden">
    <!-- Section Header -->
    <div class="bg-gray-50 dark:bg-transparent px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Order Details</h2>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Distributor Selection (Left Side) -->
            <div class="lg:col-span-1 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white/90 mb-1">
                        Distributor <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">



                        <select name="distributor_id" id="distributor_id"
                            class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 
                            dark:border-gray-700 focus:outline-none focus:ring-blue-500 
                            focus:border-blue-500 dark:bg-gray-800 dark:text-gray-700 
                                sm:text-sm rounded-md shadow-sm select2"
                            required>
                            <option value="">Select Distributor</option>
                            @foreach ($distributors as $distributor)
                                <option value="{{ $distributor->id }}" data-town="{{ $distributor->town }}"
                                    data-district="{{ $distributor->district }}" data-state="{{ $distributor->state }}"
                                    @selected(old('distributor_id', $order->distributor_id ?? '') == $distributor->id)>
                                    {{ $distributor->firm_name }} ({{ $distributor->contact_person }}) -
                                    {{ $distributor->town }}, {{ $distributor->district }}, {{ $distributor->state }}
                                </option>
                            @endforeach
                        </select>







                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    @error('distributor_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-4"></div>

            <!-- Order Meta (Right Side) -->
            <div class="space-y-4">
                <!-- Order Date -->
                <div>
                    <label for="order_date_picker"
                        class="block text-sm font-medium text-gray-700 dark:text-white/90 mb-1">
                        Order Date
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="order_date_picker" name="order_date"
                            value="{{ old('order_date', isset($order) && $order->created_at ? $order->created_at->format('Y-m-d') : now()->format('Y-m-d')) }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-transparent dark:text-white/90 sm:text-sm"
                            placeholder="Select date" />
                    </div>
                </div>

                <!-- Order Number -->
                <div>
                    <label for="order_number" class="block text-sm font-medium text-gray-700 dark:text-white/90 mb-1">
                        Order Number
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="order_number" id="order_number" placeholder="Auto-generate"
                            value="{{ old('order_number', $order->order_number ?? '') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-transparent dark:text-white/90 sm:text-sm" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script>
    $(document).ready(function() {
        $('#distributor_id').select2({
            placeholder: 'Select Distributor',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#distributor_id').parent()
        });

        // Optional: Enable distributor search by town/district/state
        $.fn.select2.amd.require(['select2/data/array', 'select2/utils'], function(ArrayData, Utils) {
            function CustomData($element, options) {
                CustomData.__super__.constructor.call(this, $element, options);
            }

            Utils.Extend(CustomData, ArrayData);

            CustomData.prototype.matches = function(params, data) {
                if ($.trim(params.term) === '') return data;

                if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) return data;

                const town = $(data.element).data('town')?.toLowerCase() || '';
                const district = $(data.element).data('district')?.toLowerCase() || '';
                const state = $(data.element).data('state')?.toLowerCase() || '';

                const term = params.term.toLowerCase();
                if (town.includes(term) || district.includes(term) || state.includes(term))
                    return data;

                return null;
            };

            $('#distributor_id').select2({
                placeholder: 'Select Distributor',
                allowClear: true,
                width: '100%',
                dataAdapter: CustomData,
                // dropdownParent: $('#distributor_id').parent()
            });
        });
    });
</script>

<script>
    flatpickr("#order_date_picker", {
        dateFormat: "Y-m-d",
        defaultDate: "{{ old('order_date', isset($order) && $order->created_at ? $order->created_at->format('Y-m-d') : now()->format('Y-m-d')) }}",
    });
</script>














<!-- Product Selection Section -->
<div class="bg-white dark:bg-white/[0.03] p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Products</h3>

        <!-- Add Product Button -->
        <button type="button" onclick="openProductModal()"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Add Product
        </button>
    </div>

    <div class="overflow-x-auto">
        <table id="order-items-body" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
<thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-700 dark:text-white">
    <tr">
        <th scope="col"
            class="px-6 py-3 text-left text-xs font-semibold 
                   text-gray-600 dark:text-gray-200 
                   uppercase tracking-wider">
            Product
        </th>
        <th scope="col"
            class="px-6 py-3 text-left text-xs font-semibold 
                   text-gray-600 dark:text-gray-200 
                   uppercase tracking-wider">
            PTD/Dozen
        </th>
        <th scope="col"
            class="px-6 py-3 text-left text-xs font-semibold 
                   text-gray-600 dark:text-gray-200 
                   uppercase tracking-wider">
            Dozen/Case
        </th>
        <th scope="col"
            class="px-6 py-3 text-left text-xs font-semibold 
                   text-gray-600 dark:text-gray-200 
                   uppercase tracking-wider">
            Free Dozen/Case
        </th>
        <th scope="col"
            class="px-6 py-3 text-left text-xs font-semibold 
                   text-gray-600 dark:text-gray-200 
                   uppercase tracking-wider">
            Qty
        </th>
        <th scope="col"
            class="px-6 py-3 text-left text-xs font-semibold 
                   text-gray-600 dark:text-gray-200 
                   uppercase tracking-wider">
            Total
        </th>
        <th scope="col"
            class="px-6 py-3 text-right text-xs font-semibold 
                   text-gray-600 dark:text-gray-200 
                   uppercase tracking-wider">
            Action
        </th>
    </tr>
</thead>

            <tbody id="product-rows" class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-gray-700">

                @if ($errors->has('stock_error'))
                    <div class="bg-red-100 dark:bg-red-500/20 text-red-800 dark:text-red-400 p-4 rounded mb-4">
                        {!! $errors->first('stock_error') !!}
                    </div>
                @endif

                @if (old('product_ids'))
                    @foreach (old('product_ids') as $i => $pid)
                        @include('admin.orders._product_row_template', [
                            'product' => $products->firstWhere('id', $pid),
                            'qty' => old('quantities')[$i],
                            'index' => $i,
                        ])
                    @endforeach
                @elseif(isset($order))
                    @foreach ($order->items as $i => $item)
                        @include('admin.orders._product_row', [
                            'product' => $item->product,
                            'qty' => $item->quantity,
                            'price' => $item->rate,
                            'dozen_case' => $item->dozen_case ?? 0,
                            'free_dozen_case' => $item->free_dozen_case ?? 0,
                            'total' => $item->total,
                        ])
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Order Summary -->
    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
        <div class="space-y-3 max-w-md ml-auto">
            <!-- Subtotal -->
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-300">Subtotal:</span>
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400 mr-1">₹</span>
                    <input type="text" readonly id="subtotal-amount" name="subtotal"
                        class="border-0 text-right bg-transparent text-gray-900 dark:text-white font-medium w-32"
                        value="{{ old('subtotal', isset($order) ? $order->subtotal : 0) }}">
                </div>
            </div>

            <!-- Discount -->
            <div class="flex justify-between items-center pt-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-300">Discount:</span>
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400 mr-1">₹</span>
                    <input type="number" min="0" step="0.01" name="discount" id="discount-amount"
                        value="{{ old('discount', $order->discount ?? 0) }}"
                        class="border border-gray-300 dark:border-gray-700 dark:bg-transparent dark:text-white rounded text-right px-2 py-1 text-sm w-32 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- CGST -->
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-300">CGST
                    ({{ config('app.cgst_percent') }}%):</span>
                <input type="hidden" id="cgst-rate" value="{{ config('app.cgst_percent') }}">
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400 mr-1">₹</span>
                    <input type="text" readonly id="cgst-amount" name="cgst"
                        class="border-0 text-right bg-transparent text-gray-900 dark:text-white font-medium w-32"
                        value="{{ old('cgst', isset($order) ? $order->cgst : 0) }}">
                </div>
            </div>

            <!-- SGST -->
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-300">SGST
                    ({{ config('app.sgst_percent') }}%):</span>
                <input type="hidden" id="sgst-rate" value="{{ config('app.sgst_percent') }}">
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 dark:text-gray-400 mr-1">₹</span>
                    <input type="text" readonly id="sgst-amount" name="sgst"
                        class="border-0 text-right bg-transparent text-gray-900 dark:text-white font-medium w-32"
                        value="{{ old('sgst', isset($order) ? $order->sgst : 0) }}">
                </div>
            </div>

            <!-- Total -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-base font-semibold text-gray-700 dark:text-white">Total Amount:</span>
                <div class="flex items-center">
                    <span class="text-sm text-gray-500 dark:text-gray-400 mr-1">₹</span>
                    <input type="text" readonly id="total-amount" name="total_amount"
                        value="{{ old('total_amount', $order->total_amount ?? 0) }}"
                        class="border-0 text-right bg-transparent text-gray-900 dark:text-white font-semibold text-lg w-32">
                </div>
            </div>
        </div>
    </div>

    <div
        class="mt-6 bg-white dark:bg-gray-100 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div
            class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-100 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-400 bg-white dark:bg-transparent hover:bg-gray-50 dark:hover:bg-gray-700 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Save Order
            </button>
        </div>
    </div>
</div>







<!-- Product Modal -->
<div id="productModal"
    class="fixed inset-0 flex items-center justify-center bg-gray-400/50 dark:bg-black/70 backdrop-blur-[32px] p-5 overflow-y-auto z-99999 hidden">

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto 
                border border-gray-200 dark:border-gray-700">

        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Add Product</h3>
                <button onclick="closeProductModal()"
                    class="text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-white transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Product Dropdown inside modal -->
            <div class="mb-4">
                <select id="product-selector"
                    class="w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm 
                           focus:border-blue-500 focus:ring-blue-500 text-sm select2 
                           bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                    <option value="">-- Select a product --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}"
                            data-name="{{ $product->type === 'variant' ? ($product->parent->category->name ?? '') . ' - ' . collect($product->attributes)->implode(', ') : $product->name }}"
                            data-price="{{ $product->ptd_per_dozen }}" data-is-free="{{ $freeDozenCase }}"
                            data-free-dozen="{{ $product->free_dozen_per_case ?? 0 }}"
                            data-dozen-case="{{ $product->dozen_per_case ?? 0 }}">
                            {{ $product->type === 'variant' ? ($product->parent->category->name ?? '') . ' - ' . collect($product->attributes)->implode(', ') : $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
            <button type="button" onclick="closeProductModal()"
                class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600
                    bg-gray-100 text-gray-700 
                    dark:bg-gray-700 dark:text-gray-400
                    hover:bg-gray-200 dark:hover:bg-gray-600 dark:hover:text-white
                    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                    shadow-sm transition duration-200 ease-in-out">
                Cancel
            </button>

                <button type="button" onclick="addSelectedProduct()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md 
                           hover:bg-blue-700 dark:hover:bg-blue-500 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    Add Product
                </button>
            </div>
        </div>
    </div>
</div>





<!-- Product Row Template -->
@include('admin.orders._product_row_template')



<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (old('product_ids'))
            const oldProductIds = @json(old('product_ids'));
            const oldPrices = @json(old('prices'));
            const oldDozens = @json(old('dozen_cases'));
            const oldFreeDozens = @json(old('free_dozen_case'));
            const oldQuantities = @json(old('quantities'));
            const oldTotals = @json(old('totals'));

            for (let i = 0; i < oldProductIds.length; i++) {
                const data = {
                    index: i,
                    id: oldProductIds[i],
                    name: window.productNames[oldProductIds[i]] || 'Unknown',
                    price: oldPrices[i],
                    dozen_case: oldDozens[i],
                    free_dozen_case: oldFreeDozens[i],
                    quantity: oldQuantities[i],
                    total: oldTotals[i],
                };
                addProductRow(data);
            }
        @endif
    });

    // Map of productId to name, provided from backend
    // window.productNames = @json($products->pluck('name', 'id'));
    window.productNames = {
        @foreach ($products as $product)
            {{ $product->id }}: @json(
                $product->type === 'variant'
                    ? ($product->parent->category->name ?? '') . ' - ' . collect($product->attributes)->implode(', ')
                    : $product->name),
        @endforeach
    };

    function addProductRow({
        index,
        id,
        name,
        price,
        dozen_case,
        free_dozen_case,
        quantity,
        total
    }) {
        const template = document.getElementById('product-row-template').innerHTML;

        const rowHTML = template
            .replace(/__INDEX__/g, index)
            .replace(/__ID__/g, id)
            .replace(/__NAME__/g, name)
            .replace(/__PRICE__/g, price)
            .replace(/__DOZEN_CASE__/g, dozen_case * quantity)
            .replace(/__FREE_DOZEN_CASE__/g, free_dozen_case * quantity)
            .replace(/__QTY__/g, quantity)
            .replace(/__TOTAL__/g, total);

        document.querySelector('#order-items-body').insertAdjacentHTML('beforeend', rowHTML);
    }
</script>





<script>
    // Product Modal Functions
    function openProductModal() {
        document.getElementById('productModal').classList.remove('hidden');
        // Initialize select2 if not already initialized
        if ($('#product-selector').hasClass('select2-hidden-accessible') === false) {
            $('#product-selector').select2({
                dropdownParent: $('#productModal'),
                placeholder: "-- Select a product --",
                allowClear: true,
                width: '100%'
            });
        }
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.add('hidden');
    }

    function addSelectedProduct() {
        const select = document.getElementById('product-selector');
        const option = select.options[select.selectedIndex];

        if (!option.value) {
            alert('Please select a product first');
            return;
        }

        const id = option.value;
        const name = option.dataset.name;
        const price = parseFloat(option.dataset.price);
        let dozen_case = parseFloat(option.dataset.dozenCase || 0);
        const has_free_qty = parseFloat(option.dataset.isFree || 0);
        let free_dozen_case = parseFloat(option.dataset.freeDozen || 0);

        console.log("IS Free:", has_free_qty);

        if (!has_free_qty)
            free_dozen_case = 0;

        console.log("Free Dozen case:", free_dozen_case);


        // Check if product row already exists
        const existingRow = [...document.querySelectorAll('#product-rows tr')].find(row => {
            return row.querySelector('.product-id')?.value === id;
        });

        if (existingRow) {
            const qtyInput = existingRow.querySelector('.qty-input');
            let newQty = parseFloat(qtyInput.value || 0) + 1;
            qtyInput.value = newQty;

            free_dozen_case = free_dozen_case * newQty;

            dozen_case = dozen_case * newQty;

            // dozen_case = ( dozen_case * newQty ) - free_dozen_case;

            // const total = newQty * price * dozen_case;

            existingRow.querySelector('.dozen-case').textContent = dozen_case.toFixed(0);
            // existingRow.querySelector('.dozen-cases-input').value = dozen_case.toFixed(0);

            existingRow.querySelector('.free-dozen').textContent = free_dozen_case.toFixed(0);
            // existingRow.querySelector('.free-dozen-input').value = free_dozen_case.toFixed(0);
            // existingRow.querySelector('.row-total').value = total.toFixed(2);


        } else {
            const index = document.querySelectorAll('#product-rows tr').length;
            let row = document.getElementById('product-row-template').innerHTML;

            row = row.replace(/__INDEX__/g, index)
                .replace(/__ID__/g, id)
                .replace(/__NAME__/g, name)
                .replace(/__PRICE__/g, price.toFixed(2))
                .replace(/__FREE_DOZEN_CASE__/g, free_dozen_case)
                .replace(/__HAS_FREE_QTY__/g, has_free_qty)
                .replace(/__DOZEN_CASE__/g, dozen_case)
                .replace(/__QTY__/g, 1);

            document.getElementById('product-rows').insertAdjacentHTML('beforeend', row);

            // Calculate total for new row
            const newRow = document.querySelectorAll('#product-rows tr')[index];
            const qty = parseFloat(newRow.querySelector('.qty-input')?.value || 0);
            const total = qty * price * (dozen_case - free_dozen_case);
            newRow.querySelector('.row-total').value = total.toFixed(2);
        }

        calculateTotal();
        closeProductModal();
        $('#product-selector').val(null).trigger('change');
    }

    $(document).ready(function() {
        // Initialize select2 for product selector in modal
        $('#product-selector').select2({
            placeholder: "-- Select a product --",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#productModal')
        });
    });



    function calculateTotal() {


        // console.log("Free dozen case:",freeDozencase); 

        let total = 0;
        console.log("🧮 Starting total calculation...");


        document.querySelectorAll('tbody tr').forEach((row, index) => {

            let freeDozencase = @json($freeDozenCase);

            const rowTotalInput = row.querySelector('.row-total');
            const priceInput = row.querySelector('input[name="prices[]"]');
            const price = parseFloat(priceInput?.value || 0);
            const dozenInput = row.querySelector('input[name="dozen_cases[]"]');
            let dozen = parseFloat(dozenInput?.value || 0);
            const qtyInput = row.querySelector('input[name="quantities[]"]');
            const qty = parseFloat(qtyInput?.value || 0);

            let isFreeQty = row.querySelector('input[name="has_free_qty[]"]')?.value ?? '0';

            if (!isFreeQty)
                freeDozencase = 0;


            freeDozencase = freeDozencase * qty;
            dozen = (dozen * qty) - freeDozencase
            const subtotal = price * dozen;
            total += subtotal;

            if (rowTotalInput) {
                rowTotalInput.value = subtotal.toFixed(2);
            }

            console.log(
                `➡️ Row ${index + 1}: qty=${qty}, price=${price}, dozen=${dozen}, freeDozen=${freeDozencase} subtotal=${subtotal}`
            );
        });

        document.getElementById('subtotal-amount').value = total.toFixed(2);


        const cgstRate = parseFloat(document.getElementById('cgst-rate').value || 0);
        const sgstRate = parseFloat(document.getElementById('sgst-rate').value || 0);
        const discount = parseFloat(document.getElementById('discount-amount').value || 0);

        total = total - discount;

        console.log(`🧾 CGST Rate: ${cgstRate}%, SGST Rate: ${sgstRate}%, Discount: ${discount}`);

        const cgstAmount = total * cgstRate / 100;
        const sgstAmount = total * sgstRate / 100;

        console.log(`💸 CGST Amount: ${cgstAmount.toFixed(2)}, SGST Amount: ${sgstAmount.toFixed(2)}`);

        // const finalAmount = total + cgstAmount + sgstAmount - discount;
        const finalAmount = total + cgstAmount + sgstAmount;

        console.log(`🧮 Final Total: ${finalAmount.toFixed(2)}`);

        document.getElementById('cgst-amount').value = cgstAmount.toFixed(2);
        document.getElementById('sgst-amount').value = sgstAmount.toFixed(2);
        document.getElementById('total-amount').value = finalAmount.toFixed(2);
    }

    // Recalculate on discount input
    document.getElementById('discount-amount').addEventListener('input', calculateTotal);


    document.addEventListener('input', function(e) {


        if (e.target.classList.contains('qty-input')) {

            console.log('👀 Quantity input changed');

            //  let freeDozencase = @json($freeDozenCase);

            const row = e.target.closest('tr');
            if (!row) {
                console.error('❌ Could not find the parent row');
                return;
            }

            // Parse qty, but ignore if input is empty
            const qtyRaw = e.target.value.trim();

            if (qtyRaw === '' || isNaN(qtyRaw)) {
                return; // ✅ stop here instead of setting everything to zero
            }
            const qty = parseFloat(qtyRaw);


            // const qty = parseFloat(e.target.value || 0);
            const priceText = row.querySelector('input[name="prices[]"]')?.value ?? '0';
            let dozenText = row.querySelector('input[name="dozen_cases[]"]')?.value ?? '0';
            let freeDozencase = row.querySelector('input[name="free_dozen_case[]"]')?.value ?? '0';

            dozenText = parseFloat(dozenText * qty);

            freeDozencase = freeDozencase * qty;

            row.querySelector('.free-dozen').textContent = freeDozencase.toFixed(0);
            // row.querySelector('.free-dozen-input').value = freeDozencase.toFixed(0);

            row.querySelector('.dozen-case').textContent = dozenText.toFixed(0);
            //  row.querySelector('.dozen-cases-input').value = dozenText.toFixed(0);         

            // Recalculate total
            calculateTotal();
        }
    });




    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            calculateTotal();
        }
    });
</script>





<style>
/* helper classes for Select2 light/dark styling */
.select2-container-light .select2-selection--single {
  background-color: #fff;
  color: #111827; /* text-gray-900 */
  border-color: #d1d5db; /* border-gray-300 */
}
.select2-container-light .select2-selection__rendered { color: inherit; }
.select2-container-light .select2-selection__arrow b { border-color: #6b7280 transparent transparent; } /* caret color */

.select2-container-dark .select2-selection--single {
  background-color: #0f1724; /* dark background matching dark page */
  color: #e6eef8; /* light text */
  border-color: #374151; /* dark border */
}
.select2-container-dark .select2-selection__rendered { color: inherit; }
.select2-container-dark .select2-selection__arrow b { border-color: #cbd5e1 transparent transparent; }

/* Dropdown menu styles */
.select2-dropdown-light {
  background: #fff;
  color: #111827;
  border-color: #e5e7eb;
}
.select2-dropdown-dark {
  background: #0b1220;
  color: #e6eef8;
  border-color: #374151;
}

/* Make items readable */
.select2-results__option { padding: 8px 12px; }
.select2-results__option[aria-selected="true"] {
  background-color: rgba(59,130,246,0.08); /* blue-500/8 */
}
.select2-container-dark .select2-results__option[aria-selected="true"] {
  background-color: rgba(96,165,250,0.12); /* lighter blue highlight in dark */
}

/* Remove native select arrow (tailwind appearance-none or fallback) */
.select-appearance-none { -webkit-appearance:none; -moz-appearance:none; appearance:none; }

/* ensure the dropdown z-index so it doesn't get clipped inside containers */
.select2-container--open { z-index: 99999; }
</style>

<!-- Select2 JS (if not already included) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Utility: detect if page is currently dark (assuming 'dark' class on <html> or <body>)
  function isDarkMode() {
    return document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
  }

  // Initialize the select with proper classes and options
  function initDistributorSelect() {
    const $sel = $('#distributor_id');

    // Add appearance-none to remove browser arrow
    $sel.addClass('select-appearance-none');

    // If Select2 already initialized, destroy to re-init cleanly
    if ($sel.hasClass('select2-hidden-accessible')) {
      $sel.select2('destroy');
    }

    const containerClass = isDarkMode() ? 'select2-container-dark' : 'select2-container-light';
    const dropdownClass  = isDarkMode() ? 'select2-dropdown-dark'  : 'select2-dropdown-light';

    $sel.select2({
      width: '100%',
      dropdownCssClass: dropdownClass,
      // containerCssClass can be string; use containerClass to style the selection box
      containerCssClass: containerClass,
      placeholder: $sel.attr('placeholder') || 'Select distributor',
      // keep markup intact, do not escape if html is present
      escapeMarkup: function(m) { return m; }
    });

    // Also ensure the generated container inherits width and rounded corners from tailwind
    $sel.data('select2').$container.addClass('w-full rounded-md');
  }

  // Toggle helper class on the containers (fast path without re-init)
  function toggleSelect2Mode(enterDark) {
    // For all select2 containers in page - add/remove classes
    document.querySelectorAll('.select2-container--default').forEach(function (c) {
      if (enterDark) {
        c.classList.remove('select2-container-light');
        c.classList.add('select2-container-dark');
      } else {
        c.classList.remove('select2-container-dark');
        c.classList.add('select2-container-light');
      }
    });

    document.querySelectorAll('.select2-dropdown').forEach(function (d) {
      if (enterDark) {
        d.classList.remove('select2-dropdown-light');
        d.classList.add('select2-dropdown-dark');
      } else {
        d.classList.remove('select2-dropdown-dark');
        d.classList.add('select2-dropdown-light');
      }
    });
  }

  // First init
  initDistributorSelect();

  // Observe html class attribute for 'dark' toggle and switch theme classes live
  const observer = new MutationObserver(function(mutations) {
    for (const m of mutations) {
      if (m.attributeName === 'class') {
        const enterDark = isDarkMode();
        // best-effort: toggle classes on existing Select2 elements
        toggleSelect2Mode(enterDark);
        // If you prefer a full reinit (safe), uncomment this:
        // initDistributorSelect();
      }
    }
  });

  observer.observe(document.documentElement, { attributes: true });

  // Also watch for manual toggles on body (if your theme adds dark to body)
  const bodyObserver = new MutationObserver(function(mutations) {
    for (const m of mutations) {
      if (m.attributeName === 'class') {
        const enterDark = isDarkMode();
        toggleSelect2Mode(enterDark);
      }
    }
  });
  bodyObserver.observe(document.body, { attributes: true });

  // If you dynamically change select options after init, re-run initDistributorSelect() where appropriate.
});
</script>
