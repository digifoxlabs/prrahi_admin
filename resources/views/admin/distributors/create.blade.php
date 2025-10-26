@extends('admin.admin-layout')

@section('page-content')


    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

      @include('admin.distributors._breadcrump')


        <div
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-6xl text-center">

          
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white/90">Create Distributor</h2>
                    @include('admin.distributors._form', [
                        'distributor' => null,
                        'action' => route('admin.distributors.store'),
                        'method' => 'POST'
                    ])
          
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



