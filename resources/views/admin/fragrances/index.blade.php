@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.fragrances._breadcrump')


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
                    <a href="{{ route('admin.fragrances.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">+ Add
                        Fragrance</a>
                    <table class="mt-4 w-full table-auto border text-center">
                        <thead class="bg-gray-100">
                            <tr>
                               
                                <th class="p-2">#</th>    
                                <th class="p-2">Name</th>    
                                <th class="p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fragrances as $frag)
                                <tr class="border-t" x-data="{ showModal: false, deleteUrl: '' }">
                            
                                    <td class="p-2">{{ $loop->iteration }}</td>                                   
                                    <td class="p-2">{{ $frag->name }}</td>                                   
                                    <td class="p-2">
                                        <a href="{{ route('admin.fragrances.edit', $frag) }}" class="text-blue-600">Edit</a>
                                        |
                                        <!-- Delete Button (triggers modal) -->
                                        <button
                                            @click.prevent="showModal = true; deleteUrl = '{{ route('admin.fragrances.destroy', $frag) }}';"
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
                                                        Are you sure you want to delete the Fragrance:
                                                        <strong>{{ $frag->name }}</strong>?
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
                                    <td colspan="2" class="px-4 py-3 text-center text-gray-400">No Fragrances
                                        found.
                                    </td>
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
            page: 'fragrances',
        };
    </script>
@endpush


