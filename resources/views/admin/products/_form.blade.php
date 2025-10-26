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
$isEdit = isset($product);
$type = old('type', $product->type ?? 'simple');
$productType = $type;
$selectedCategory = old('category_id', $product->category_id ?? '');
$selectedSubCategory = old('sub_category_id', $product->sub_category_id ?? '');
$variants = [];
$variant = null;

if ($isEdit && $type === 'variant' && $product->parent) {
    $parent = $product->parent;
    $product->name = $parent->name;
    $product->category_id = $parent->category_id;
    $product->sub_category_id = $parent->sub_category_id;
    $product->unit = $parent->unit;
    $selectedCategory = $parent->category_id;
    $selectedSubCategory = $parent->sub_category_id;
    $variant = [
        'id' => $product->id,
        'code' => $product->code,
        'fragrance' => $product->attributes['fragrance'] ?? '',
        'size' => $product->attributes['size'] ?? '',
        'dozen_per_case' => $product->dozen_per_case,
        'free_dozen_per_case' => $product->free_dozen_per_case,
        'mrp_per_unit' => $product->mrp_per_unit,
        'ptr_per_dozen' => $product->ptr_per_dozen,
        'ptd_per_dozen' => $product->ptd_per_dozen,
        'weight_gm' => $product->weight_gm,
    ];
} elseif ($isEdit && $type === 'variable') {
    $variants = $product->children->map(fn($v) => [
        'id' => $v->id,
        'code' =>$v->code,
        'fragrance' => $v->attributes['fragrance'] ?? '',
        'size' => $v->attributes['size'] ?? '',
        'dozen_per_case' => $v->dozen_per_case,
        'free_dozen_per_case' => $v->free_dozen_per_case,
        'mrp_per_unit' => $v->mrp_per_unit,
        'ptr_per_dozen' => $v->ptr_per_dozen,
        'ptd_per_dozen' => $v->ptd_per_dozen,
        'weight_gm' => $v->weight_gm,
    ])->toArray();
} else {
    $productType = old('type', 'simple');
    $variants = old('variants', []);
}
@endphp

