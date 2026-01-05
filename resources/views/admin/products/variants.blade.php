{{-- resources/views/admin/products/variants.blade.php --}}
@extends('admin.admin-layout')

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

    {{-- Breadcrumb / header --}}
<div class="mb-4 flex items-center justify-between gap-4">
    <h1 class="text-2xl font-semibold text-gray-900 truncate">
        {{ $product->name ?? 'Product' }}
    </h1>

    <a href="{{ route('admin.products.index') }}"
       class="inline-flex items-center text-sm text-blue-600 hover:underline whitespace-nowrap">
        <!-- optional left arrow icon -->
        <svg class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M9.707 14.707a1 1 0 01-1.414 0L4.586 11a1 1 0 010-1.414L8.293 5.707a1 1 0 011.414 1.414L7.414 10l2.293 2.293a1 1 0 010 1.414z"/>
        </svg>
        Back to Products
    </a>
</div>


    {{-- Messages --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    {{-- Actions --}}
    <div class="mb-4 flex items-center gap-2">
        @if (Auth::guard('admin')->user()->hasPermission('create_products'))
            <a href="{{ route('admin.products.add-variant', $product->id) }}?redirect_to={{ urlencode(request()->fullUrl()) }}" class="bg-green-600 text-white px-3 py-2 rounded text-xs">+ Add Variant</a>
        @endif
       
    </div>

    {{-- Variants grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($product->variants as $variant)
            <div class="border rounded-xl bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="min-w-0">
                        
                        <div class="font-semibold text-sm truncate">Code: <strong>{{ $variant->code ?? '-' }}</strong></div>
                        @if(!empty($variant->attributes))
                            <div class="text-xs text-gray-500 mt-1">
                                @if(!empty($variant->attributes['fragrance'])) Fragrance: {{ $variant->attributes['fragrance'] }} @endif
                                @if(!empty($variant->attributes['fragrance']) && !empty($variant->attributes['size'])) &nbsp;|&nbsp; @endif
                                @if(!empty($variant->attributes['size'])) Size: {{ $variant->attributes['size'] }} @endif
                            </div>
                        @endif
                    </div>

                    <div class="text-right">
                        <div class="text-[11px] text-gray-500">Stock</div>
                        <div class="font-semibold">{{ $variant->total_stock ?? 0 }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mt-3 text-sm text-gray-700">
                    <div>
                        <div class="text-xs text-gray-500">MRP</div>
                        <div class="font-semibold">{{ $variant->mrp_per_unit ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">PTD</div>
                        <div class="font-semibold">{{ $variant->ptd_per_dozen ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Dist. Disc</div>
                        <div class="font-semibold">{{ $variant->distributor_discount_percent ? $variant->distributor_discount_percent.'%' : '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">PTR</div>
                        <div class="font-semibold">{{ $variant->ptr_per_dozen ?? '-' }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-xs text-gray-500">Retail Disc</div>
                        <div class="font-semibold">{{ $variant->retailer_discount_percent ? $variant->retailer_discount_percent.'%' : '-' }}</div>
                    </div>
                </div>

                <div class="mt-3 flex gap-2">
                    <a href="{{ route('admin.products.edit', $variant->id) }}" class="px-2 py-1 border rounded text-xs">Edit</a>

                    <form action="{{ route('admin.products.destroy', $variant->id) }}" method="POST" onsubmit="return confirm('Delete this variant?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-2 py-1 border border-red-300 rounded text-xs text-red-700">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-sm text-gray-500">No variants found.</div>
        @endforelse
    </div>

    {{-- Optional: pagination if you load variants as paginated in controller --}}
    @if(method_exists($product->variants, 'links'))
        <div class="mt-4">
            {{ $product->variants->links('vendor.pagination.tailwind') }}
        </div>
    @endif
</div>
@endsection
@push('scripts')
    <script>
        window.pageXData = { page: 'products' };
    </script>
@endpush
