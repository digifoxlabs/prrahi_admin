@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.users._breadcrump')

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
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-4 py-6 dark:border-gray-800 dark:bg-white/[0.03] md:px-6 md:py-8 xl:px-10 xl:py-12">

            <div class="mx-auto w-full max-w-6xl" x-data="usersComponent()">

                <!-- Action Bar (flex-only, desktop-proof) -->
                <div
                    class="mb-4 rounded-xl border border-gray-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 p-3 dark:border-gray-800 dark:bg-black/30 md:static md:border-0 md:bg-transparent md:p-0">

                    <!-- Mobile: stacked | Desktop: no-wrap single row -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:flex-nowrap">

                        <!-- Search (left) -->
                        <div class="w-full sm:w-auto sm:flex-1 min-w-0">
                            <label for="user-search" class="sr-only">Search users</label>
                            <div class="relative w-full sm:w-64 md:w-80 lg:w-96">
                                <input id="user-search" type="text" x-model="search" @input="updateQuery"
                                    placeholder="Search users..."
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
                                        x-bind:href="'{{ route('admin.users.export') }}?search=' + encodeURIComponent(search)"
                                        @click="open = false"
                                        class="block px-4 py-2 text-sm hover:bg-gray-50 focus:bg-gray-50 dark:hover:bg-neutral-800"
                                        role="menuitem" tabindex="-1">
                                        Export All
                                    </a>
                                </div>
                            </div>


                              @if (Auth::guard('admin')->user()->hasPermission('create_users'))
                            <!-- Create -->
                            <a href="{{ route('admin.users.create') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">

                                <span class="sm:inline">+ Create</span>
                            </a>
                            @endif

                        </div>
                    </div>
                </div>



                <!-- Table -->
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                    <table id="usersTable" class="min-w-full table-auto text-xs sm:text-sm dark:text-white/90">
                        <thead class="sticky top-0 z-[1] bg-gray-100 text-xs uppercase dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-3 text-center w-12 min-w-[56px]">#</th>
                                <th class="px-3 py-3 text-center w-[16%] min-w-[140px]">Name</th>
                                <th class="px-3 py-3 text-center w-[26%] min-w-[180px]">Email</th>
                                <th class="px-3 py-3 text-center w-[16%] min-w-[120px]">Mobile</th>
                                <th class="px-3 py-3 text-center min-w-[160px]">Roles</th>
                                <th class="px-3 py-3 text-center w-[16%] min-w-[140px]">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                            @forelse ($users as $index => $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-3 py-3 whitespace-nowrap text-center">
                                        {{ $users->firstItem() + $index }}
                                    </td>

                                    <td class="px-3 py-3">
                                        <div class="font-medium truncate max-w-[160px] sm:max-w-none">
                                            {{ $user->fname }} {{ $user->lname }}
                                        </div>
                                    </td>

                                    <td class="px-3 py-3">
                                        <!-- mobile truncates, desktop shows fully -->
                                        <div class="truncate max-w-[180px] sm:max-w-none lg:whitespace-normal">
                                            {{ $user->email }}
                                        </div>
                                    </td>

                                    <td class="px-3 py-3 whitespace-nowrap">
                                        {{ $user->mobile_number }}
                                    </td>

                                    <td class="px-3 py-3">
                                        <div class="truncate max-w-[200px] sm:max-w-none lg:whitespace-normal">
                                            {{ $user->roles->pluck('name')->implode(', ') }}
                                        </div>
                                    </td>

                                    <!-- Actions: centered on mobile, right on sm+ -->
                                    <td class="px-3 py-3 text-center sm:text-right">
                                        <div
                                            class="inline-flex flex-wrap items-center gap-x-4 gap-y-2 justify-center sm:justify-end">
                                            @if (Auth::guard('admin')->user()->hasPermission('view_users'))
                                                <a href="{{ route('admin.users.show', $user) }}"
                                                    class="text-blue-600 hover:underline">View</a>
                                            @endif

                                            @if (Auth::guard('admin')->user()->hasPermission('edit_roles'))
                                                <a href="{{ route('admin.users.assign.roles', $user) }}"
                                                    class="text-green-600 hover:underline">Assign Roles</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-500 dark:text-gray-300">
                                        No users found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>





                <div class="mt-4">
                    {{ $users->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'users'
        };
    </script>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function usersComponent() {
            return {
                search: '{{ $search }}',
                debounceTimeout: null,
                updateQuery() {
                    clearTimeout(this.debounceTimeout);
                    this.debounceTimeout = setTimeout(() => {
                        const q = this.search.trim();
                        const query = q ? '?search=' + encodeURIComponent(q) : '';
                        window.location.href = '{{ route('admin.users.index') }}' + query;
                    }, 500);
                },
                exportToExcel() {
                    const rows = [
                        ["Sl", "fname", "lname", "email", "mobile_number", "address", "district", "city_town", "state",
                            "country", "pincode", "created_at"
                        ]
                    ];
                    @foreach ($users as $index => $user)
                        rows.push([
                            {{ $users->firstItem() + $index }},
                            "{{ $user->fname }}",
                            "{{ $user->lname }}",
                            "{{ $user->email }}",
                            "{{ $user->mobile_number }}",
                            "{{ $user->address }}",
                            "{{ $user->district }}",
                            "{{ $user->city_town }}",
                            "{{ $user->state }}",
                            "{{ $user->country }}",
                            "{{ $user->pincode }}",
                            "{{ $user->created_at->format('Y-m-d H:i') }}"
                        ]);
                    @endforeach
                    const worksheet = XLSX.utils.aoa_to_sheet(rows);
                    const workbook = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Users");
                    XLSX.writeFile(workbook, "users_export.xlsx");
                }
            }
        }
    </script>
@endpush
