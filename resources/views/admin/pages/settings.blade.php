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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Distributor Order Settings Card --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden dark:border-gray-800 dark:bg-gray-900/50">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 dark:bg-gray-800/50 dark:border-gray-800">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 5l9 4 9-4m-18 5l9 4 9-4"></path>
                            </svg>
                            Distributor Order
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="hidden" name="settings[distributor_orders][check_stock_before_order]" value="0">
                                <input id="distributor-check-stock" type="checkbox" name="settings[distributor_orders][check_stock_before_order]" value="1"
                                    @checked(($distributorOrderSettings['check_stock_before_order'] ?? '0') == '1')
                                    class="h-4 w-4 text-blue-600 focus:ring-2 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600 dark:checked:border-blue-600 dark:focus:ring-blue-600">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="distributor-check-stock" class="font-medium text-gray-700 dark:text-gray-300">Check stock availability before creating order</label>
                                <p class="text-gray-500 dark:text-gray-400">Validate distributor order quantities against available stock before saving the order.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="hidden" name="settings[distributor_orders][allow_zero_stock_order]" value="0">
                                <input id="distributor-zero-stock" type="checkbox" name="settings[distributor_orders][allow_zero_stock_order]" value="1"
                                    @checked(($distributorOrderSettings['allow_zero_stock_order'] ?? '0') == '1')
                                    class="h-4 w-4 text-blue-600 focus:ring-2 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600 dark:checked:border-blue-600 dark:focus:ring-blue-600">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="distributor-zero-stock" class="font-medium text-gray-700 dark:text-gray-300">Allow order for zero stock availability</label>
                                <p class="text-gray-500 dark:text-gray-400">Enable this if distributor orders should still be allowed when available stock is zero.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Retail Order Settings Card --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden dark:border-gray-800 dark:bg-gray-900/50">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 dark:bg-gray-800/50 dark:border-gray-800">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11c1.657 0 3-1.79 3-4s-1.343-4-3-4-3 1.79-3 4 1.343 4 3 4zm-8 0c1.657 0 3-1.79 3-4S9.657 3 8 3 5 4.79 5 7s1.343 4 3 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm8 0c-.29 0-.62.02-.97.05 1.42 1.03 2.97 2.76 2.97 4.95v1h6v-2c0-2.66-5.33-4-8-4z"></path>
                            </svg>
                            Retail Order
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="hidden" name="settings[retail_orders][check_stock_before_order]" value="0">
                                <input id="retail-check-stock" type="checkbox" name="settings[retail_orders][check_stock_before_order]" value="1"
                                    @checked(($retailOrderSettings['check_stock_before_order'] ?? '0') == '1')
                                    class="h-4 w-4 text-blue-600 focus:ring-2 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600 dark:checked:border-blue-600 dark:focus:ring-blue-600">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="retail-check-stock" class="font-medium text-gray-700 dark:text-gray-300">Check stock availability before creating order</label>
                                <p class="text-gray-500 dark:text-gray-400">Validate retail order quantities against available stock before saving the order.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="hidden" name="settings[retail_orders][allow_zero_stock_order]" value="0">
                                <input id="retail-zero-stock" type="checkbox" name="settings[retail_orders][allow_zero_stock_order]" value="1"
                                    @checked(($retailOrderSettings['allow_zero_stock_order'] ?? '0') == '1')
                                    class="h-4 w-4 text-blue-600 focus:ring-2 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600 dark:checked:border-blue-600 dark:focus:ring-blue-600">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="retail-zero-stock" class="font-medium text-gray-700 dark:text-gray-300">Allow order for zero stock availability</label>
                                <p class="text-gray-500 dark:text-gray-400">Enable this if retail orders should still be allowed when available stock is zero.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Invoice Template Settings --}}
                <div class="col-span-2 bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden dark:border-gray-800 dark:bg-gray-900/50">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 dark:bg-gray-800/50 dark:border-gray-800">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M8 6h8a2 2 0 012 2v12l-3-2-3 2-3-2-3 2V8a2 2 0 012-2z"></path>
                            </svg>
                            Invoice Template Settings
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice Title</label>
                                <input type="text" name="settings[invoice_template][title]"
                                    value="{{ $invoiceSettings['title'] ?? 'Tax Invoice' }}"
                                    class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                                <input type="text" name="settings[invoice_template][company_name]"
                                    value="{{ $invoiceSettings['company_name'] ?? config('app.name') }}"
                                    class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div class="space-y-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Address Block (multiline)</label>
                                <textarea name="settings[invoice_template][company_address]" rows="3"
                                    class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ $invoiceSettings['company_address'] ?? '' }}</textarea>
                            </div>

                            <div class="space-y-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Declaration Text</label>
                                <textarea name="settings[invoice_template][declaration]" rows="3"
                                    class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ $invoiceSettings['declaration'] ?? 'We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.' }}</textarea>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Signature Company Text</label>
                                <input type="text" name="settings[invoice_template][signature_for_name]"
                                    value="{{ $invoiceSettings['signature_for_name'] ?? config('app.name') }}"
                                    class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Signature Label</label>
                                <input type="text" name="settings[invoice_template][signature_label]"
                                    value="{{ $invoiceSettings['signature_label'] ?? 'Authorised Signatory' }}"
                                    class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div class="space-y-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Footer Note</label>
                                <input type="text" name="settings[invoice_template][footer_note]"
                                    value="{{ $invoiceSettings['footer_note'] ?? 'This is a Computer Generated Invoice' }}"
                                    class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div class="space-y-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custom Invoice CSS (optional)</label>
                                <textarea name="settings[invoice_template][custom_css]" rows="4"
                                    class="block w-full rounded-md border border-gray-300 py-2 px-3 text-sm font-mono focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ $invoiceSettings['custom_css'] ?? '' }}</textarea>
                            </div>

                            <div class="flex items-start md:col-span-2">
                                <div class="flex items-center h-5">
                                    <input type="hidden" name="settings[invoice_template][auto_print]" value="0">
                                    <input id="invoice-auto-print" type="checkbox" name="settings[invoice_template][auto_print]" value="1"
                                        @checked(($invoiceSettings['auto_print'] ?? '1') == '1')
                                        class="h-4 w-4 text-blue-600 focus:ring-2 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600 dark:checked:border-blue-600 dark:focus:ring-blue-600">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="invoice-auto-print" class="font-medium text-gray-700 dark:text-gray-300">Auto print on load</label>
                                    <p class="text-gray-500 dark:text-gray-400">When enabled, print dialog opens automatically after opening invoice page.</p>
                                </div>
                            </div>
                        </div>
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

