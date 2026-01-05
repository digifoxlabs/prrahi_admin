@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
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

@php
$selected = old('distributor_id') ?? ($order->distributor_id ?? null);
@endphp


<!-- Order Header Section -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6 overflow-hidden">
    <!-- Section Header -->
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
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

              <div class="lg:col-span-1 space-y-4"></div>

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
    $.fn.select2.amd.require(['select2/data/array', 'select2/utils'], function (ArrayData, Utils) {
        function CustomData ($element, options) {
            CustomData.__super__.constructor.call(this, $element, options);
        }

        Utils.Extend(CustomData, ArrayData);

        CustomData.prototype.matches = function (params, data) {
            if ($.trim(params.term) === '') return data;

            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) return data;

            const town = $(data.element).data('town')?.toLowerCase() || '';
            const district = $(data.element).data('district')?.toLowerCase() || '';
            const state = $(data.element).data('state')?.toLowerCase() || '';

            const term = params.term.toLowerCase();
            if (town.includes(term) || district.includes(term) || state.includes(term)) return data;

            return null;
        };

        $('#distributor_id').select2({
            placeholder: 'Select Distributor',
            allowClear: true,
            width: '100%',
            dataAdapter: CustomData,
            dropdownParent: $('#distributor_id').parent()
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
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Products</h3>
        
        <!-- Product Dropdown -->
        <div class="w-96">
            <select id="product-selector" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm select2">
                <option value="">-- Add a product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        data-name="{{ $product->type === 'variant' ? ($product->parent->category->name ?? '') . ' - ' . collect($product->attributes)->implode(', ') : $product->name }}"
                        data-price="{{ $product->ptd_per_dozen }}"
                        data-dozen-case="{{ $product->dozen_per_case ?? 0 }}">            
                        {{ $product->type === 'variant' ? ($product->parent->category->name ?? '') . ' - ' . collect($product->attributes)->implode(', ') : $product->name }}
                    </option>
                @endforeach
            </select>
        </div>


    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PTD/Dozen</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dozen/case</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody id="product-rows" class="bg-white divide-y divide-gray-200">
                @if(old('product_ids'))
                    @foreach(old('product_ids') as $i => $pid)
                        @include('admin.orders._product_row_template', [
                            'product' => $products->firstWhere('id', $pid),
                            'qty' => old('quantities')[$i],
                            'index' => $i
                        ])
                    @endforeach
                @elseif(isset($order))
                    @foreach($order->items as $i => $item)
                        @include('admin.orders._product_row', [
                            'product' => $item->product,
                            'qty' => $item->quantity,
                            'price' => $item->rate,
                            'dozen_case' => $item->dozen_case,
                            'total' => $item->total,
                        ])
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Order Summary -->
    <div class="mt-8 border-t border-gray-200 pt-6">
        <div class="space-y-3 max-w-md ml-auto">
            <!-- Subtotal -->
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500">Subtotal:</span>
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 mr-1">₹</span>
                    <input type="text" readonly id="subtotal-amount" name="subtotal"
                        class="border-0 text-right bg-transparent text-gray-900 font-medium w-32"
                        value="{{ old('subtotal', isset($order) ? $order->subtotal : 0) }}">
                </div>
            </div>
            
            <!-- CGST -->
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500">CGST ({{ config('app.cgst_percent') }}%):</span>
                <input type="hidden" id="cgst-rate" value="{{ config('app.cgst_percent') }}">
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 mr-1">₹</span>
                    <input type="text" readonly id="cgst-amount" 
                        class="border-0 text-right bg-transparent text-gray-900 font-medium w-32"
                        value="{{ old('cgst', isset($order) ? $order->cgst : 0) }}">
                </div>
            </div>
            
            <!-- SGST -->
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-500">SGST ({{ config('app.sgst_percent') }}%):</span>
                <input type="hidden" id="sgst-rate" value="{{ config('app.sgst_percent') }}">
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 mr-1">₹</span>
                    <input type="text" readonly id="sgst-amount" 
                        class="border-0 text-right bg-transparent text-gray-900 font-medium w-32"
                        value="{{ old('cgst', isset($order) ? $order->sgst : 0) }}">
                </div>
            </div>
            
            <!-- Discount -->
            <div class="flex justify-between items-center pt-2">
                <span class="text-sm font-medium text-gray-500">Discount:</span>
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 mr-1">₹</span>
                    <input type="number" min="0" step="0.01" name="discount" id="discount-amount"
                        value="{{ old('discount', $order->discount ?? 0) }}"
                        class="border border-gray-300 rounded text-right px-2 py-1 text-sm w-32 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            
            <!-- Total -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                <span class="text-base font-semibold text-gray-700">Total Amount:</span>
                <div class="flex items-center">
                    <span class="text-sm text-gray-500 mr-1">₹</span>
                    <input type="text" readonly id="total-amount" name="total_amount"
                        value="{{ old('total_amount', $order->total_amount ?? 0) }}"
                        class="border-0 text-right bg-transparent text-gray-900 font-semibold text-lg w-32">
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="mt-8 flex justify-end">
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Save Order
        </button>
    </div> --}}

    <div class="mt-6 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Cancel
        </a>
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Save Order
        </button>
    </div>
</div>


</div>

<!-- Product Row Template -->
@include('admin.orders._product_row_template')

<script>
$(document).ready(function() {
    $('#product-selector').select2({
        placeholder: "-- Add a product --",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#product-selector').parent()
    });
});

$('#product-selector').on('select2:select', function (e) {
    const option = e.params.data.element;
    if (!option.value) return;

    const id = option.value;
    const name = option.dataset.name;
    const price = parseFloat(option.dataset.price);
    const dozen_case = parseFloat(option.dataset.dozenCase || 0);

    // Check if product row already exists
    const existingRow = [...document.querySelectorAll('#product-rows tr')].find(row => {
        return row.querySelector('.product-id')?.value === id;
    });

    if (existingRow) {
        const qtyInput = existingRow.querySelector('.qty-input');
        const newQty = parseFloat(qtyInput.value || 0) + 1;
        qtyInput.value = newQty;

        const total = newQty * price * dozen_case;
        existingRow.querySelector('.row-total').value = total.toFixed(2);
    } else {
        const index = document.querySelectorAll('#product-rows tr').length;
        let row = document.getElementById('product-row-template').innerHTML;

        row = row.replace(/__INDEX__/g, index)
                 .replace(/__ID__/g, id)
                 .replace(/__NAME__/g, name)
                 .replace(/__PRICE__/g, price.toFixed(2))
                 .replace(/__DOZEN_CASE__/g, dozen_case);

        document.getElementById('product-rows').insertAdjacentHTML('beforeend', row);

        // Calculate total for new row
        const newRow = document.querySelectorAll('#product-rows tr')[index];
        const qty = parseFloat(newRow.querySelector('.qty-input')?.value || 0);
        const total = qty * price * dozen_case;
        newRow.querySelector('.row-total').value = total.toFixed(2);
    }

    calculateTotal();
    $('#product-selector').val(null).trigger('change');
});

function calculateTotal() {
    let total = 0;
    console.log("🧮 Starting total calculation...");

    document.querySelectorAll('tbody tr').forEach((row, index) => {
        const rowTotalInput = row.querySelector('.row-total');
        const priceInput = row.querySelector('input[name="prices[]"]');
        const price = parseFloat(priceInput?.value || 0);
        const dozenInput = row.querySelector('input[name="dozen_cases[]"]');
        const dozen = parseFloat(dozenInput?.value || 0);
        const qtyInput = row.querySelector('input[name="quantities[]"]');
        const qty = parseFloat(qtyInput?.value || 0);
        const subtotal = qty * price * dozen;
        total += subtotal;

        if (rowTotalInput) {
            rowTotalInput.value = subtotal.toFixed(2);
        }

        console.log(`➡️ Row ${index + 1}: qty=${qty}, price=${price}, dozen=${dozen}, subtotal=${subtotal}`);
    });

    document.getElementById('subtotal-amount').value = total.toFixed(2);
    const cgstRate = parseFloat(document.getElementById('cgst-rate').value || 0);
    const sgstRate = parseFloat(document.getElementById('sgst-rate').value || 0);
    const discount = parseFloat(document.getElementById('discount-amount').value || 0);

    console.log(`🧾 CGST Rate: ${cgstRate}%, SGST Rate: ${sgstRate}%, Discount: ${discount}`);

    const cgstAmount = total * cgstRate / 100;
    const sgstAmount = total * sgstRate / 100;

    console.log(`💸 CGST Amount: ${cgstAmount.toFixed(2)}, SGST Amount: ${sgstAmount.toFixed(2)}`);

    const finalAmount = total + cgstAmount + sgstAmount - discount;

    console.log(`🧮 Final Total: ${finalAmount.toFixed(2)}`);

    document.getElementById('cgst-amount').value = cgstAmount.toFixed(2);
    document.getElementById('sgst-amount').value = sgstAmount.toFixed(2);
    document.getElementById('total-amount').value = finalAmount.toFixed(2);
}

// Recalculate on discount input
document.getElementById('discount-amount').addEventListener('input', calculateTotal);

document.addEventListener('input', function (e) {
    if (e.target.classList.contains('qty-input')) {
        console.log('👀 Quantity input changed');

        const row = e.target.closest('tr');
        if (!row) {
            console.error('❌ Could not find the parent row');
            return;
        }

        const qty = parseFloat(e.target.value || 0);
        const priceText = row.querySelector('input[name="prices[]"]')?.value ?? '0';
        const dozenText = row.querySelector('input[name="dozen_cases[]"]')?.value ?? '0';

        console.log(`🔢 Raw Inputs -> qty: ${qty}, priceText: "${priceText}", dozenText: "${dozenText}"`);

        const price = parseFloat(priceText) || 0;
        const dozen_case = parseFloat(dozenText) || 0;

        console.log(`✅ Parsed Inputs -> qty: ${qty}, price: ${price}, dozen_case: ${dozen_case}`);

        const total = qty * price * dozen_case;
        console.log(`💰 Row total calculated: ${total.toFixed(2)}`);

        const rowTotalInput = row.querySelector('.row-total');
        
        if (rowTotalInput) {
            rowTotalInput.value = total.toFixed(2);
        } else {
            console.warn('⚠️ .row-total input not found in row');
        }

        // Recalculate total
        calculateTotal();
    }
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
        calculateTotal();
    }
});
</script>

