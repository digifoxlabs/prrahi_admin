@if ($errors->any())
    <div @class(['p-3', 'bg-red-100', 'text-red-800', 'rounded', 'mb-4'])>
        <strong>There were some errors:</strong>
        <ul @class(['list-disc', 'pl-5', 'mt-2', 'space-y-1'])>
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
        'hsn' => $product->hsn,
        'fragrance' => $product->attributes['fragrance'] ?? '',
        'size' => $product->attributes['size'] ?? '',
        'base_unit' => $product->base_unit,
        'base_quantity' => $product->base_quantity,
        'mrp_per_unit' => $product->mrp_per_unit,
        'ptr_per_dozen' => $product->ptr_per_dozen,
        'retailer_discount_percent' => $product->retailer_discount_percent,
        'ptd_per_dozen' => $product->ptd_per_dozen,
        'distributor_discount_percent' => $product->distributor_discount_percent,
        'weight_gm' => $product->weight_gm,
    ];
} elseif ($isEdit && $type === 'variable') {
    $variants = $product->children->map(fn($v) => [
        'id' => $v->id,
        'code' =>$v->code,
        'fragrance' => $v->attributes['fragrance'] ?? '',
        'size' => $v->attributes['size'] ?? '',
        'base_unit' => $v->base_unit,
        'base_quantity' => $v->base_quantity,
        'mrp_per_unit' => $v->mrp_per_unit,
        'ptr_per_dozen' => $v->ptr_per_dozen,
        'retailer_discount_percent' => $v->retailer_discount_percent,
        'ptd_per_dozen' => $v->ptd_per_dozen,
        'distributor_discount_percent' => $v->distributor_discount_percent,
        'weight_gm' => $v->weight_gm,
    ])->toArray();
} else {
    $productType = old('type', 'simple');
    $variants = old('variants', []);
}
@endphp

