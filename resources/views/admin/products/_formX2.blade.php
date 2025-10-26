@php
    $isEdit = isset($product);
    $productType = old('type', $product->type ?? 'simple');
    $selectedCategory = old('category_id', $product->category_id ?? '');
    $selectedSubCategory = old('sub_category_id', $product->sub_category_id ?? '');
    $variants = old('variants') ?: ($productType === 'variable' && $isEdit
        ? $product->variants->map(fn ($v) => [
            'fragrance' => $v->attributes['fragrance'] ?? '',
            'size' => $v->attributes['size'] ?? '',
        ])->values()->toArray()
        : []);
@endphp

<div
    x-data="productForm()"
    x-init="init()"
>
    {{-- Name --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Product Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="w-full border p-2 rounded" required>
    </div>

    {{-- Product Type --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Product Type</label>
        @if (!$isEdit)
            <select name="type" x-model="productType" class="w-full border p-2 rounded">
                <option value="simple">Simple</option>
                <option value="variable">Variable</option>
            </select>
        @else
            <div class="p-2 border bg-gray-100 text-gray-700 capitalize" x-text="productType"></div>
            <input type="hidden" name="type" :value="productType">
        @endif
    </div>

    {{-- Segment & Sub-segment --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block font-semibold mb-1">Segment</label>
            <select name="category_id" class="w-full border p-2 rounded" x-model="selectedCategory" @change="fetchSubSegments" required>
                <option value="">-- Select Segment --</option>
                @foreach($segments as $seg)
                    <option value="{{ $seg->id }}">{{ $seg->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold mb-1">Sub-segment</label>
            <select name="sub_category_id" class="w-full border p-2 rounded" x-model="selectedSubCategory">
                <option value="">-- Select Sub-segment --</option>
                <template x-for="sub in subSegments" :key="sub.id">
                    <option :value="sub.id" x-text="sub.name"></option>
                </template>
            </select>
        </div>
    </div>

    {{-- Unit --}}
    <div class="mb-4">
        <label class="block font-semibold mb-1">Unit</label>
        <select name="unit" class="w-full border p-2 rounded">
            <option value="">-- Select Unit --</option>
            <option value="dozen" @selected(old('unit', $product->unit ?? '') === 'dozen')>Dozen</option>
            <option value="piece" @selected(old('unit', $product->unit ?? '') === 'piece')>Piece</option>
            <option value="case" @selected(old('unit', $product->unit ?? '') === 'case')>Case</option>
        </select>
    </div>

    {{-- Variants --}}
    <div x-show="productType === 'variable'" class="mb-4">
        <label class="block font-semibold mb-2">Variants</label>
        <template x-for="(variant, index) in variants" :key="index">
            <div class="flex flex-wrap gap-2 mb-2">
                <input type="text" class="border p-2 rounded" :name="'variants[' + index + '][fragrance]'" placeholder="Fragrance" x-model="variant.fragrance">
                <input type="text" class="border p-2 rounded" :name="'variants[' + index + '][size]'" placeholder="Size" x-model="variant.size">
                <button type="button" class="text-red-600" @click="variants.splice(index, 1)">✕</button>
            </div>
        </template>
        <button type="button" class="mt-2 bg-green-600 text-white px-3 py-1 rounded" @click="variants.push({ fragrance: '', size: '' })">+ Add Variant</button>
    </div>

    {{-- Pricing + Details --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="block font-semibold mb-1">Dozen per Case</label>
            <input type="number" name="dozen_per_case" value="{{ old('dozen_per_case', $product->dozen_per_case ?? '') }}" class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block font-semibold mb-1">MRP per Unit</label>
            <input type="number" step="0.01" name="mrp_per_unit" value="{{ old('mrp_per_unit', $product->mrp_per_unit ?? '') }}" class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block font-semibold mb-1">PTR per Dozen</label>
            <input type="number" step="0.01" name="ptr_per_dozen" value="{{ old('ptr_per_dozen', $product->ptr_per_dozen ?? '') }}" class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block font-semibold mb-1">PTD per Dozen</label>
            <input type="number" step="0.01" name="ptd_per_dozen" value="{{ old('ptd_per_dozen', $product->ptd_per_dozen ?? '') }}" class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block font-semibold mb-1">Weight (gm)</label>
            <input type="number" step="0.01" name="weight_gm" value="{{ old('weight_gm', $product->weight_gm ?? '') }}" class="w-full border p-2 rounded">
        </div>
        <div>
            <label class="block font-semibold mb-1">Size</label>
            <input type="text" name="size" value="{{ old('size', $product->size ?? '') }}" class="w-full border p-2 rounded">
        </div>
    </div>

    {{-- Submit & Cancel --}}
    <div class="mt-6 flex gap-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">{{ $isEdit ? 'Update' : 'Create' }}</button>
        <a href="{{ route('admin.products.index') }}" class="border px-4 py-2 rounded hover:bg-gray-100">Cancel</a>
    </div>
</div>

<script>
function productForm() {
    return {
        productType: @json($productType),
        selectedCategory: @json($selectedCategory),
        selectedSubCategory: @json($selectedSubCategory),
        variants: @json($variants),
        subSegments: [],

        async init() {
            await this.fetchSubSegments();
        },

        async fetchSubSegments() {
            if (!this.selectedCategory) {
                this.subSegments = [];
                return;
            }
            const url = '{{ url('admin/categories') }}/' + this.selectedCategory + '/children';
            try {
                const res = await fetch(url);
                if (!res.ok) throw new Error('Failed to fetch');
                this.subSegments = await res.json();
            } catch (err) {
                console.error('Failed to load sub segments:', err);
            }
        }
    }
}
</script>

