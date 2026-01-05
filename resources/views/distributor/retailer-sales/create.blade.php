@extends('distributor.layout')

@section('page-content')
<div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('distributor.retailer-sales.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-2 mb-3">
                ← Back to Retail Sales
            </a>

            <h1 class="text-2xl font-bold text-gray-900">New Retailer Sale</h1>
            <p class="text-sm text-gray-500">Sell products from your available stock to a retailer</p>
        </div>

        {{-- Form --}}
        <form method="POST"
              action="{{ route('distributor.retailer-sales.store') }}"
              class="bg-white rounded-xl shadow border p-6 space-y-8">
            @csrf

            {{-- Retailer + Date --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Retailer --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Retailer</label>
                    <select name="retailer_id" required
                            class="w-full border rounded-lg p-2">
                        <option value="">-- Select Retailer --</option>
                        @foreach($retailers as $r)
                            <option value="{{ $r->id }}" @selected(old('retailer_id') == $r->id)>
                                {{ $r->retailer_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('retailer_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Sale Date</label>
                    <input type="date"
                           name="sale_date"
                           value="{{ old('sale_date', now()->toDateString()) }}"
                           required
                           class="w-full border rounded-lg p-2">
                </div>
            </div>

            {{-- Products --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800">Products</h3>
                    <button type="button"
                            id="addItem"
                            class="text-sm bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700">
                        + Add Product
                    </button>
                </div>

                {{-- Items --}}
                <div id="items" class="space-y-3">

                    {{-- First row --}}
                    <div class="item-row grid grid-cols-1 md:grid-cols-3 gap-3 bg-gray-50 p-3 rounded-lg border">
                        {{-- Product --}}
                        <div>
                            <label class="text-sm text-gray-600">Product</label>
                            <select name="items[0][product_id]"
                                    class="w-full border rounded-lg p-2 product-select"
                                    required>
                                <option value="">-- Select Product --</option>
                                @foreach($stocks as $stock)
                                    <option value="{{ $stock->product->id }}"
                                            data-available="{{ $stock->available_qty }}">
                                        {{ $stock->product->product_name }}
                                        (Avail: {{ $stock->available_qty }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Qty --}}
                        <div>
                            <label class="text-sm text-gray-600">Quantity</label>
                            <input type="number"
                                   name="items[0][qty]"
                                   min="1"
                                   class="w-full border rounded-lg p-2 qty-input"
                                   required>
                            <p class="text-xs text-red-600 mt-1 hidden qty-error"></p>
                        </div>

                        {{-- Remove --}}
                        <div class="flex items-end">
                            <button type="button"
                                    class="remove-item text-red-600 text-sm hidden">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    Save Sale
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Template --}}
<template id="item-template">
    <div class="item-row grid grid-cols-1 md:grid-cols-3 gap-3 bg-gray-50 p-3 rounded-lg border">
        <div>
            <label class="text-sm text-gray-600">Product</label>
            <select class="w-full border rounded-lg p-2 product-select" required></select>
        </div>

        <div>
            <label class="text-sm text-gray-600">Quantity</label>
            <input type="number" min="1"
                   class="w-full border rounded-lg p-2 qty-input" required>
            <p class="text-xs text-red-600 mt-1 hidden qty-error"></p>
        </div>

        <div class="flex items-end">
            <button type="button"
                    class="remove-item text-red-600 text-sm">
                Remove
            </button>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>


    window.pageXData = {
        page: 'retailers-sales',
    };
document.addEventListener('DOMContentLoaded', () => {

    let index = 1;
    const items = document.getElementById('items');
    const template = document.getElementById('item-template');
    const form = document.querySelector('form');

    const stockOptions = `
        <option value="">-- Select Product --</option>
        @foreach($stocks as $stock)
            <option value="{{ $stock->product->id }}"
                    data-available="{{ $stock->available_qty }}">
                {{ $stock->product->product_name }}
                (Avail: {{ $stock->available_qty }})
            </option>
        @endforeach
    `;

    function updateDisabledProducts() {
        const selected = [...document.querySelectorAll('.product-select')]
            .map(s => s.value).filter(Boolean);

        document.querySelectorAll('.product-select').forEach(select => {
            [...select.options].forEach(opt => {
                opt.disabled = selected.includes(opt.value) && opt.value !== select.value;
            });
        });
    }

    function attachLogic(row) {
        const select = row.querySelector('.product-select');
        const qty = row.querySelector('.qty-input');
        const error = row.querySelector('.qty-error');

        select.addEventListener('change', () => {
            updateDisabledProducts();
            validate();
        });

        qty.addEventListener('input', validate);

        function validate() {
            const opt = select.selectedOptions[0];
            if (!opt) return;

            const available = parseInt(opt.dataset.available || 0);
            const entered = parseInt(qty.value || 0);

            if (entered > available) {
                error.textContent = `Only ${available} available`;
                error.classList.remove('hidden');
                qty.classList.add('border-red-500');
                form.dataset.invalid = '1';
            } else {
                error.classList.add('hidden');
                qty.classList.remove('border-red-500');
                form.dataset.invalid = '';
            }
        }
    }

    attachLogic(items.querySelector('.item-row'));

    document.getElementById('addItem').addEventListener('click', () => {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.item-row');

        const select = row.querySelector('.product-select');
        const qty = row.querySelector('.qty-input');

        select.name = `items[${index}][product_id]`;
        qty.name = `items[${index}][qty]`;
        select.innerHTML = stockOptions;

        row.querySelector('.remove-item').addEventListener('click', () => {
            row.remove();
            updateDisabledProducts();
        });

        items.appendChild(row);
        attachLogic(row);
        updateDisabledProducts();
        index++;
    });

    form.addEventListener('submit', e => {
        if (form.dataset.invalid === '1') {
            e.preventDefault();
            alert('Please fix quantity errors before submitting.');
        }
    });
});
</script>
@endpush
