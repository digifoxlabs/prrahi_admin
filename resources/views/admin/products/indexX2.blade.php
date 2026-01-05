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
                                        <a href="{{ route('admin.products.add-variant', $product->id) }}" class="text-xs px-2 py-1 border rounded">+ Add Variant</a>

                                        <button
                                            @click="window.dispatchEvent(new CustomEvent('open-variants-modal',{detail:{productId:{{ $product->id }}}}))"
                                            class="text-xs px-2 py-1 border rounded">View Variants ({{ $product->variants->count() }})</button>
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

            {{-- VARIANTS MODAL: single Alpine instance using inline x-data, products passed from paginator items --}}
            <div
                x-data="{
                    modalOpen: false,
                    currentProduct: null,
                    productsMap: {},
                    init() {
                        // initialize map from blade-passed data-products
                        const list = JSON.parse(this.$el.getAttribute('data-products') || '[]');
                        list.forEach(p => {
                            // normalize
                            p.variants = p.variants || [];
                            try {
                                if (typeof p.attributes === 'string') p.attributes = JSON.parse(p.attributes);
                            } catch(e) { p.attributes = {}; }
                            p.variants.forEach(v => {
                                try {
                                    if (typeof v.attributes === 'string') v.attributes = JSON.parse(v.attributes);
                                } catch(e) { v.attributes = {}; }
                            });
                            this.productsMap[p.id] = p;
                        });

                        // window event listener
                        this._handler = (e) => {
                            const pid = e && e.detail ? e.detail.productId : undefined;
                            if (pid !== undefined) {
                                this.currentProduct = this.productsMap[pid] || null;
                                this.modalOpen = !!this.currentProduct;
                                if (this.modalOpen) {
                                    setTimeout(()=>{ document.getElementById('variantsModalTitle')?.scrollIntoView({behavior:'smooth'}); }, 50);
                                }
                            }
                        };
                        window.addEventListener('open-variants-modal', this._handler);
                    },
                    // cleanup if the element is removed
                    destroy() {
                        if (this._handler) window.removeEventListener('open-variants-modal', this._handler);
                    },
                    close() { this.modalOpen = false; this.currentProduct = null; }
                }"
                data-products='@json($products->items())'
                x-init="init()"
                class="relative"
            >


    <!-- overlay — VERY high z-index -->
    <div x-show="modalOpen" x-cloak style="display:none" @class(['fixed', 'inset-0' , 'z-[9999999]' , 'bg-black/40'
     , 'flex' , 'items-start' , 'justify-center' , 'px-4' , 'py-8' ])>

     <!-- inner modal — still relative but higher than overlay siblings -->
     <div @class(['bg-white', 'max-w-3xl' , 'w-full' , 'rounded-xl' , 'shadow-2xl' , 'p-5' , 'max-h-[85vh]'
         , 'overflow-y-auto' , 'relative' , 'z-[10000000]' ]) role="dialog" aria-modal="true"
         aria-labelledby="variantsModalTitle" @click.outside="close()">

         <!-- Header: product name (left) + close button (right) -->
         <div class="flex items-start justify-between mb-3">
             <h2 id="variantsModalTitle" class="text-base font-semibold text-gray-800" x-text="currentProduct?.name || 'Variants'"></h2>
             <button @click="close()" class="ml-3 p-1.5 rounded-full hover:bg-gray-100 text-red-600" aria-label="Close variants modal">✕</button>
         </div>

         <div class="grid grid-cols-1 sm:grid-cols-2 gap-3"
             x-show="currentProduct && currentProduct.variants && currentProduct.variants.length > 0">
             
             <template x-for="variant in currentProduct.variants" :key="variant.id">
                 <div class="border rounded-lg bg-gray-50 p-3 text-xs text-gray-700" @click.stop>
                     <div class="flex items-start justify-between mb-2">
                         <div>
                             {{-- <div class="font-semibold" x-text="variant.name || currentProduct.name"></div> --}}
                             <div class="font-semibold">Code: <span x-text="variant.code"></span></div>
                             <div class="text-[11px] text-gray-500"
                                 x-show="variant.attributes?.fragrance || variant.attributes?.size">
                                 (<span
                                     x-text="variant.attributes?.fragrance ? 'Fragrance: ' + variant.attributes.fragrance : ''"></span>
                                 <span x-show="variant.attributes?.fragrance && variant.attributes?.size"> | </span>
                                 <span
                                     x-text="variant.attributes?.size ? 'Size: ' + variant.attributes.size : ''"></span>)
                             </div>
                         </div>
                         <div class="text-right">
                             <div class="text-[11px] text-gray-500">Stock</div>
                             <div class="font-semibold text-xs" x-text="variant.total_stock ?? 0"></div>
                         </div>
                     </div>

                     <div class="grid grid-cols-2 gap-2">
                         <div><span class="text-[11px]">MRP:</span> <span class="font-semibold"
                                 x-text="variant.mrp_per_unit ?? '-'"></span></div>
                         <div><span class="text-[11px]">PTD:</span> <span class="font-semibold"
                                 x-text="variant.ptd_per_dozen ?? '-'"></span></div>
                         <div><span class="text-[11px]">Dist Disc:</span> <span class="font-semibold"
                                 x-text="variant.distributor_discount_percent != null ? (variant.distributor_discount_percent + '%') : '-'"></span>
                         </div>
                         <div><span class="text-[11px]">PTR:</span> <span class="font-semibold"
                                 x-text="variant.ptr_per_dozen ?? '-'"></span></div>
                         <div><span class="text-[11px]">Retail Disc:</span> <span class="font-semibold"
                                 x-text="variant.retailer_discount_percent != null ? (variant.retailer_discount_percent + '%') : '-'"></span>
                         </div>
                     </div>

                     <div class="mt-3 flex gap-2">
                         <a :href="`/admin/products/${variant.id}/edit`"
                             class="px-2 py-1 border rounded text-gray-700 text-xs hover:bg-white" @click.stop
                             target="_blank">Edit</a>

                         <form :action="`/admin/products/${variant.id}`" method="POST" @click.stop>
                             @csrf
                             @method('DELETE')
                             <button type="button"
                                 @click="if(confirm('Delete this variant?')) $event.target.closest('form').submit()"
                                 class="px-2 py-1 border border-red-300 rounded text-red-700 text-xs hover:bg-red-50">Delete</button>
                         </form>
                     </div>
                 </div>
             </template>
         </div>
     </div>
 </div>
 {{-- end variants modal --}}



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

@push('scripts')
<!-- xlsx for export -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<!-- small runtime check helper: open console and run Alpine.version to confirm -->
<script>
    (function(){
        try {
            if (!window.Alpine) console.warn('Alpine is not loaded. Ensure Alpine v3.x is included before these scripts.');
        } catch(e) {}
    })();
</script>
@endpush