<div x-data="productForm()" x-init="init()">
    {{-- Product Name --}}
    <div @class(['mb-4'])>
        <label @class(['font-semibold'])>Product Name</label>
        @if($isEdit && $productType === 'variant')
            <input type="text" name="name" @class(['w-full', 'border', 'p-2', 'rounded', 'bg-gray-100']) value="{{ $product->name }}" readonly>
        @else
            <input type="text" name="name" @class(['w-full', 'border', 'p-2', 'rounded'])
                   value="{{ old('name', $product->name ?? '') }}" required>
        @endif
    </div>

    {{-- Product Type --}}
    <div @class(['mb-4'])>
        @if (!$isEdit)
            <label @class(['font-semibold'])>Product Type</label>
            <select name="type" x-model="productType" @class(['w-full', 'border', 'p-2', 'rounded'])>
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
        <div @class(['mb-4'])>
            <label @class(['font-semibold'])>Product Code</label>
            <input type="text" name="code" @class(['w-full', 'border', 'p-2', 'rounded'])
                value="" placeholder ="Keep Blank for NULL">
        </div>        
        <div @class(['mb-4'])>
            <label @class(['font-semibold'])>HSN Code</label>
            <input type="text" name="hsn" @class(['w-full', 'border', 'p-2', 'rounded'])
                value="" placeholder ="6 or 8 digit HSN Code">
        </div>        
    </div>
    @endif

    @if($isEdit && $productType === 'simple')
        <div @class(['mb-4'])>
            <label @class(['font-semibold'])>Product Code</label>
            <input type="text" name="code" @class(['w-full', 'border', 'p-2', 'rounded'])
                value="{{ old('code', $product->code ?? '') }}" placeholder ="Keep Blank for NULL">
        </div>
        <div @class(['mb-4'])>
            <label @class(['font-semibold'])>HSN Code</label>
            <input type="text" name="hsn" @class(['w-full', 'border', 'p-2', 'rounded'])
                value="{{ old('hsn', $product->hsn ?? '') }}" placeholder ="6 or 8 digit HSN Code">
        </div>
     @endif

    {{-- Category & Sub-category --}}
    <div @class(['grid', 'grid-cols-2', 'gap-4', 'mb-4'])>
        <div>
            <label @class(['font-semibold'])>Segment</label>
            @if($isEdit && $productType === 'variant')
                <input type="text" @class(['w-full', 'border', 'p-2', 'rounded', 'bg-gray-100'])
                       value="{{ $product->parent->category->name ?? '-' }}" readonly>
            @else
                <select name="category_id" @class(['w-full', 'border', 'p-2', 'rounded'])
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
            <label @class(['font-semibold'])>Sub-Segment</label>
            @if($isEdit && $productType === 'variant')
                <input type="text" @class(['w-full', 'border', 'p-2', 'rounded', 'bg-gray-100'])
                       value="{{ $product->parent->subCategory->name ?? '-' }}" readonly>
            @else
                <select name="sub_category_id" @class(['w-full', 'border', 'p-2', 'rounded'])
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

    {{-- Base Unit --}}
    <div @class(['grid', 'grid-cols-2', 'gap-4', 'mb-4'])>
        <div>
            <label @class(['font-semibold'])>Base Unit</label>
            @if($isEdit && $productType === 'variant')
                <input type="text" @class(['w-full', 'border', 'p-2', 'rounded', 'bg-gray-100'])
                    value="{{ strtoupper($product->parent->base_unit) }}" readonly>
            @else
                <select name="base_unit" @class(['w-full', 'border', 'p-2', 'rounded'])>
                    <option value="">-- Select Unit --</option>
                    <option value="dozen" @selected(old('base_unit', $product->unit ?? 'dozen') === 'dozen')>Dozen</option>
                    <option value="piece" @selected(old('base_unit', $product->unit ?? '') === 'piece')>Piece</option>
                    <option value="case" @selected(old('base_unit', $product->unit ?? '') === 'case')>Case</option>
                </select>
            @endif
        </div>

           <!-- Base Quantity -->
        <div>
            <label @class(['font-semibold'])>Base Quantity (per unit)</label>
            @if($isEdit && $productType === 'variant')
                <input type="number" step="0.01"
                    @class(['w-full', 'border', 'p-2', 'rounded', 'bg-gray-100'])
                    value="{{ $product->parent->base_quantity ?? '' }}" readonly>
            @else
                <input type="number" step="0.01" name="base_quantity"
                    @class(['w-full', 'border', 'p-2', 'rounded'])
                    value="{{ old('base_quantity', $product->base_quantity ?? 12) }}"
                    placeholder="e.g. 12 for dozen, 1 for piece">
            @endif
            @error('base_quantity')
                <p @class(['text-red-500', 'text-sm', 'mt-1'])>{{ $message }}</p>
            @enderror
        </div>

    </div>

