@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.categories._breadcrump')

        @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
                <strong>Please fix the following:</strong>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div
            class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-3xl">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white/90">Edit Category</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update category details and parent segment.</p>
                </div>

                @include('admin.categories._form', ['segments' => $segments, 'category' => $category])
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