<div x-data="productForm()" x-init="init()">
    {{-- Product Name --}}
    <div class="mb-4">
        <label class="font-semibold">Product Name</label>
        @if($isEdit && $productType === 'variant')
            <input type="text" name="name" class="w-full border p-2 rounded bg-gray-100" value="{{ $product->name }}" readonly>
        @else
            <input type="text" name="name" class="w-full border p-2 rounded"
                   value="{{ old('name', $product->name ?? '') }}" required>
        @endif
    </div>

    {{-- Product Type --}}
    <div class="mb-4">
        @if (!$isEdit)
            <label class="font-semibold">Product Type</label>
            <select name="type" x-model="productType" class="w-full border p-2 rounded">
                <option value="simple">Simple</option>
                <option value="variable">Variable</option>
            </select>
        @else
            <input type="hidden" name="type" :value="productType">
        @endif
    </div>

    {{-- Product Code --}}
     @if(!$isEdit)
    <div x-show="productType === 'simple'">        
        <div class="mb-4">
            <label class="font-semibold">Product Code</label>
            <input type="text" name="code" class="w-full border p-2 rounded"
                value="" placeholder ="Keep Blank for NULL">
        </div>        
    </div>
    @endif

    @if($isEdit && $productType === 'simple')
        <div class="mb-4">
            <label class="font-semibold">Product Code</label>
            <input type="text" name="code" class="w-full border p-2 rounded"
                value="{{ old('code', $product->code ?? '') }}" placeholder ="Keep Blank for NULL">
        </div>
     @endif

    {{-- Category & Sub-category --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="font-semibold">Segment</label>
            @if($isEdit && $productType === 'variant')
                <input type="text" class="w-full border p-2 rounded bg-gray-100"
                       value="{{ $product->parent->category->name ?? '-' }}" readonly>
            @else
                <select name="category_id" class="w-full border p-2 rounded"
                        x-model="selectedCategory" @change="fetchSubSegments" required>
                    <option value="">-- Select Segment --</option>
                    @foreach($segments as $seg)
                        <option value="{{ $seg->id }}" @selected($seg->id == $selectedCategory)>
                            {{ $seg->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>
        <div>
            <label class="font-semibold">Sub-Segment</label>
            @if($isEdit && $productType === 'variant')
                <input type="text" class="w-full border p-2 rounded bg-gray-100"
                       value="{{ $product->parent->subCategory->name ?? '-' }}" readonly>
            @else
                <select name="sub_category_id" class="w-full border p-2 rounded"
                        x-model="selectedSubCategory">
                    <option value="">-- Select Sub-segment --</option>
                    <template x-for="sub in subSegments" :key="sub.id">
                        <option :value="sub.id" x-text="sub.name"
                                :selected="sub.id == selectedSubCategory"></option>
                    </template>
                </select>
            @endif
        </div>
    </div>

    {{-- Unit --}}
    <div class="mb-4">
        <label class="font-semibold">Unit</label>
        @if($isEdit && $productType === 'variant')
            <input type="text" class="w-full border p-2 rounded bg-gray-100"
                   value="{{ $product->unit }}" readonly>
        @else
            <select name="unit" class="w-full border p-2 rounded">
                <option value="">-- Select Unit --</option>
                <option value="dozen" @selected(old('unit', $product->unit ?? '') === 'dozen')>Dozen</option>
                <option value="piece" @selected(old('unit', $product->unit ?? '') === 'piece')>Piece</option>
                <option value="case" @selected(old('unit', $product->unit ?? '') === 'case')>Case</option>
            </select>
        @endif
    </div>

    {{-- Variant Editable Fields --}}
    @if($isEdit && $productType === 'variant')
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
            @foreach (['code' ,'fragrance', 'size', 'dozen_per_case', 'mrp_per_unit', 'ptr_per_dozen', 'ptd_per_dozen', 'weight_gm','free_dozen_per_case'] as $field)
                <div>
                    <label class="font-semibold">{{ ucfirst(str_replace('_',' ',$field)) }}</label>
                    <input type="{{ in_array($field, ['code','fragrance', 'size']) ? 'text' : 'number' }}"
                           name="{{ $field }}"
                           class="w-full border p-2 rounded"
                           value="{{ old($field, $variant[$field] ?? '') }}">
                </div>
            @endforeach
        </div>
    @endif

    {{-- Simple Product Fields --}}
    <div x-show="productType === 'simple'" class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <template x-if="productType === 'simple'">
            <div class="contents">
                <div><label class="font-semibold">Dozen/Case</label>
                    <input type="number" name="dozen_per_case"
                           class="w-full border p-2 rounded"
                           value="{{ old('dozen_per_case', $product->dozen_per_case ?? '') }}">
                </div>
                <div><label class="font-semibold">MRP/Unit</label>
                    <input type="number" step="0.01" name="mrp_per_unit"
                           class="w-full border p-2 rounded"
                           value="{{ old('mrp_per_unit', $product->mrp_per_unit ?? '') }}">
                </div>
                <div><label class="font-semibold">PTR/Dozen</label>
                    <input type="number" step="0.01" name="ptr_per_dozen"
                           class="w-full border p-2 rounded"
                           value="{{ old('ptr_per_dozen', $product->ptr_per_dozen ?? '') }}">
                </div>
                <div><label class="font-semibold">PTD/Dozen</label>
                    <input type="number" step="0.01" name="ptd_per_dozen"
                           class="w-full border p-2 rounded"
                           value="{{ old('ptd_per_dozen', $product->ptd_per_dozen ?? '') }}">
                </div>
                <div><label class="font-semibold">Weight (gm)</label>
                    <input type="number" step="0.01" name="weight_gm"
                           class="w-full border p-2 rounded"
                           value="{{ old('weight_gm', $product->weight_gm ?? '') }}">
                </div>
                <div><label class="font-semibold">Size</label>
                    <input type="text" name="size"
                           class="w-full border p-2 rounded"
                           value="{{ old('size', $product->size ?? '') }}">
                </div>     
                               
                <div>
                    <label class="font-semibold">Free Dozen/case</label>
                    <input type="text" name="free_dozen_per_case"
                           class="w-full border p-2 rounded"
                           value="{{ old('size', $product->free_dozen_per_case ?? '') }}">
                </div>
            </div>
        </template>
    </div>

    {{-- Variants Section for Variable Product --}}
    <div x-show="productType === 'variable'" class="mb-6">
        <label class="font-semibold mb-2 block">Variants</label>
        <template x-for="(variant, index) in variants" :key="index">

            {{-- <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-2 p-3 border rounded bg-gray-50">

                <input type="hidden" :name="'variants['+index+'][id]'" x-model="variant.id">
                <input type="text" class="border p-2 rounded" 
               :name="'variants['+index+'][code]'" 
               placeholder="Product Code" 
               x-model="variant.code">

                <input type="text" class="border p-2 rounded"
                       :name="'variants['+index+'][fragrance]'" placeholder="Fragrance"
                       x-model="variant.fragrance">
                <input type="text" class="border p-2 rounded"
                       :name="'variants['+index+'][size]'" placeholder="Size"
                       x-model="variant.size">
                <input type="number" class="border p-2 rounded"
                       :name="'variants['+index+'][dozen_per_case]'" placeholder="Dozen/Case"
                       x-model="variant.dozen_per_case">
                <input type="number" step="0.01" class="border p-2 rounded"
                       :name="'variants['+index+'][mrp_per_unit]'" placeholder="MRP/Unit"
                       x-model="variant.mrp_per_unit">
                <input type="number" step="0.01" class="border p-2 rounded"
                       :name="'variants['+index+'][ptr_per_dozen]'" placeholder="PTR/Dozen"
                       x-model="variant.ptr_per_dozen">
                <input type="number" step="0.01" class="border p-2 rounded"
                       :name="'variants['+index+'][ptd_per_dozen]'" placeholder="PTD/Dozen"
                       x-model="variant.ptd_per_dozen">
                <input type="number" step="0.01" class="border p-2 rounded"
                       :name="'variants['+index+'][weight_gm]'" placeholder="Weight (gm)"
                       x-model="variant.weight_gm">      
                <input type="number" step="0.01" class="border p-2 rounded"
                       :name="'variants['+index+'][free_dozen_per_case]'" placeholder="Free Dozen/Case"
                       x-model="variant.free_dozen_per_case">
                <button type="button" class="text-red-600"
                        @click="variants.splice(index,1)"
                        x-show="!@json($isEdit)">✕</button>
            </div> --}}

             <div class="relative grid grid-cols-2 md:grid-cols-3 gap-4 mb-4 p-4 border rounded-lg bg-white shadow-sm">
                <!-- X Button positioned at top right -->
                <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm"
                        @click="variants.splice(index,1)"
                        x-show="!@json($isEdit)">✕</button>
                
                <input type="hidden" :name="'variants['+index+'][id]'" x-model="variant.id">
                
                <!-- Code Input with Label -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Product Code</label>
                    <input type="text" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           :name="'variants['+index+'][code]'" 
                           placeholder="e.g., PROD-001"
                           x-model="variant.code">
                </div>
                
                <!-- Fragrance Input with Label -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Fragrance</label>
                    <input type="text" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           :name="'variants['+index+'][fragrance]'" placeholder="e.g., Lavender"
                           x-model="variant.fragrance">
                </div>
                
                <!-- Size Input with Label -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Size</label>
                    <input type="text" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           :name="'variants['+index+'][size]'" placeholder="e.g., 250ml"
                           x-model="variant.size">
                </div>
                
                <!-- Dozen/Case Input with Label -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Dozen/Case</label>
                    <input type="number" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           :name="'variants['+index+'][dozen_per_case]'" placeholder="e.g., 12"
                           x-model="variant.dozen_per_case">
                </div>
                
                <!-- MRP/Unit Input with Label -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">MRP/Unit ($)</label>
                    <input type="number" step="0.01" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           :name="'variants['+index+'][mrp_per_unit]'" placeholder="e.g., 5.99"
                           x-model="variant.mrp_per_unit">
                </div>
                
                <!-- PTR/Dozen Input with Label -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">PTR/Dozen ($)</label>
                    <input type="number" step="0.01" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           :name="'variants['+index+'][ptr_per_dozen]'" placeholder="e.g., 59.99"
                           x-model="variant.ptr_per_dozen">
                </div>
                
                <!-- PTD/Dozen Input with Label -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">PTD/Dozen ($)</label>
                    <input type="number" step="0.01" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           :name="'variants['+index+'][ptd_per_dozen]'" placeholder="e.g., 54.99"
                           x-model="variant.ptd_per_dozen">
                </div>
                
                <!-- Weight Input with Label -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Weight (gm)</label>
                    <input type="number" step="0.01" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           :name="'variants['+index+'][weight_gm]'" placeholder="e.g., 350.5"
                           x-model="variant.weight_gm">
                </div>
                
                <!-- Free Dozen/Case Input with Label -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Free Dozen/Case</label>
                    <input type="number" step="0.01" class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           :name="'variants['+index+'][free_dozen_per_case]'" placeholder="e.g., 1"
                           x-model="variant.free_dozen_per_case">
                </div>
            </div>





        </template>
        @if(!$isEdit)
            <button type="button" class="mt-2 bg-green-600 text-white px-3 py-1 rounded"
                    @click="variants.push({id:'', code:'', fragrance:'', size:'', dozen_per_case:'', mrp_per_unit:'', ptr_per_dozen:'', ptd_per_dozen:'', weight_gm:'', free_dozen_per_case:''})">
                + Add Variant
            </button>
        @endif
    </div>

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
        async init() { await this.fetchSubSegments(); },
        async fetchSubSegments() {
            if (!this.selectedCategory) { this.subSegments = []; this.selectedSubCategory = ''; return; }
            const res = await fetch('{{ url('admin/categories') }}/' + this.selectedCategory + '/children');
            if (!res.ok) return this.subSegments = [];
            this.subSegments = await res.json();
            if (!this.subSegments.some(s => s.id == this.selectedSubCategory)) {
                this.selectedSubCategory = '';
            }
        }
    }
}
</script>
