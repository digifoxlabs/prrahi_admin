@extends($layout)

@section('page-content')


    <div class="mx-auto max-w-(--breakpoint-2xl) p-2 md:py-2">

      {{-- @include('admin.distributors._breadcrump') --}}


        <div
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-8xl text-center">

                    @include('distributor.manage._form', [
                    'action' => route($routePrefix.'.distributor.store'),
                    'method' => 'POST',
                    'buttonText' => 'Create',
                    'distributor' => null
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



