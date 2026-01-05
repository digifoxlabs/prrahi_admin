@extends('admin.admin-layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

      @include('admin.orders._breadcrump')


        <div
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-6xl">



        
            <h2 class="text-xl font-semibold mb-4 dark:text-white">Create New Order</h2>

            <form class="text-center" action="{{ route('admin.orders.store') }}" method="POST">
                @csrf
                @include('admin.orders._form', ['order' => null])
            </form>

           


            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'createOrder',
        };
    </script>

@endpush



