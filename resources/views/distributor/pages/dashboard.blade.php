@extends('distributor.layout')

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
    <div class="grid grid-cols-12 gap-4 md:gap-6">


{{-- Dashboard Cards --}}
<div class="col-span-12">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">
        Distributor Dashboard
    </h1>
</div>

<div class="col-span-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

    {{-- My Orders --}}
    <a href="{{ route('distributor.orders.index') }}"
       class="group relative rounded-2xl border border-gray-200 dark:border-gray-700
              bg-white dark:bg-gray-900 p-5 shadow-sm
              hover:shadow-lg hover:-translate-y-1 transition-all duration-200">

        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    My Orders
                </p>
                <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white">
                    View Orders
                </h3>
            </div>

            <div class="flex h-12 w-12 items-center justify-center rounded-xl
                        bg-blue-100 text-blue-600
                        group-hover:bg-blue-600 group-hover:text-white transition">
                📦
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            Track and manage all orders placed by you.
        </p>
    </a>

    {{-- My Retailers --}}
    <a href="{{ route('distributor.retailers.index') }}"
       class="group relative rounded-2xl border border-gray-200 dark:border-gray-700
              bg-white dark:bg-gray-900 p-5 shadow-sm
              hover:shadow-lg hover:-translate-y-1 transition-all duration-200">

        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    My Retailers
                </p>
                <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white">
                    Retail Network
                </h3>
            </div>

            <div class="flex h-12 w-12 items-center justify-center rounded-xl
                        bg-green-100 text-green-600
                        group-hover:bg-green-600 group-hover:text-white transition">
                🏪
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            View and manage your appointed retailers.
        </p>
    </a>

    {{-- My Inventory --}}
    <a href="{{  route('distributor.stock.index') }}"
       class="group relative rounded-2xl border border-gray-200 dark:border-gray-700
              bg-white dark:bg-gray-900 p-5 shadow-sm
              hover:shadow-lg hover:-translate-y-1 transition-all duration-200">

        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    My Inventory
                </p>
                <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white">
                    Stock Overview
                </h3>
            </div>

            <div class="flex h-12 w-12 items-center justify-center rounded-xl
                        bg-purple-100 text-purple-600
                        group-hover:bg-purple-600 group-hover:text-white transition">
                📊
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            Check available stock and inventory movement.
        </p>
    </a>

    {{-- Retail Sales --}}
    <a href=" {{ route('distributor.retailer-sales.index') }}"
       class="group relative rounded-2xl border border-gray-200 dark:border-gray-700
              bg-white dark:bg-gray-900 p-5 shadow-sm
              hover:shadow-lg hover:-translate-y-1 transition-all duration-200">

        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Retail Sales
                </p>
                <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white">
                   Sale to Retailers
                </h3>
            </div>

            <div class="flex h-12 w-12 items-center justify-center rounded-xl
                        bg-orange-100 text-orange-600
                        group-hover:bg-orange-600 group-hover:text-white transition">
                🧾
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
             Create and Manage Sales to Retailers
        </p>
    </a>

    {{-- Retail Orders --}}
    <a href="#"
       class="group relative rounded-2xl border border-gray-200 dark:border-gray-700
              bg-white dark:bg-gray-900 p-5 shadow-sm
              hover:shadow-lg hover:-translate-y-1 transition-all duration-200">

        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Retail Orders
                </p>
                <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white">
                    Orders from Retailers
                </h3>
            </div>

            <div class="flex h-12 w-12 items-center justify-center rounded-xl
                        bg-orange-100 text-orange-600
                        group-hover:bg-orange-600 group-hover:text-white transition">
                🧾
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            Review orders placed by your retailers.
        </p>
    </a>

</div>




    </div>
 @endsection

@push('scripts')
<script>
    window.pageXData = {
        page: 'dashboard',
    };
</script>

@endpush