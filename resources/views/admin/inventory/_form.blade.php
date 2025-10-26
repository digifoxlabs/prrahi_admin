@if ($errors->any())
    <div class="p-3 bg-red-100 text-red-800 rounded mb-4">
        <strong>There were some errors:</strong>
        <ul class="list-disc pl-5 mt-2 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $isEdit = isset($inventory);
@endphp

<div class="space-y-4">
    {{-- Product --}}
    <div>
        <label class="font-semibold">Product</label>
<select name="product_id" class="w-full border p-2 rounded" required>
    <option value="">-- Select Product --</option>
    @foreach($products as $product)
        @php
            $productName = $product->type === 'variant'
                ? ($product->parent->category->name ?? '') . ' - ' . ($product->attributes['fragrance'] ?? '')
                : $product->name;
        @endphp
        <option value="{{ $product->id }}"
            @selected(old('product_id', $transaction->product_id ?? '') == $product->id)>
            {{ $productName }}
        </option>
    @endforeach
</select>
    </div>

    {{-- Transaction Type --}}
    <div>
        <label class="font-semibold">Transaction Type</label>
        <select name="type" class="w-full border p-2 rounded" required>
            <option value="">-- Select Type --</option>
            <option value="in" @selected(old('type', $inventory->type ?? '') === 'in')>In</option>
            <option value="out" @selected(old('type', $inventory->type ?? '') === 'out')>Out</option>
            <option value="adjustment" @selected(old('type', $inventory->type ?? '') === 'adjustment')>Adjustment</option>
        </select>
    </div>

    {{-- Quantity --}}
    <div>
        <label class="font-semibold">Quantity</label>
        <input type="number" name="quantity" class="w-full border p-2 rounded" required
               value="{{ old('quantity', $inventory->quantity ?? '') }}">
    </div>

    {{-- Date --}}
    <div>
        <label class="font-semibold">Date</label>
        <input type="date" name="date" class="w-full border p-2 rounded" required
              value="{{ old('date', isset($inventory) ? \Illuminate\Support\Carbon::parse($inventory->date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
>
    </div>

    {{-- Remarks --}}
    <div>
        <label class="font-semibold">Remarks</label>
        <textarea name="remarks" class="w-full border p-2 rounded" rows="3">{{ old('remarks', $inventory->remarks ?? '') }}</textarea>
    </div>

    {{-- Submit --}}
    <div class="pt-4 flex gap-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ $isEdit ? 'Update Transaction' : 'Create Transaction' }}
        </button>
        <a href="{{ route('admin.inventory.index') }}" class="border px-4 py-2 rounded hover:bg-gray-100">Cancel</a>
    </div>
</div>
