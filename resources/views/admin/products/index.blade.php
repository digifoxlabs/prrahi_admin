{{-- resources/views/admin/products/index.blade.php --}}
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

    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">

        <!-- Grid container root -->
        <div class="mx-auto w-full max-w-6xl">

            {{-- Action Bar (search + create + export) --}}
            <div x-data="{
                    search: new URLSearchParams(window.location.search).get('search') || '',
                    exportAllUrl: '{{ route('admin.products.export') }}?search=' + encodeURIComponent(new URLSearchParams(window.location.search).get('search') || ''),
                    updateQuery() {
                        const url = new URL(window.location.href);
                        if (this.search && this.search.length > 0) url.searchParams.set('search', this.search);
                        else url.searchParams.delete('search');
                        url.searchParams.delete('page');
                        window.location.href = url.toString();
                    },
                    clearSearch() { this.search = ''; this.updateQuery(); },
                    exportToExcel() {
                        const rows = [['Name','Code','Type','Category/Sub','Base Unit','Base Qty','Stock','MRP','PTD','Distributor Discount','PTR','Retailer Discount']];
                        document.querySelectorAll('.product-card').forEach(card => {
                            const d = card.dataset;
                            rows.push([d.name||'',d.code||'',d.type||'',d.category||'',d.baseUnit||'',d.baseQty||'',d.stock||'',d.mrp||'',d.ptd||'',d.distDiscount||'',d.ptr||'',d.retailDiscount||'']);
                        });
                        const ws = XLSX.utils.aoa_to_sheet(rows);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Products');
                        XLSX.writeFile(wb, 'products_page.xlsx');
                    }
                }" class="mb-4 rounded-xl border border-gray-200 bg-white/90 p-3">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:flex-nowrap">

                    <!-- Search -->
                    <div class="w-full sm:w-auto sm:flex-1 min-w-0">
                        <label for="product-search" class="sr-only">Search products</label>
                        <div class="relative w-full sm:w-64 md:w-80 lg:w-96">
                            <input id="product-search" type="text" x-model="search" @input.debounce.400ms="updateQuery"
                                placeholder="Search products or variants..."
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <button type="button" @click="clearSearch" x-show="search.length"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-800"
                                title="Clear search" aria-label="Clear search">✕</button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 sm:gap-3 sm:ml-4 shrink-0 whitespace-nowrap">
                        <div x-data="{ open:false }" class="relative">
                            <button @click="open = !open" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-3 py-2 text-sm text-white">
                                Export
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M5 20h14v-2H5v2ZM5 4v8h4v4h6v-4h4V4H5Zm6 10v-4h2v4h-2Zm6-6H7V6h10v2Z"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open=false" x-cloak class="absolute right-0 mt-2 w-56 rounded-lg border bg-white shadow z-50">
                                <button @click="exportToExcel(); open=false" class="block w-full px-4 py-2 text-left text-sm">Export Current Page</button>
                                <a :href="exportAllUrl" class="block px-4 py-2 text-sm">Export All</a>
                            </div>
                        </div>

                        @if (Auth::guard('admin')->user()->hasPermission('create_products'))
                            <a href="{{ route('admin.products.create') }}"
                                class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm text-white">+ Create</a>
                        @endif
                    </div>
                </div>
            </div>

            <p class="mb-2 text-sm text-gray-600">Total Products: {{ $products->total() }} | Pages: {{ $products->lastPage() }}</p>

            {{-- Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($products as $product)
                    @php
                        $categoryLabel = trim(
                            ($product->category->name ?? '') .
                            ((isset($product->subCategory) && $product->subCategory->name) ? ' / ' . $product->subCategory->name : '')
                        );
                        $categoryLabel = $categoryLabel !== '' ? $categoryLabel : '-';
                    @endphp

                    <div class="product-card rounded-xl border p-4 bg-white shadow-sm overflow-visible"
                         data-name="{{ $product->name ?? '-' }}"
                         data-code="{{ $product->code ?? '-' }}"
                         data-type="{{ $product->type }}"
                         data-category="{{ $categoryLabel }}"
                         data-base-unit="{{ $product->base_unit ?? '' }}"
                         data-base-qty="{{ $product->base_quantity ?? '' }}"
                         data-stock="{{ $product->total_stock ?? 0 }}"
                         data-mrp="{{ $product->mrp_per_unit ?? '' }}"
                         data-ptd="{{ $product->ptd_per_dozen ?? '' }}"
                         data-dist-discount="{{ $product->distributor_discount_percent ?? '' }}"
                         data-ptr="{{ $product->ptr_per_dozen ?? '' }}"
                         data-retail-discount="{{ $product->retailer_discount_percent ?? '' }}">
                        
                        <div class="flex items-start justify-between" x-data="{ actionsOpen:false }">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-semibold truncate">{{ $product->name ?? '-' }}</h3>
                                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">{{ ucfirst($product->type) }}</span>
                                </div>

                                <p class="text-xs text-gray-500">Code: <span class="font-medium">{{ $product->code ?? '-' }}</span></p>
                                <p class="text-xs text-gray-500">Category: <span class="font-medium">{{ $categoryLabel }}</span></p>
                                <p class="text-xs text-gray-500">Base: <span class="font-medium">
                                    @if ($product->base_unit && $product->base_quantity) {{ strtoupper($product->base_unit) }}/{{ $product->base_quantity }} @else N/A @endif
                                </span></p>

                                @if ($product->type === 'simple')
                                    <div class="mt-2 grid grid-cols-2 gap-1 text-xs text-gray-600">
                                        <div>Stock: <strong>{{ $product->total_stock ?? 0 }}</strong></div>
                                        <div>MRP: <strong>{{ $product->mrp_per_unit ?? '-' }}</strong></div>
                                        <div>PTD: <strong>{{ $product->ptd_per_dozen ?? '-' }}</strong></div>
                                        <div>Dist. Disc: <strong>{{ $product->distributor_discount_percent ? $product->distributor_discount_percent.'%' : '-' }}</strong></div>
                                        <div>PTR: <strong>{{ $product->ptr_per_dozen ?? '-' }}</strong></div>
                                        <div>Retail Disc: <strong>{{ $product->retailer_discount_percent ? $product->retailer_discount_percent.'%' : '-' }}</strong></div>
                                    </div>
                                @endif

                                @if ($product->type === 'variable')
                                    <div class="mt-3 flex gap-2">
                                        <a href="{{ route('admin.products.add-variant', $product->id) }}?redirect_to={{ urlencode(url()->current()) }}" class="text-xs px-2 py-1 border rounded">+ Add Variant</a>

                                        <!-- Open new page to view variants -->
                                        <a href="{{ route('admin.products.variants', $product->id) }}"
                                            class="text-xs px-2 py-1 border rounded">View Variants ({{ $product->variants->count() }})</a>
                                    </div>
                                @endif
                            </div>

                            <!-- actions dropdown -->
                            <div class="relative">
                                <button @click.prevent.stop="actionsOpen = !actionsOpen" class="p-1 rounded hover:bg-gray-100">
                                    <svg class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor"><path d="M6 10a2 2 0 100-4 2 2 0 000 4zm4 0a2 2 0 100-4 2 2 0 000 4zm4 0a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                </button>

                                <div x-show="actionsOpen" x-cloak @click.outside="actionsOpen=false" class="absolute right-0 mt-2 w-40 rounded border bg-white shadow z-50" style="display:none" @click.stop>
                                    @include('admin.products._actions', ['product' => $product])
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $products->withQueryString()->links('vendor.pagination.tailwind') }}
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.pageXData = { page: 'products' };
    </script>
@endpush

@push('scripts')
<!-- xlsx for export -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@endpush
