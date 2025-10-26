@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.categories._breadcrump')


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
            <div class="mx-auto w-full max-w-6xl">


<div class="p-4">
    <a href="{{ route('admin.categories.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">+ Add
        Category</a>
    <table class="mt-4 w-full table-auto border text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">#</th>
                <th class="p-2">Name</th>
                <th class="p-2">Parent</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $cat)
                <tr class="border-t" x-data="{ showModal: false, deleteUrl: '', open: false }">
                    <td class="p-2">{{ $loop->iteration }}</td>
                    <td class="p-2">{{ $cat->name }}</td>
                    <td class="p-2">{{ $cat->parent?->name }}</td>
                    <td class="p-2 whitespace-nowrap text-center relative">
                        <!-- Actions Dropdown -->
                        <button @click="open = !open"
                            class="bg-gray-200 text-sm px-3 py-1 rounded hover:bg-gray-300 focus:outline-none">
                            Actions ▾
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 z-20 mt-1 w-32 bg-white border border-gray-200 rounded shadow-md text-left">

                           
                            <!-- Edit -->
                            <a href="{{ route('admin.categories.edit', $cat) }}"
                                class="block px-3 py-2 text-sm text-blue-600 hover:bg-gray-100 hover:text-blue-800">
                                ✏️ Edit
                            </a>

                            <!-- Delete triggers modal -->
                            <button type="button" @click.prevent="showModal = true; deleteUrl = '{{ route('admin.categories.destroy', $cat) }}'; open = false;"
                                class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100 hover:text-red-700">
                                🗑️ Delete
                            </button>
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <template x-if="showModal">
                            <div x-show="showModal" x-transition
                                class="fixed inset-0 z-[100] flex items-center justify-center"
                                style="height: 100vh; width: 100vw;">
                                <div class="absolute inset-0 bg-gray-700/60 backdrop-blur-sm"
                                    @click="showModal = false"></div>
                                <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md p-6 z-[101]"
                                    @click.stop>
                                    <h2 class="text-lg font-semibold mb-4 text-red-600">Confirm Deletion</h2>
                                    <p class="mb-6">
                                        Are you sure you want to delete the Category:
                                        <strong>{{ $cat->name }}</strong>?
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
                    <td colspan="4" class="px-4 py-3 text-center text-gray-400">No Categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>



            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'categories',
        };
    </script>
@endpush
