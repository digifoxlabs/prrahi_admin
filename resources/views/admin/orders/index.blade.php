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
                    class="mb-4 rounded-xl border border-gray-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 p-3 dark:border-gray-800 dark:bg-black md:static md:border-0 md:bg-transparent md:p-0">

                    <!-- Mobile: stacked | Desktop: row -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:flex-nowrap">

                        <!-- Left: Search + Status -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:flex-1 min-w-0">

                            <!-- Search -->
                            <div class="relative w-full sm:w-64 md:w-80 lg:w-96">
                                <input type="text" x-model="search" @input="updateQuery"
                                    placeholder="Search by Order No, Distributor..."
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-transparent dark:text-white/90" />
                                <button type="button" @click="search = ''; updateQuery()" x-show="search"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-800 dark:text-gray-300"
                                    title="Clear search">
                                    ✕
                                </button>
                            </div>

                            <!-- Status Filter -->
                            <div class="relative w-full sm:w-48">
                                <select x-model="status" @change="updateQuery"
                                    class="w-full rounded-lg border border-gray-300 
                                        px-4 py-2.5 text-sm 
                                        focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 
                                        dark:focus:ring-blue-400 dark:focus:border-blue-400
                                        transition">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
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

                            @if (Auth::guard('admin')->user()->hasPermission('create_orders'))
                                <!-- Create -->
                                <a href="{{ route('admin.orders.create') }}"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                                    + Create
                                </a>
                            @endif
                        </div>
                    </div>
                </div>


                <div class="overflow-x-auto">

                    <table id="ordersTable" class="min-w-full border text-sm table-auto md:table-fixed text-gray-900 dark:text-white/90 border-gray-200 dark:border-gray-700">
                        <thead class="bg-gray-10 text-xs uppercase">
                            <tr class="dark:text-black/90">
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Order No</th>
                                <th class="px-4 py-3">Distributor</th>
                                <th class="px-4 py-3">Total AMount</th>
                                <th class="px-4 py-3">Order Date</th>
                                <th class="px-4 py-3">Created By</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $index => $order)
                                <tr class="border-b hover:bg-gray-500" x-data="{ showModal: false, deleteUrl: '' }">
                                    <td class="px-4 py-3">{{ $orders->firstItem() + $index }}</td>

                                    <td class="px-4 py-3">{{ $order->order_number }}</td>
                                    <td class="px-4 py-3">{{ $order->distributor->firm_name }}</td>
                                    <td class="px-4 py-3">{{ $order->total_amount }}</td>
                                    <td class="px-4 py-3">{{ $order->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3">

                                        @php

                                            $creator = $order->created_by;
                                            if ($order->created_by_type === \App\Models\User::class) {
                                                echo 'Admin: ' . optional($creator)->fname;
                                            } elseif ($order->created_by_type === \App\Models\Distributor::class) {
                                                echo 'Distributor: ' . optional($creator)->name;
                                            } elseif ($order->created_by_type === \App\Models\SalesPerson::class) {
                                                echo 'SalesPerson: ' . optional($creator)->name;
                                            } else {
                                                echo '—';
                                            }

                                        @endphp

                                    </td>
                                    <td class="px-4 py-3">

                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $order->status === 'confirmed'
                                                ? 'bg-green-100 text-green-800'
                                                : ($order->status === 'pending'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>

                                    </td>



                                    <!-- Replace your current Actions <td> with this block -->
<td class="px-4 py-3 text-right" x-data="{
    open: false,
    showModal: false,
    deleteUrl: '',
    dropdownStyle: '',
    openDropdown() {
        this.open = !this.open;
        if (this.open) {
            this.$nextTick(() => {
                const rect = this.$refs.actionsBtn.getBoundingClientRect();
                const menuWidth = 160; // px (same as w-40)
                let left = rect.right - menuWidth;
                const margin = 8; 
                if (left < margin) left = margin;
                let top = rect.bottom + 8;
                const availableBelow = window.innerHeight - top - margin;
                const menuMax = Math.min(224, Math.max(120, availableBelow));
                if (availableBelow < 140) {
                    const aboveTop = rect.top - 8 - menuMax;
                    if (aboveTop > margin) {
                        top = rect.top - 8 - menuMax; 
                    } else {
                        top = Math.max(margin, Math.min(top, window.innerHeight - menuMax - margin));
                    }
                }
                this.dropdownStyle = `left: ${left}px; top: ${top}px; width: ${menuWidth}px; max-height: ${menuMax}px; overflow:auto;`;
                const onScroll = () => { 
                    this.open = false;
                    window.removeEventListener('scroll', onScroll, true); 
                };
                window.addEventListener('scroll', onScroll, true);
            });
        }
    }
}" @keydown.escape.window="open = false">
    <div class="inline-block text-left">
        <button x-ref="actionsBtn" @click="openDropdown()" type="button"
            class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium 
                   text-gray-700 dark:text-gray-400 
                   bg-white dark:bg-gray-800 
                   border border-gray-300 dark:border-gray-700 
                   rounded-md shadow-sm 
                   hover:bg-gray-100 hover:text-gray-900 
                   dark:hover:bg-gray-700 dark:hover:text-white 
                   focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            Actions
            <svg class="w-4 h-4 ml-1 text-gray-500 dark:text-gray-400" fill="none"
                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <!-- Fixed dropdown -->
    <div x-cloak x-show="open" x-transition @click.away="open = false"
        :style="dropdownStyle"
        class="fixed rounded-md shadow-lg 
               bg-white dark:bg-gray-800 
               ring-1 ring-black ring-opacity-5 dark:ring-gray-700 
               z-50"
        style="will-change: transform;">
        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
            @if (Auth::guard('admin')->user()->hasPermission('view_orders'))
                <li>
                    <a href="{{ route('admin.orders.show', $order) }}"
                        class="flex items-center px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-blue-600 transition">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none"
                            stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        View
                    </a>
                </li>
            @endif

            @if (Auth::guard('admin')->user()->hasPermission('edit_orders'))
                <li>
                    <a href="{{ route('admin.orders.edit', $order) }}"
                        class="flex items-center px-4 py-2 text-sm text-green-600 dark:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-green-600 transition">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none"
                            stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11 5H6a2 2 0 00-2 2v11c0 1.1.9 2 2 2h11a2 2 0 002-2v-5m-5.586-6.586a2 2 0 112.828 2.828L11 15H8v-3l5.586-5.586z" />
                        </svg>
                        Edit
                    </a>
                </li>
            @endif

            @if (Auth::guard('admin')->user()->hasPermission('delete_orders'))
                <li>
                    <button
                        @click.prevent="deleteUrl = '{{ route('admin.orders.destroy', $order) }}'; open = false; showModal = true;"
                        class="w-full text-left flex items-center px-4 py-2 
                               text-red-500 dark:text-red-400 
                               hover:bg-gray-100 hover:text-red-600 
                               dark:hover:bg-gray-700 dark:hover:text-red-400 transition">
                        <svg class="w-4 h-4 mr-2" fill="none"
                            stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Delete
                    </button>
                </li>
            @endif
        </ul>
    </div>

    <!-- Delete Confirmation Modal -->
    <template x-if="showModal">
        <div x-show="showModal" x-transition
            class="fixed inset-0 z-[100] flex items-center justify-center"
            style="height: 100vh; width: 100vw;">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gray-700/60 dark:bg-black/70 backdrop-blur-sm"
                @click="showModal = false"></div>

            <!-- Modal Card -->
            <div class="relative bg-white dark:bg-gray-900 rounded-lg shadow-lg 
                        w-full max-w-md p-6 z-[101] 
                        border border-gray-200 dark:border-gray-700"
                @click.stop>
                <h2 class="text-lg font-semibold mb-4 text-red-600 dark:text-red-400">
                    Confirm Deletion
                </h2>
                <p class="mb-6 text-gray-700 dark:text-gray-400">
                    Are you sure you want to delete the Order:
                    <strong class="text-gray-900 dark:text-white">{{ $order->order_number }}</strong>?
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="showModal = false"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 
                               text-gray-800 dark:text-gray-200 
                               rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancel
                    </button>
                    <form :action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded 
                                   hover:bg-red-700 dark:hover:bg-red-500 transition">
                            Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</td>



                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-3 text-center text-gray-400">No Orders
                                        found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $orders->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>


            </div>


        </div>


    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'orders',
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
