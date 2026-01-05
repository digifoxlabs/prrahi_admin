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
            

                 <div class="mx-auto w-full max-w-6xl" x-data="productTableComponent()">   


                 <!-- Action Bar (flex-only, desktop-proof) -->
                <div
                    class="mb-4 rounded-xl border border-gray-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 p-3 dark:border-gray-800 dark:bg-black/30 md:static md:border-0 md:bg-transparent md:p-0">

                    <!-- Mobile: stacked | Desktop: no-wrap single row -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:flex-nowrap">

                        <!-- Search (left) -->
                        <div class="w-full sm:w-auto sm:flex-1 min-w-0">
                            <label for="user-search" class="sr-only">Search products</label>
                            <div class="relative w-full sm:w-64 md:w-80 lg:w-96">
                                <input id="user-search" type="text" x-model="search" @input="updateQuery"
                                    placeholder="Search products..."
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-transparent dark:text-white/90" />
                                <!-- Clear Button -->
                                <button type="button" @click="search = ''; updateQuery()" x-show="search"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-800 dark:text-gray-300"
                                    title="Clear search" aria-label="Clear search">
                                    ✕
                                </button>
                            </div>
                        </div>


                        <!-- Actions (right) -->
                        <div class="flex items-center gap-2 sm:gap-3 sm:ml-4 shrink-0 whitespace-nowrap">

                            <!-- Export dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-green-600 px-3 py-2 text-sm font-medium text-white shadow hover:bg-green-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-400"
                                    type="button" id="menu-button" aria-expanded="false" aria-haspopup="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                        fill="currentColor" aria-hidden="true">
                                        <path d="M5 20h14v-2H5v2ZM5 4v8h4v4h6v-4h4V4H5Zm6 10v-4h2v4h-2Zm6-6H7V6h10v2Z" />
                                    </svg>
                                    <span class="whitespace-nowrap">Export</span>
                                    <svg class="-mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Dropdown -->
                                <div x-show="open" @click.outside="open = false" x-transition
                                    class="absolute right-0 mt-2 w-56 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg ring-1 ring-black/5 dark:border-gray-700 dark:bg-neutral-900 z-50"
                                    role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                    <button @click="exportToExcel(); open = false"
                                        class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-50 focus:bg-gray-50 dark:hover:bg-neutral-800"
                                        role="menuitem" tabindex="-1">
                                        Export Current Page
                                    </button>
                                    <a href="{{ route('admin.users.export') }}?search="
                                        x-bind:href="'{{ route('admin.products.export') }}?search=' + encodeURIComponent(search)"
                                        @click="open = false"
                                        class="block px-4 py-2 text-sm hover:bg-gray-50 focus:bg-gray-50 dark:hover:bg-neutral-800"
                                        role="menuitem" tabindex="-1">
                                        Export All
                                    </a>
                                </div>
                            </div>


                             @if (Auth::guard('admin')->user()->hasPermission('create_products'))
                            <!-- Create -->
                            <a href="{{ route('admin.products.create') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">

                                <span class="sm:inline">+ Create</span>
                            </a>
                            @endif

                        </div>
                    </div>
                </div>
                    <p class="mb-2 text-sm text-gray-600">Total Products: {{ $products->total() }} | Pages:
                        {{ $products->lastPage() }}</p>

                    <div class="overflow-x-auto">
                        <table id="productsTable"
                            class="min-w-full border text-sm dark:text-white/90 table-auto md:table-fixed">
                            <thead class="bg-gray-10">
                                <tr class="dark:text-black/90 text-center">
                                    <th class="px-2 py-2 border w-12">SL</th>
                                    <th class="px-2 py-2 border min-w-[150px] break-words">Name</th>
                                    <th class="px-2 py-2 border min-w-[120px] break-words">Code</th>
                                    <th class="px-2 py-2 border min-w-[150px] break-words">Category/Sub</th>
                                    <th class="px-2 py-2 border w-24">Type</th>
                                    <th class="px-2 py-2 border w-20">Stock</th>
                                    <th class="px-2 py-2 border w-28">Unit/Qty</th>
                                    <th class="px-2 py-2 border w-28">MRP/Unit</th>
                                    <th class="px-2 py-2 border w-28">PTD</th>
                                    <th class="px-2 py-2 border w-28">Dist Discount</th>
                                    <th class="px-2 py-2 border w-28">PTR</th>
                                    <th class="px-2 py-2 border w-28">Retail Discount</th>
                                    <th class="px-2 py-2 border min-w-[120px]">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sl = ($products->currentPage() - 1) * $products->perPage() + 1; @endphp
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="px-2 py-2 border">{{ $sl++ }}</td>

                                        {{-- Product Name --}}
                                        <td class="px-2 py-2 border">
                                            @if ($product->type == 'variant')
                                                {{ $product->parent->name ?? '-' }}
                                                @php
                                                    $fragrance = $product->attributes['fragrance'] ?? null;
                                                    $size = $product->attributes['size'] ?? null;
                                                @endphp
                                                @if ($fragrance || $size)
                                                    <br>
                                                    <span class="text-gray-500 text-xs">
                                                        ({{ $fragrance ? 'Fragrance: ' . $fragrance : '' }}
                                                        {{ $fragrance && $size ? ' | ' : '' }}
                                                        {{ $size ? 'Size: ' . $size : '' }})
                                                    </span>
                                                @endif
                                            @else
                                                {{ $product->name ?? '-' }}
                                            @endif
                                        </td>

                                        {{-- Product Code --}}
                                        <td class="px-2 py-2 border text-center">{{ $product->code }}</td>

                                        {{-- Category / Sub Category --}}
                                        <td class="px-2 py-2 border">
                                            {{ $product->type == 'variant'
                                                ? ($product->parent->category->name ?? '') . (isset($product->parent->subCategory) ? ' / ' . $product->parent->subCategory->name : '')
                                                : ($product->category->name ?? '') . (isset($product->subCategory) ? ' / ' . $product->subCategory->name : '') }}
                                        </td>

                                        {{-- Type --}}
                                        <td class="px-2 py-2 border capitalize">{{ $product->type }}</td>

                                        {{-- Stock --}}
                                        <td class="px-2 py-2 border text-center">
                                            {{ $product->total_stock ?? 0 }}
                                        </td>

                                        {{-- Unit/Qty  --}}
                                        <td class="px-2 py-2 border text-center">
                                            @if ($product->base_unit)    
                                            {{ strtoupper($product->base_unit) }}/{{ $product->base_quantity }} 
                                            @else N/A
                                            @endif
                                        
                                        </td>

                                        {{-- MRP --}}
                                        <td class="px-2 py-2 border text-center">{{ $product->mrp_per_unit }}</td>

                                        {{-- PTD --}}
                                        <td class="px-2 py-2 border text-center">{{ $product->ptd_per_dozen }}</td>
                                        <td class="px-2 py-2 border text-center">{{ $product->distributor_discount_percent }}%</td>
                                        {{-- PTR --}}
                                        <td class="px-2 py-2 border text-center">{{ $product->ptr_per_dozen }}</td>
                                        <td class="px-2 py-2 border text-center">{{ $product->retailer_discount_percent }} %</td>


                                        {{-- Actions --}}
                                        <td class="px-2 py-2 border text-center" x-data="{ open: false }">
                                            @include('admin.products._actions', ['product' => $product])
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $products->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
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
        <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

        <script>
            function productTableComponent() {
                return {
                    search: new URLSearchParams(window.location.search).get('search') || '',
                    updateQuery() {
                        const url = new URL(window.location.href);
                        if (this.search) {
                            url.searchParams.set('search', this.search);
                        } else {
                            url.searchParams.delete('search');
                        }
                        url.searchParams.delete('page'); // Reset to first page on search
                        window.location.href = url.toString();
                    },
                    exportToExcel() {

                        const table = document.getElementById('productsTable');

                        const selectedColumnIndexes = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]; // column index

                        // Clone table to avoid modifying the original
                        const clonedTable = table.cloneNode(true);

                        // Remove unwanted columns from header
                        const headerRow = clonedTable.querySelector('thead tr');
                        Array.from(headerRow.cells).forEach((cell, index) => {
                            if (!selectedColumnIndexes.includes(index)) {
                                cell.remove();
                            }
                        });

                        // Remove unwanted columns from body
                        const bodyRows = clonedTable.querySelectorAll('tbody tr');
                        bodyRows.forEach(row => {
                            Array.from(row.cells).forEach((cell, index) => {
                                if (!selectedColumnIndexes.includes(index)) {
                                    cell.remove();
                                }
                            });
                        });

                        const wb = XLSX.utils.table_to_book(clonedTable, {
                            sheet: "Products"
                        });
                        XLSX.writeFile(wb, "products.xlsx");

                    }
                }
            }
        </script>
    @endpush
