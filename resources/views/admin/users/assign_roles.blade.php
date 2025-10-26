@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.roles._breadcrump')


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




<div class="max-w-4xl mx-auto bg-white dark:bg-neutral-900 shadow-lg rounded-2xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
    
    <!-- Heading -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
            Assign Roles to 
            <span class="text-blue-600">{{ $userdata->fname }} {{ $userdata->lname }}</span>
        </h2>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h6v6m-6 0h6M9 7h6m-6 0H5v10h14V7h-4z" />
        </svg>
    </div>

    <form method="POST" action="{{ route('admin.users.assign.roles.store', $userdata) }}">
        @csrf

        <!-- Roles List -->
        <div class="mb-6">
            <label class="block text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Select Roles:</label>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 cursor-pointer hover:bg-blue-50 dark:hover:bg-neutral-800 transition">
                        <input type="checkbox" name="roles[]" 
                            value="{{ $role->id }}"
                            {{ $userdata->roles->contains($role->id) ? 'checked' : '' }}
                            class="form-checkbox h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-800 dark:text-gray-200">{{ $role->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                Save Roles
            </button>
        </div>
    </form>
</div>

          


            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'roles',
        };
    </script>
@endpush


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>


@endpush
