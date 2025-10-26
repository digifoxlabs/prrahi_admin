@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.products._breadcrump')


        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                class="bg-yellow-100 text-red-800 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-6xl">





<form method="POST" action="{{ route('admin.products.store-variant', $product) }}">
    @csrf

    <div class="bg-white p-6 rounded shadow max-w-4xl mx-auto">

        <h2 class="text-xl font-bold mb-4">Add Variants for <span class="text-blue-700">{{ $product->name }}</span></h2>

        <div class="mb-4">
            <label class="font-semibold">Product Name</label>
            <input type="text" class="w-full border p-2 rounded bg-gray-100" value="{{ $product->name }}" readonly>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="font-semibold">Segment</label>
                <input type="text" class="w-full border p-2 rounded bg-gray-100" value="{{ $product->category->name ?? '' }}" readonly>
            </div>
            <div>
                <label class="font-semibold">Sub-Segment</label>
                <input type="text" class="w-full border p-2 rounded bg-gray-100" value="{{ $product->subCategory->name ?? '-' }}" readonly>
            </div>
        </div>

        <div class="mb-4">
            <label class="font-semibold">Unit</label>
            <input type="text" class="w-full border p-2 rounded bg-gray-100" value="{{ $product->unit }}" readonly>
        </div>

        <div x-data="variantForm()" class="mb-6">
            <template x-for="(variant, index) in variants" :key="index">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 p-4 mb-4 border rounded bg-gray-50">
                    <input type="text" class="w-full border p-2 rounded" :name="'variants['+index+'][code]'" placeholder="Product Code" x-model="variant.code" required>
                    <input type="text" class="w-full border p-2 rounded" :name="'variants['+index+'][fragrance]'" placeholder="Fragrance" x-model="variant.fragrance">
                    <input type="text" class="w-full border p-2 rounded" :name="'variants['+index+'][size]'" placeholder="Size" x-model="variant.size">
                    <input type="number" step="1" class="w-full border p-2 rounded" :name="'variants['+index+'][dozen_per_case]'" placeholder="Dozen/Case" x-model="variant.dozen_per_case">
                    <input type="number" step="0.01" class="w-full border p-2 rounded" :name="'variants['+index+'][mrp_per_unit]'" placeholder="MRP/Unit" x-model="variant.mrp_per_unit">
                    <input type="number" step="0.01" class="w-full border p-2 rounded" :name="'variants['+index+'][ptr_per_dozen]'" placeholder="PTR/Dozen" x-model="variant.ptr_per_dozen">
                    <input type="number" step="0.01" class="w-full border p-2 rounded" :name="'variants['+index+'][ptd_per_dozen]'" placeholder="PTD/Dozen" x-model="variant.ptd_per_dozen">
                    <input type="number" step="0.01" class="w-full border p-2 rounded" :name="'variants['+index+'][weight_gm]'" placeholder="Weight (gm)" x-model="variant.weight_gm">
                    <button type="button" class="text-red-600" @click="variants.splice(index, 1)">Remove</button>
                </div>
            </template>

            <button type="button" class="bg-green-600 text-white px-3 py-1 rounded" @click="variants.push({ code:'', fragrance: '', size: '', dozen_per_case: '', mrp_per_unit: '', ptr_per_dozen: '', ptd_per_dozen: '', weight_gm: '' })">
                + Add Variant
            </button>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Variants</button>
            <a href="{{ route('admin.products.index') }}" class="border px-4 py-2 rounded">Cancel</a>
        </div>
    </div>
</form>

<script>
function variantForm() {
    return {
        variants: [{ code:'', fragrance: '', size: '', dozen_per_case: '', mrp_per_unit: '', ptr_per_dozen: '', ptd_per_dozen: '', weight_gm: '' }]
    }
}
</script>


    

             </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'products',
        };
    </script>
@endpush


