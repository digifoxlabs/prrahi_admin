@extends('admin.admin-layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

      @include('admin.permissions._breadcrump')

        <div
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-6xl text-center">


                <div class="max-w-xl mx-auto p-4 bg-white rounded shadow mt-10">
                    <h2 class="text-xl font-bold mb-4">Edit Permission</h2>
                    @include('admin.permissions._form', ['permission' => $permission])
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
