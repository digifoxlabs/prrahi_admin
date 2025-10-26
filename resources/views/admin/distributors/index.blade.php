@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.distributors._breadcrump')


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


            <div class="mx-auto w-full max-w-6xl" x-data="distributorTableComponent()">

                <!-- Action Bar (flex-only, desktop-proof) -->
                <div
                    class="mb-4 rounded-xl border border-gray-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 p-3 dark:border-gray-800 dark:bg-black md:static md:border-0 md:bg-transparent md:p-0">

                    <!-- Mobile: stacked | Desktop: no-wrap single row -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:flex-nowrap">

                        <!-- Search (left) -->
                        <div class="w-full sm:w-auto sm:flex-1 min-w-0">
                            <label for="user-search" class="sr-only">Search distributors</label>
                            <div class="relative w-full sm:w-64 md:w-80 lg:w-96">
                                <input id="user-search" type="text" x-model="search" @input="updateQuery"
                                    placeholder="Search distributors..."
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
                                    <a href="{{ route('admin.distributors.export') }}?search="
                                        x-bind:href="'{{ route('admin.distributors.export') }}?search=' + encodeURIComponent(search)"
                                        @click="open = false"
                                        class="block px-4 py-2 text-sm hover:bg-gray-50 focus:bg-gray-50 dark:hover:bg-neutral-800"
                                        role="menuitem" tabindex="-1">
                                        Export All
                                    </a>
                                </div>
                            </div>


                            @if (Auth::guard('admin')->user()->hasPermission('create_distributors'))
                                <!-- Create -->
                                <a href="{{ route('admin.distributors.create') }}"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">

                                    <span class="sm:inline">+ Create</span>
                                </a>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table id="permissionsTable"
                        class="min-w-full border text-sm table-auto md:table-fixed text-gray-900 dark:text-white/90 border-gray-200 dark:border-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-700 dark:text-white">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Profile</th>
                                <th class="px-4 py-3">Firm</th>
                                <th class="px-4 py-3">Contact person</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Phone</th>
                                <th class="px-4 py-3">Address</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($distributors as $index => $distributor)
                                <tr class="border-b border-gray-200 dark:border-gray-700" x-data="{ showModal: false, deleteUrl: '' }">
                                    <td class="px-4 py-3">{{ $distributors->firstItem() + $index }}</td>
                                    <td class="px-4 py-3">
                                        <img src="{{ $distributor->profile_photo ? asset('storage/' . $distributor->profile_photo) : asset('images/user/user-01.jpg') }}"
                                            alt="Profile"
                                            class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                                    </td>
                                    <td class="px-4 py-3">{{ $distributor->firm_name }}</td>
                                    <td class="px-4 py-3">{{ $distributor->contact_person }}</td>
                                    <td class="px-4 py-3">{{ $distributor->email }}</td>
                                    <td class="px-4 py-3">{{ $distributor->contact_number }}</td>
                                    <td class="px-4 py-3">{{ $distributor->town . '/' . $distributor->state }}</td>


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
                                                    // compute left so right edge aligns to button's right edge
                                                    let left = rect.right - menuWidth;
                                                    const margin = 8; // safe margin from viewport edges
                                                    if (left < margin) left = margin;
                                                    // set top just below the button
                                                    let top = rect.bottom + 8;
                                                    // estimate available space and adjust maxHeight to prevent overflow
                                                    const availableBelow = window.innerHeight - top - margin;
                                                    const menuMax = Math.min(224, Math.max(120, availableBelow)); // 14rem = 224px
                                                    // If not enough space below, try to show above the button
                                                    if (availableBelow < 140) {
                                                        const aboveTop = rect.top - 8 - menuMax;
                                                        if (aboveTop > margin) {
                                                            top = rect.top - 8 - menuMax; // place above
                                                        } else {
                                                            // fallback: clamp top so menu stays inside viewport
                                                            top = Math.max(margin, Math.min(top, window.innerHeight - menuMax - margin));
                                                        }
                                                    }
                                                    this.dropdownStyle = `left: ${left}px; top: ${top}px; width: ${menuWidth}px; max-height: ${menuMax}px; overflow:auto;`;
                                                    // close on scroll (optional)
                                                    const onScroll = () => { this.open = false;
                                                        window.removeEventListener('scroll', onScroll, true); };
                                                    window.addEventListener('scroll', onScroll, true);
                                                });
                                            }
                                        }
                                    }"
                                        @keydown.escape.window="open = false">
                                        <div class="inline-block text-left">
                                            <button x-ref="actionsBtn" @click="openDropdown()" type="button"
                                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium
                                                text-gray-700 dark:text-white
                                                bg-white dark:bg-gray-800
                                                border border-gray-300 dark:border-gray-700
                                                rounded-md shadow-sm
                                                hover:bg-gray-100 hover:text-gray-900
                                                dark:hover:bg-gray-700 dark:hover:text-gray-700
                                                focus:outline-none focus:ring-2 focus:ring-blue-500
                                                transition">
                                                Actions
                                                <svg class="w-4 h-4 ml-1 text-gray-500 dark:text-gray-400 transition-colors"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Viewport-mounted dropdown (fixed) -->
                                        <div x-cloak x-show="open" x-transition @click.away="open = false"
                                            :style="dropdownStyle"
                                            class="fixed rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 dark:ring-gray-700 z-50"
                                            style="will-change: transform;">
                                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                                <!-- View -->
                                                @if (Auth::guard('admin')->user()->hasPermission('view_distributors'))
                                                    <li>
                                                        <a href="{{ route('admin.distributors.show', $distributor) }}"
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

                                                <!-- Edit -->
                                                @if (Auth::guard('admin')->user()->hasPermission('edit_distributors'))
                                                    <li>
                                                        <a href="{{ route('admin.distributors.edit', $distributor) }}"
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

                                                <!-- Delete -->
                                                @if (Auth::guard('admin')->user()->hasPermission('delete_distributors'))
                                                    <li>
                                                        <button
                                                            @click.prevent="deleteUrl = '{{ route('admin.distributors.destroy', $distributor) }}'; open = false; showModal = true;"
                                                            class="w-full text-left flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-red-600 dark:hover:text-red-400 text-red-500 dark:text-red-400 transition">
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

                                                <!-- Modal -->
                                                <div class="relative bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-md p-6 z-[101] border border-gray-200 dark:border-gray-700"
                                                    @click.stop>
                                                    <h2 class="text-lg font-semibold mb-4 text-red-600 dark:text-red-400">
                                                        Confirm Deletion</h2>
                                                    <p class="mb-6 text-gray-700 dark:text-gray-300">
                                                        Are you sure you want to delete the Distributor:
                                                        <strong
                                                            class="text-gray-900 dark:text-white">{{ $distributor->firstname }}</strong>?
                                                    </p>

                                                    <div class="flex justify-end space-x-3">
                                                        <button @click="showModal = false"
                                                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                                            Cancel
                                                        </button>

                                                        <form :action="deleteUrl" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 dark:hover:bg-red-500 transition">
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
                                    <td colspan="6" class="px-4 py-3 text-center text-gray-400 dark:text-gray-500">No
                                        Distributors found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>




                <div class="mt-4">
                    {{ $distributors->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            </div>


        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'distributors',
        };
    </script>
@endpush


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        function distributorTableComponent() {
            return {
                search: '{{ $search ?? '' }}',
                debounceTimeout: null,
                updateQuery() {
                    clearTimeout(this.debounceTimeout);
                    this.debounceTimeout = setTimeout(() => {
                        const base = '{{ route('admin.distributors.index') }}';
                        const query = this.search.trim() ? '?search=' + encodeURIComponent(this.search.trim()) : '';
                        window.location.href = base + query;
                    }, 500);
                },
                exportToExcel() {
                    const table = document.getElementById('permissionsTable');
                    const selectedColumnIndexes = [0, 2, 3, 4, 5]; // column index

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
                        sheet: "Distributors"
                    });
                    XLSX.writeFile(wb, "distributors.xlsx");

                }
            };
        }
    </script>
@endpush
