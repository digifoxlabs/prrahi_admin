@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.orders._breadcrump')


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
          



                      <div class="mx-auto w-full max-w-6xl" x-data="orderTableComponent()">




                    <!-- Action Bar -->
<div
    class="mb-4 rounded-xl border border-gray-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 p-3 dark:border-gray-800 dark:bg-black/30 md:static md:border-0 md:bg-transparent md:p-0">

    <!-- Mobile: stacked | Desktop: row -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:flex-nowrap">

        <!-- Left: Search + Status -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:flex-1 min-w-0">

            <!-- Search -->
            <div class="relative w-full sm:w-64 md:w-80 lg:w-96">
                <input type="text" x-model="search" @input="updateQuery"
                    placeholder="Search by Order Number..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-transparent dark:text-white/90" />
                <button type="button" @click="search = ''; updateQuery()" x-show="search"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-800 dark:text-gray-300"
                    title="Clear search">
                    ✕
                </button>
            </div>


        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0 whitespace-nowrap">

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
                    <a href="{{ route('admin.distributors.export') }}?search="
                        x-bind:href="'{{ route('admin.distributors.export') }}?search=' + encodeURIComponent(search)"
                        @click="open = false"
                        class="block px-4 py-2 text-sm hover:bg-gray-50 focus:bg-gray-50 dark:hover:bg-neutral-800"
                        role="menuitem" tabindex="-1">
                        Export All
                    </a>
                </div>
            </div>


        </div>
    </div>
</div>


                    <div class="overflow-x-auto">

                        <table id="ordersTable" class="min-w-full border border-gray-200 dark:border-gray-700 text-sm dark:text-white/90 table-auto md:table-fixed">
                            <thead class="bg-gray-10 text-xs uppercase">
                                <tr class="dark:text-black/90">
                                    <th class="px-4 py-3">#</th>
                                    <th class="px-4 py-3">Order No</th>                                    
                                    <th class="px-4 py-3">Order</th>                                    
                                    <th class="px-4 py-3 text-center">Invoice</th>
                                    <th class="px-4 py-3 text-center">Created At</th>
                                    <th class="px-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $index => $invoice)
                                    <tr class="border-b hover:bg-gray-50 text-center" x-data="{ showModal: false, deleteUrl: '' }">
                                        <td class="border px-4 py-3">{{ $invoices->firstItem() + $index }}</td>

                                        <td class="border px-4 py-3">{{ $invoice->order_number }}</td>

                                        <td class="border px-4 py-3">
                                            @if($invoice->order)
                                            <a href="{{ route('admin.orders.show', $invoice->order->id) }}" 
                                            class="text-blue-600 text-xs ml-2 hover:underline">View Order</a>
                                            @endif


                                        </td>

                                        <td class="border px-4 py-3">
                                            @if($invoice->order)
                                            <a href="{{ route('admin.tally.invoices.show', $invoice->id) }}" 
                                            class="text-blue-600 text-xs ml-2 hover:underline">View Invoice</a>
                                            @endif


                                        </td>


                                         <td class="border px-3 py-2">{{ $invoice->created_at->format('d-m-Y H:i') }}</td>

                                         <td class="border px-3 py-2">
                                            

                                                    <form action="{{ route('admin.tally.invoices.destroy', $invoice->id) }}" 
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Delete this invoice?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                                    </form>


                                         </td>                        
                            


                           
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-3 text-center text-gray-400">No Invoices
                                            found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $invoices->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'invoices',
        };
    </script>
@endpush


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>


    <script>
        function orderTableComponent() {
            return {
                search: @json($search ?? ''),
                status: @json($status ?? ''),

                updateQuery() {
                    const params = new URLSearchParams(window.location.search);
                    if (this.search) {
                        params.set('search', this.search);
                    } else {
                        params.delete('search');
                    }
                    if (this.status) {
                        params.set('status', this.status);
                    } else {
                        params.delete('status');
                    }
                    window.location.search = params.toString();
                },

                exportToExcel() {

                    const table = document.getElementById('ordersTable');

                    const selectedColumnIndexes = [0, 1, 2, 3, 4, 5, 6]; // column index

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
                        sheet: "Distributors"
                    });
                    XLSX.writeFile(wb, "distributors.xlsx");

                }
            }
        }
    </script>
@endpush
