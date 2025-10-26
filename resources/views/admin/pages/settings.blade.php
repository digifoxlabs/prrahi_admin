@extends('admin.admin-layout')

@section('page-content')

    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `Dashboard` }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName"></h2>

                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="index.html">
                                Home
                                <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke=""
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName"></li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Breadcrumb End -->


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




<div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
    <div class="mx-auto w-full max-w-[1200px]">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
                {{-- Product Settings Card --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden dark:border-gray-800 dark:bg-gray-900/50">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 dark:bg-gray-800/50 dark:border-gray-800">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Product Settings
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="check-stock" type="checkbox" name="settings[products][check_stock_before_order]" value="1"
                                    @checked($productSettings['check_stock_before_order'] ?? false)
                                    class="h-4 w-4 text-blue-600 focus:ring-2 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600 dark:checked:border-blue-600 dark:focus:ring-blue-600">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="check-stock" class="font-medium text-gray-700 dark:text-gray-300">Check stock before creating order</label>
                                <p class="text-gray-500 dark:text-gray-400">Enable to verify product availability during order creation</p>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="low-stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Low stock warning threshold</label>
                            <div class="relative rounded-md shadow-sm max-w-xs">
                                <input type="number" id="low-stock" name="settings[products][low_stock_warning]" 
                                       value="{{ $productSettings['low_stock_warning'] ?? 10 }}" 
                                       class="block w-full rounded-md border-gray-300 pl-3 pr-10 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">units</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">System will warn when stock falls below this quantity</p>
                        </div>
                    </div>
                </div>

                {{-- Distributor Settings Card --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden dark:border-gray-800 dark:bg-gray-900/50">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 dark:bg-gray-800/50 dark:border-gray-800">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Distributor Settings
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="space-y-1">
                            <label for="max-outstanding" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maximum outstanding limit</label>
                            <div class="relative rounded-md shadow-sm max-w-xs">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                </div>
                                <input type="number" id="max-outstanding" name="settings[distributors][max_outstanding]" 
                                       value="{{ $distributorSettings['max_outstanding'] ?? 0 }}" 
                                       class="block w-full rounded-md border-gray-300 pl-7 pr-10 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500">
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Set the maximum allowed outstanding balance for distributors</p>
                        </div>
                    </div>
                </div>

                {{-- Empty Card 1 (Placeholder for additional settings) --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden dark:border-gray-800 dark:bg-gray-900/50">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 dark:bg-gray-800/50 dark:border-gray-800">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Free Dozen Settings
                        </h2>
                    </div>
                    <div class="p-6">
                        
                    <div class="space-y-1">
                            <label for="free-dozen" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Free Dozen per case</label>
                            <div class="relative rounded-md shadow-sm max-w-xs">
                          
                                {{-- <input type="number" id="free-dozen" name="settings[orders][free-dozen]" 
                                       value="{{ $orderSettings['free-dozen'] ?? 0 }}" 
                                       class="block w-full rounded-md border-gray-300 pl-7 pr-10 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"> --}}
                           
<input type="hidden" name="settings[orders][free-dozen]" value="0">
<input type="checkbox" id="free-dozen" name="settings[orders][free-dozen]" value="1"
       {{ !empty($orderSettings['free-dozen']) && $orderSettings['free-dozen'] == 1 ? 'checked' : '' }}
       class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600 dark:checked:border-blue-600">




                            
                                    </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Is Free Dozen Active</p>
                        </div>



                    </div>
                </div>

                {{-- Empty Card 2 (Placeholder for additional settings) --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden dark:border-gray-800 dark:bg-gray-900/50">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 dark:bg-gray-800/50 dark:border-gray-800">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Security Settings
                        </h2>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Security settings can be added here</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>





    </div>
    
@endsection

@push('scripts')
<script>
    window.pageXData = {
        page: 'settings',       
    };
</script>

@endpush

