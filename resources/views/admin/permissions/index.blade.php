@extends('admin.admin-layout')

@section('page-content')

    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">


        @include('admin.permissions._breadcrump')


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
            <div class="mx-auto w-full max-w-6xl text-center">


                <div class="p-6 bg-white shadow rounded-lg" x-data="permissionTableComponent()">

                    <div class="grid grid-cols-3 gap-4">

                        <div class="col-span-2">

                            <div class="relative w-full md:w-1/3 mb-4" x-data>
                                <input type="text" x-model="search" @input="updateQuery"
                                    placeholder="Search permissions..."
                                    class="w-full px-4 py-2 pr-10 border border-gray-300 rounded" />
                                <!-- Clear Button -->
                                <button type="button" @click="search = ''; updateQuery()" x-show="search"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-800"
                                    title="Clear search">
                                    ✕
                                </button>
                            </div>
                        </div>

                        <div class="col-end-7">

                            <div x-data="{ open: false }" class="relative inline-block text-left ml-4">
                                <div>
                                    <button @click="open = !open"
                                        class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-green-600 text-sm font-medium text-white hover:bg-green-700 focus:outline-none"
                                        type="button" id="menu-button" aria-expanded="true" aria-haspopup="true">
                                        Export to Excel
                                        <!-- Chevron -->
                                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Dropdown menu -->
                                <div x-show="open" @click.outside="open = false"
                                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                    role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                    <div class="py-1" role="none">
                                        <!-- Export Current Page (Alpine-triggered method) -->
                                        <button @click="exportToExcel; open = false"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                            role="menuitem" tabindex="-1">
                                            Export Current Page
                                        </button>

                                        <!-- Export All (server-side link with search binding) -->
                                        <a href="{{ route('admin.permissions.export') }}?search="
                                            x-bind:href="'{{ route('admin.permissions.export') }}?search=' + encodeURIComponent(
                                                search)"
                                            @click="open = false"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"
                                            tabindex="-1">
                                            Export All
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('admin.permissions.create') }}"
                                class="inline-block px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition duration-150 ease-in-out">
                                + Create
                            </a>


                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="permissionsTable" class="min-w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-100 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3">#</th>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permissions as $index => $permission)
                                    <tr class="border-b hover:bg-gray-50" x-data="{ showModal: false, deleteUrl: '' }">
                                        <td class="px-4 py-3">{{ $permissions->firstItem() + $index }}</td>
                                        <td class="px-4 py-3">{{ $permission->name }}</td>
                                        <td class="px-4 py-3 text-right">

                                            <!-- Edit -->
                                            <a href="{{ route('admin.permissions.edit', $permission) }}"
                                                class="text-blue-500 hover:underline mr-2">
                                                Edit
                                            </a>

                                            <!-- Delete Button (triggers modal) -->
                                            <button
                                                @click.prevent="showModal = true; deleteUrl = '{{ route('admin.permissions.destroy', $permission) }}';"
                                                class="text-red-500 hover:underline">
                                                Delete
                                            </button>

                                            <!-- Modal -->
                                            <template x-if="showModal">

                                                <!-- Delete Confirmation Modal -->
                                                <div x-show="showModal" x-transition
                                                    class="fixed inset-0 z-[100] flex items-center justify-center"
                                                    style="height: 100vh; width: 100vw;">

                                                    <!-- Overlay (covers entire screen) -->
                                                    <div class="absolute inset-0 bg-gray-700/60 backdrop-blur-sm"
                                                        @click="showModal = false"></div>

                                                    <!-- Modal Box -->
                                                    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md p-6 z-[101]"
                                                        @click.stop>
                                                        <h2 class="text-lg font-semibold mb-4 text-red-600">Confirm
                                                            Deletion</h2>
                                                        <p class="mb-6">
                                                            Are you sure you want to delete the Permission:
                                                            <strong>{{ $permission->name }}</strong>?
                                                        </p>

                                                        <div class="flex justify-end space-x-3">
                                                            <button @click="showModal = false"
                                                                class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                                                                Cancel
                                                            </button>

                                                            <form :action="deleteUrl" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
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
                                        <td colspan="3" class="px-4 py-3 text-center text-gray-400">No permissions
                                            found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $permissions->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'permissions',
        };
    </script>
@endpush


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        function permissionTableComponent() {
            return {
                search: '{{ $search ?? '' }}',
                debounceTimeout: null,
                updateQuery() {
                    clearTimeout(this.debounceTimeout);
                    this.debounceTimeout = setTimeout(() => {
                        const base = '{{ route('admin.permissions.index') }}';
                        const query = this.search.trim() ? '?search=' + encodeURIComponent(this.search.trim()) : '';
                        window.location.href = base + query;
                    }, 500);
                },
                exportToExcel() {
                    const table = document.getElementById('permissionsTable');
                    const wb = XLSX.utils.table_to_book(table, {
                        sheet: "Permissions"
                    });
                    XLSX.writeFile(wb, "permissions.xlsx");
                }
            };
        }
    </script>
@endpush
