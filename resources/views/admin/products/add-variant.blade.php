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



    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <div
        class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
        <div class="mx-auto w-full max-w-4xl">

            <form method="POST" action="{{ route('admin.products.store-variant', $product) }}">
                @csrf

         

                    <h2 class="text-xl font-bold mb-4">Add Variants for <span
                            class="text-blue-700">{{ $product->name }}</span></h2>

                    <div class="mb-4">
                        <label class="font-semibold">Product Name</label>
                        <input type="text" class="w-full border p-2 rounded bg-gray-100" value="{{ $product->name }}"
                            readonly>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="font-semibold">Segment</label>
                            <input type="text" class="w-full border p-2 rounded bg-gray-100"
                                value="{{ $product->category->name ?? '' }}" readonly>
                        </div>
                        <div>
                            <label class="font-semibold">Sub-Segment</label>
                            <input type="text" class="w-full border p-2 rounded bg-gray-100"
                                value="{{ $product->subCategory->name ?? '-' }}" readonly>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="font-semibold">Unit</label>
                        <input type="text" class="w-full border p-2 rounded bg-gray-100"
                            value="{{ strtoupper($product->base_unit) }}" readonly>
                    </div>

                    <div x-data="variantForm()" class="mb-6">

                        <template x-for="(variant, index) in variants" :key="index">
                            <div class="p-4 mb-4 border rounded bg-blue-50 space-y-6">

                                <!-- A) Identity -->
                                <div>
                                    <h4 class="font-semibold mb-3">Identity</h4>

                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

                                        <!-- Product Code -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 mb-1">Product Code</label>
                                            <input type="text"
                                                class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                :name="'variants['+index+'][code]'" placeholder="e.g., PROD-001"
                                                x-model="variant.code">
                                        </div>

                                        <!-- Fragrance -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 mb-1">Fragrance</label>
                                            <input type="text"
                                                class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                :name="'variants['+index+'][fragrance]'" placeholder="e.g., Lavender"
                                                x-model="variant.fragrance">
                                        </div>

                                        <!-- Size -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 mb-1">Size</label>
                                            <input type="text"
                                                class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                :name="'variants['+index+'][size]'" placeholder="e.g., 250ml"
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
                                                :name="'variants['+index+'][mrp_per_unit]'" placeholder="e.g., 5.99"
                                                x-model="variant.mrp_per_unit">
                                        </div>

                                        <!-- Weight (gm) -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 mb-1">Weight (gm)</label>
                                            <input type="number" step="0.01"
                                                class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                :name="'variants['+index+'][weight_gm]'" placeholder="e.g., 350.5"
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
                                                :name="'variants['+index+'][ptd_per_dozen]'" placeholder="e.g., 54.99"
                                                x-model="variant.ptd_per_dozen">
                                        </div>

                                        <!-- Distributor Discount (%) -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 mb-1">Distributor Discount
                                                (%)</label>
                                            <input type="number" step="0.01"
                                                class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                :name="'variants['+index+'][distributor_discount_percent]'"
                                                placeholder="e.g., 5" x-model="variant.distributor_discount_percent">
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
                                                :name="'variants['+index+'][ptr_per_dozen]'" placeholder="e.g., 59.99"
                                                x-model="variant.ptr_per_dozen">
                                        </div>

                                        <!-- Retailer Discount (%) -->
                                        <div class="flex flex-col">
                                            <label class="text-sm font-medium text-gray-700 mb-1">Retailer Discount
                                                (%)</label>
                                            <input type="number" step="0.01"
                                                class="border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                :name="'variants['+index+'][retailer_discount_percent]'"
                                                placeholder="e.g., 2.5" x-model="variant.retailer_discount_percent">
                                        </div>

                                    </div>
                                </div>

                                <!-- Remove Button -->
                                <button type="button" class="text-red-600 bg-white font-medium mt-2"
                                    @click="variants.splice(index, 1)">
                                    Remove Variant
                                </button>

                            </div>
                        </template>

                        <!-- Add Variant Button -->
                        <button type="button" class="bg-green-600 text-white px-3 py-1 rounded" @click="variants.push({ 
                            code:'', 
                            fragrance:'', 
                            size:'', 
                            mrp_per_unit:'', 
                            weight_gm:'', 
                            ptd_per_dozen:'', 
                            distributor_discount_percent:'', 
                            ptr_per_dozen:'', 
                            retailer_discount_percent:''
                            })">
                            + Add Variant
                        </button>

                    </div>

                    <div class="mt-6 flex gap-4">
                        <input type="hidden" name="redirect_to"
                            value="{{ request('redirect_to', route('admin.products.index')) }}">

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Variants</button>
                        {{-- <a href="{{ route('admin.products.index') }}" class="border px-4 py-2 rounded">Cancel</a>
                        --}}
                        <a href="{{ request('redirect_to', route('admin.products.index')) }}"
                            class="border px-4 py-2 rounded">
                            Cancel
                        </a>
                    </div>
              
            </form>

            <script>
                function variantForm() {
                    return {
                        variants: [{
                            code: '',
                            fragrance: '',
                            size: '',
                            dozen_per_case: '',
                            mrp_per_unit: '',
                            ptr_per_dozen: '',
                            ptd_per_dozen: '',
                            weight_gm: ''
                        }]
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