{{-- Variant Editable Fields (Edit mode - single variant) --}}
@if($isEdit && $productType === 'variant')
    <div class="space-y-6 mb-6 bg-white rounded shadow p-6">

        <!-- A) Identity -->
        <div>
            <h4 class="font-semibold mb-3 text-left">Identity</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Product Code -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Product Code</label>
                    <input type="text"
                           name="code"
                           class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., PROD-001"
                           value="{{ old('code', $variant['code'] ?? '') }}">
                </div>
                <!-- HSN Code -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">HSN Code</label>
                    <input type="text"
                           name="hsn"
                           class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="6 or 8 digit hsn code"
                           value="{{ old('hsn', $variant['hsn'] ?? '') }}">
                </div>

                <!-- Fragrance -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Fragrance</label>
                    <input type="text"
                           name="fragrance"
                           class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Lavender"
                           value="{{ old('fragrance', $variant['fragrance'] ?? '') }}">
                </div>

                <!-- Size -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Size</label>
                    <input type="text"
                           name="size"
                           class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 250ml"
                           value="{{ old('size', $variant['size'] ?? '') }}">
                </div>
            </div>
        </div>

        <!-- B) Product Specs -->
        <div>
            <h4 class="font-semibold mb-3 text-left">Product Specs</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <!-- MRP/Unit -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">MRP/Unit</label>
                    <input type="number" step="0.01"
                           name="mrp_per_unit"
                           class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 5.99"
                           value="{{ old('mrp_per_unit', $variant['mrp_per_unit'] ?? '') }}">
                </div>

                <!-- Weight (gm) -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Weight (gm)</label>
                    <input type="number" step="0.01"
                           name="weight_gm"
                           class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 350.5"
                           value="{{ old('weight_gm', $variant['weight_gm'] ?? '') }}">
                </div>


            </div>
        </div>

        <!-- C) Distributor Pricing -->
        <div>
            <h4 class="font-semibold mb-3 text-left">Distributor Pricing</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <!-- PTD/Dozen -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">PTD/Dozen</label>
                    <input type="number" step="0.01"
                           name="ptd_per_dozen"
                           class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 54.99"
                           value="{{ old('ptd_per_dozen', $variant['ptd_per_dozen'] ?? '') }}">
                </div>

                <!-- Discount to Distributor (%) -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Discount to Distributor (%)</label>
                    <input type="number" step="0.01"
                           name="distributor_discount_percent"
                           class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 5"
                           value="{{ old('distributor_discount_percent', $variant['distributor_discount_percent'] ?? '') }}">
                </div>

                <!-- (Optional) Keep a blank column for layout balance or future fields -->
                <div></div>
            </div>
        </div>

        <!-- D) Retailer Pricing -->
        <div>
            <h4 class="font-semibold mb-3 text-left">Retailer Pricing</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <!-- PTR/Dozen -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">PTR/Dozen</label>
                    <input type="number" step="0.01"
                           name="ptr_per_dozen"
                           class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 59.99"
                           value="{{ old('ptr_per_dozen', $variant['ptr_per_dozen'] ?? '') }}">
                </div>

                <!-- Discount for Retailer (%) -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Discount for Retailer (%)</label>
                    <input type="number" step="0.01"
                           name="retailer_discount_percent"
                           class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 2.5"
                           value="{{ old('retailer_discount_percent', $variant['retailer_discount_percent'] ?? '') }}">
                </div>

                <!-- (Optional) blank column for alignment -->
                <div></div>
            </div>
        </div>

    </div>
@endif



