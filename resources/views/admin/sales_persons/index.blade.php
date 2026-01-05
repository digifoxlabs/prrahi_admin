@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.sales_persons._breadcrump')

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



            <div class="mx-auto w-full max-w-8xl" x-data="salesTableComponent()">


                <!-- Action Bar (flex-only, desktop-proof) -->
                <div
                    class="mb-4 rounded-xl border border-gray-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 p-3 dark:border-gray-800 dark:bg-black md:static md:border-0 md:bg-transparent md:p-0">

                    <!-- Mobile: stacked | Desktop: no-wrap single row -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:flex-nowrap">

                        <!-- Search (left) -->
                        <div class="w-full sm:w-auto sm:flex-1 min-w-0">
                            <label for="user-search" class="sr-only">Search sales persons</label>
                            <div class="relative w-full sm:w-64 md:w-80 lg:w-96">
                                <input id="user-search" type="text" x-model="search" @input="updateQuery"
                                    placeholder="Search sales persons..."
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
                                    <a href="{{ route('admin.sales-persons.export') }}?search="
                                        x-bind:href="'{{ route('admin.sales-persons.export') }}?search=' + encodeURIComponent(search)"
                                        @click="open = false"
                                        class="block px-4 py-2 text-sm hover:bg-gray-50 focus:bg-gray-50 dark:hover:bg-neutral-800"
                                        role="menuitem" tabindex="-1">
                                        Export All
                                    </a>
                                </div>
                            </div>


                            @if (Auth::guard('admin')->user()->hasPermission('create_sales'))
                                <!-- Create -->
                                <a href="{{ route('admin.sales-persons.create') }}"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">

                                    <span class="sm:inline">+ Create</span>
                                </a>
                            @endif

                        </div>
                    </div>
                </div>