{{-- Simple Product Fields --}}
<template x-if="productType === 'simple'">

    <div class="space-y-6">

    {{-- 1) Core Specs: MRP / Weight / Size --}}
    <div @class(['border', 'rounded-xl', 'p-4', 'md:p-5', 'bg-white'])>
        <h3 @class(['font-semibold', 'mb-3'])>Product Specs</h3>
        <div @class(['grid', 'grid-cols-2', 'md:grid-cols-3', 'gap-4'])>
            {{-- MRP/Unit --}}
            <div>
                <label @class(['font-semibold'])>MRP/Unit</label>
                <input type="number" step="0.01" name="mrp_per_unit"
                       @class(['w-full', 'border', 'p-2', 'rounded'])
                       value="{{ old('mrp_per_unit', $product->mrp_per_unit ?? '') }}">
                @error('mrp_per_unit') <p @class(['text-red-500', 'text-sm', 'mt-1'])>{{ $message }}</p> @enderror
            </div>

            {{-- Weight (gm) --}}
            <div>
                <label @class(['font-semibold'])>Weight (gm)</label>
                <input type="number" step="0.01" name="weight_gm"
                       @class(['w-full', 'border', 'p-2', 'rounded'])
                       value="{{ old('weight_gm', $product->weight_gm ?? '') }}">
                @error('weight_gm') <p @class(['text-red-500', 'text-sm', 'mt-1'])>{{ $message }}</p> @enderror
            </div>

            {{-- Size --}}
            <div>
                <label @class(['font-semibold'])>Size</label>
                <input type="text" name="size"
                       @class(['w-full', 'border', 'p-2', 'rounded'])
                       value="{{ old('size', $product->size ?? '') }}">
                @error('size') <p @class(['text-red-500', 'text-sm', 'mt-1'])>{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- 2) Distributor Pricing --}}
    <div @class(['border', 'rounded-xl', 'p-4', 'md:p-5', 'bg-white'])>
        <h3 @class(['font-semibold', 'mb-3'])>Distributor Pricing</h3>
        <div @class(['grid', 'grid-cols-2', 'md:grid-cols-3', 'gap-4'])>
            {{-- PTD/Dozen --}}
            <div>
                <label @class(['font-semibold'])>PTD/Dozen</label>
                <input type="number" step="0.01" name="ptd_per_dozen"
                       @class(['w-full', 'border', 'p-2', 'rounded'])
                       value="{{ old('ptd_per_dozen', $product->ptd_per_dozen ?? '') }}">
                @error('ptd_per_dozen') <p @class(['text-red-500', 'text-sm', 'mt-1'])>{{ $message }}</p> @enderror
            </div>

            {{-- Discount to Distributor (%) --}}
            <div>
                <label @class(['font-semibold'])>Discount to Distributor (%)</label>
                <input type="number" step="0.01" name="distributor_discount_percent"
                       @class(['w-full', 'border', 'p-2', 'rounded'])
                       value="{{ old('distributor_discount_percent', $product->distributor_discount_percent ?? '') }}"
                       placeholder="e.g. 5 for 5%">
                @error('distributor_discount_percent') <p @class(['text-red-500', 'text-sm', 'mt-1'])>{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- 3) Retailer Pricing --}}
    <div @class(['border', 'rounded-xl', 'p-4', 'md:p-5', 'bg-white'])>
        <h3 @class(['font-semibold', 'mb-3'])>Retailer Pricing</h3>
        <div @class(['grid', 'grid-cols-2', 'md:grid-cols-3', 'gap-4'])>
            {{-- PTR/Dozen --}}
            <div>
                <label @class(['font-semibold'])>PTR/Dozen</label>
                <input type="number" step="0.01" name="ptr_per_dozen"
                       @class(['w-full', 'border', 'p-2', 'rounded'])
                       value="{{ old('ptr_per_dozen', $product->ptr_per_dozen ?? '') }}">
                @error('ptr_per_dozen') <p @class(['text-red-500', 'text-sm', 'mt-1'])>{{ $message }}</p> @enderror
            </div>

            {{-- Discount for Retailer (%) --}}
            <div>
                <label @class(['font-semibold'])>Discount for Retailer (%)</label>
                <input type="number" step="0.01" name="retailer_discount_percent"
                       @class(['w-full', 'border', 'p-2', 'rounded'])
                       value="{{ old('retailer_discount_percent', $product->retailer_discount_percent ?? '') }}"
                       placeholder="e.g. 2.5 for 2.5%">
                @error('retailer_discount_percent') <p @class(['text-red-500', 'text-sm', 'mt-1'])>{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    </div>
</template>


{{-- Variants Section for Variable Product --}}
<div x-show="productType === 'variable'" class="mb-6">
    <label class="font-semibold mb-2 block">Variants</label>

    <template x-for="(variant, index) in variants" :key="index">
        <div class="relative mb-4 p-4 border rounded-xl bg-white shadow-sm space-y-5">

            <!-- Remove button (hidden in edit mode) -->
            <button type="button"
                    class="absolute top-2 right-2 text-red-500 hover:text-red-700 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm"
                    @click="variants.splice(index,1)"
                    x-show="!@json($isEdit)">✕</button>

            <input type="hidden" :name="'variants['+index+'][id]'" x-model="variant.id">

            <!-- A) Identity -->
            <div>
                <h4 class="font-semibold mb-3">Identity</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- Product Code -->
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Product Code</label>
                        <input type="text"
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               :name="'variants['+index+'][code]'"
                               placeholder="e.g., PROD-001"
                               x-model="variant.code">
                    </div>
                    <!-- HSN Code -->
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">HSN Code</label>
                        <input type="text"
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               :name="'variants['+index+'][hsn]'"
                               placeholder="6 or 8 digit hsn code"
                               x-model="variant.hsn">
                    </div>

                    <!-- Fragrance -->
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Fragrance</label>
                        <input type="text"
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               :name="'variants['+index+'][fragrance]'"
                               placeholder="e.g., Lavender"
                               x-model="variant.fragrance">
                    </div>

                    <!-- Size -->
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Size</label>
                        <input type="text"
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               :name="'variants['+index+'][size]'"
                               placeholder="e.g., 250ml"
                               x-model="variant.size">
                    </div>
                </div>
            </div>

            <!-- B) Product Specs -->
            <div>
                <h4 class="font-semibold mb-3">Product Specs</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- MRP/Unit -->
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">MRP/Unit</label>
                        <input type="number" step="0.01"
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               :name="'variants['+index+'][mrp_per_unit]'"
                               placeholder="e.g., 5.99"
                               x-model="variant.mrp_per_unit">
                    </div>

                    <!-- Weight (gm) -->
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Weight (gm)</label>
                        <input type="number" step="0.01"
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               :name="'variants['+index+'][weight_gm]'"
                               placeholder="e.g., 350.5"
                               x-model="variant.weight_gm">
                    </div>
                </div>
            </div>

            <!-- C) Distributor Pricing -->
            <div>
                <h4 class="font-semibold mb-3">Distributor Pricing</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- PTD/Dozen -->
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">PTD/Dozen</label>
                        <input type="number" step="0.01"
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               :name="'variants['+index+'][ptd_per_dozen]'"
                               placeholder="e.g., 54.99"
                               x-model="variant.ptd_per_dozen">
                    </div>

                    <!-- Discount to Distributor (%) -->
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Discount to Distributor (%)</label>
                        <input type="number" step="0.01"
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               :name="'variants['+index+'][distributor_discount_percent]'"
                               placeholder="e.g., 5"
                               x-model="variant.distributor_discount_percent">
                    </div>
                </div>
            </div>

            <!-- D) Retailer Pricing -->
            <div>
                <h4 class="font-semibold mb-3">Retailer Pricing</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- PTR/Dozen -->
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">PTR/Dozen</label>
                        <input type="number" step="0.01"
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               :name="'variants['+index+'][ptr_per_dozen]'"
                               placeholder="e.g., 59.99"
                               x-model="variant.ptr_per_dozen">
                    </div>

                    <!-- Discount for Retailer (%) -->
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Discount for Retailer (%)</label>
                        <input type="number" step="0.01"
                               class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               :name="'variants['+index+'][retailer_discount_percent]'"
                               placeholder="e.g., 2.5"
                               x-model="variant.retailer_discount_percent">
                    </div>
                </div>
            </div>


        </div>
    </template>

    @if(!$isEdit)
        <button type="button"
                class="mt-2 bg-green-600 text-white px-3 py-1 rounded"
                @click="variants.push({
                    id:'', code:'', fragrance:'', size:'',
                    mrp_per_unit:'', weight_gm:'',
                    ptd_per_dozen:'', distributor_discount_percent:'',
                    ptr_per_dozen:'', retailer_discount_percent:''
                })">
            + Add Variant
        </button>
    @endif
</div>




    <div @class(['mt-6', 'flex', 'gap-4'])>
        <button @class(['bg-blue-600', 'text-white', 'px-4', 'py-2', 'rounded'])>{{ $isEdit ? 'Update' : 'Create' }}</button>
        <a href="{{ route('admin.products.index') }}" @class(['border', 'px-4', 'py-2', 'rounded', 'hover:bg-gray-100'])>Cancel</a>
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