<div class="overflow-x-auto">
    <table id="salesPersonsTable"
        class="min-w-full border text-sm table-auto md:table-fixed text-gray-900 dark:text-white/90 border-gray-200 dark:border-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-700 dark:text-white">
            <tr>
                <th class="p-2">#</th>
                <th class="p-2">Name</th>
                <th class="p-2">Login ID</th>
                <th class="p-2">Designation</th>
                <th class="p-2">Headquarter</th>
                <th class="p-2">District</th>
                <th class="p-2">Town</th>
                <th class="p-2">Zone</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($salesPersons as $sp)
                <tr class="border-t dark:border-gray-700" x-data="{ showModal: false, deleteUrl: '', open: false }">
                    <td class="p-2">{{ $loop->iteration }}</td>
                    <td class="p-2 font-medium">{{ $sp->name }}</td>
                    <td class="p-2">{{ $sp->login_id }}</td>
                    <td class="p-2">{{ $sp->designation ?? '-' }}</td>
                    <td class="p-2">{{ $sp->headquarter ?? '-' }}</td>
                    <td class="p-2">{{ $sp->district ?? '-' }}</td>
                    <td class="p-2">{{ $sp->town ?? '-' }}</td>
                    <td class="p-2">{{ $sp->zone ?? '-' }}</td>


                    <td class="p-2 relative" x-data="{
                    open: false,
                    showModal: false,
                    deleteUrl: '',
                    dropdownStyle: '',
                    toggleDropdown($refs) {
                    this.open = !this.open;
                    if (this.open) {
                    this.$nextTick(() => {
                    const rect = $refs.actionsBtn.getBoundingClientRect();
                    const menuWidth = 160; // matches w-40
                    const margin = 8;

                    let left = rect.right - menuWidth;
                    if (left < margin) left = margin;

                    let top = rect.bottom + 6;
                    const availableBelow = window.innerHeight - top - margin;
                    const menuMax = Math.min(224, Math.max(120, availableBelow));

                    // If not enough space below, show above
                    if (availableBelow < 140) {
                    const aboveTop = rect.top - 6 - menuMax;
                    if (aboveTop > margin) {
                    top = aboveTop;
                    } else {
                    top = Math.max(margin, Math.min(top, window.innerHeight - menuMax - margin));
                    }
                    }

                    this.dropdownStyle = `left:${left}px; top:${top}px; width:${menuWidth}px; max-height:${menuMax}px; overflow:auto;`;

                    // Close on scroll
                    const onScroll = () => {
                    this.open = false;
                    window.removeEventListener('scroll', onScroll, true);
                    };
                    window.addEventListener('scroll', onScroll, true);
                    });
                    }
                    }
                    }" @keydown.escape.window="open = false">

                        <!-- Actions Button -->
                        <button x-ref="actionsBtn" @click="toggleDropdown($refs)" class="bg-gray-200 dark:bg-gray-700 text-sm px-3 py-1 rounded 
                    hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white transition">
                            Actions ▾
                        </button>

                        <!-- Fixed Dropdown -->
                        <div x-cloak x-show="open" x-transition @click.away="open = false" :style="dropdownStyle"
                            class="fixed rounded-md shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 z-[100]">

                            @if (Auth::guard('admin')->user()->hasPermission('view_sales'))
                            <a href="{{ route('admin.sales-persons.show', $sp) }}"
                                class="block px-3 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-blue-500 transition">👁️
                                View</a>
                            @endif

                            @if (Auth::guard('admin')->user()->hasPermission('edit_sales'))
                            <a href="{{ route('admin.sales-persons.edit', $sp) }}"
                                class="block px-3 py-2 text-sm text-green-600 dark:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-green-400 transition">✏️
                                Edit</a>
                            @endif

                            @if (Auth::guard('admin')->user()->hasPermission('delete_sales'))
                            <button
                                @click.prevent="showModal = true; deleteUrl = '{{ route('admin.sales-persons.destroy', $sp) }}'; open = false;"
                                class="w-full text-left px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-red-500 transition">🗑️
                                Delete</button>
                            @endif
                        </div>

                        <!-- Delete Modal -->
                        <template x-if="showModal">
                            <div x-show="showModal" x-transition
                                class="fixed inset-0 flex items-center justify-center z-[100]">
                                <div class="absolute inset-0 bg-gray-700/60 backdrop-blur-sm"
                                    @click="showModal = false"></div>
                                <div
                                    class="relative bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-md p-6 z-[101]">
                                    <h2 class="text-lg font-semibold mb-4 text-red-600 dark:text-red-400">Confirm
                                        Deletion</h2>
                                    <p class="mb-6 text-gray-700 dark:text-white">Delete
                                        <strong>{{ $sp->name }}</strong>?</p>
                                    <div class="flex justify-end space-x-3">
                                        <button @click="showModal = false" class="px-4 py-2 rounded transition
                    bg-gray-200 text-gray-800 hover:bg-gray-300
                    dark:bg-white-700 dark:text-blue-400 
                    dark:hover:bg-blue-600 dark:hover:text-white">
                                            Cancel
                                        </button>

                                        <form :action="deleteUrl" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:hover:bg-red-500 transition">Yes,
                                                Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="9" class="p-4 text-gray-400 dark:text-gray-500">No Sales Persons found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $salesPersons->links() }}</div>
</div>

            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'sales-persons',
        };
    </script>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        function salesTableComponent() {
            return {
                search: '{{ $search ?? '' }}',
                debounceTimeout: null,
                updateQuery() {
                    clearTimeout(this.debounceTimeout);
                    this.debounceTimeout = setTimeout(() => {
                        const base = '{{ route('admin.sales-persons.index') }}';
                        const query = this.search.trim() ? '?search=' + encodeURIComponent(this.search.trim()) : '';
                        window.location.href = base + query;
                    }, 500);
                },
                exportToExcel() {

                    const table = document.getElementById('salesPersonsTable');
                    const selectedColumnIndexes = [0, 1, 2, 3, 4, 5, 6, 7]; // column index

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

                    // Export the cleaned cloned table
                    const wb = XLSX.utils.table_to_book(clonedTable, {
                        sheet: "Sales"
                    });
                    XLSX.writeFile(wb, "sales-persons.xlsx");

                }
            };
        }
    </script>
@endpush
