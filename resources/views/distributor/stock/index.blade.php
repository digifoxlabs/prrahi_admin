@extends('distributor.layout')

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
            My Inventory
        </h2>

        <a href="{{ route('distributor.dashboard') }}"
           class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            ← Back
        </a>
    </div>

    {{-- Card --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm
                dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700 dark:text-gray-200">
                <thead class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-800">
                    <tr class="text-xs uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-right">MRP / Unit</th>
                        <th class="px-4 py-3 text-right">PTR / Dozen</th>
                        <th class="px-4 py-3 text-center">Available Qty</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">
                                {{ $stock->product->product_name }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ number_format($stock->product->mrp, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ number_format($stock->product->ptr, 2) }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center
                                             rounded-full bg-green-100 px-3 py-1
                                             text-sm font-semibold text-green-800
                                             dark:bg-green-900/30 dark:text-green-300">
                                    {{ $stock->available_qty }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                No inventory available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Enhanced Transactions Link --}}
        <div class="mt-6 border-t border-gray-200 dark:border-gray-800 px-4 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Need detailed transaction history?</span>
                    <p class="mt-1">View all your inventory transactions including purchases, sales, and adjustments.</p>
                </div>
                
                <a href="{{ route('distributor.inventory.ledger') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600
                          px-5 py-3 text-sm font-semibold text-white shadow-md
                          hover:from-blue-700 hover:to-indigo-700 hover:shadow-lg
                          active:scale-[0.98] transition-all duration-200
                          dark:from-blue-500 dark:to-indigo-500 dark:hover:from-blue-600 dark:hover:to-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    View All Transactions
                </a>
            </div>
        </div>

        {{-- Pagination --}}
        @if($stocks->hasPages())
            <div class="border-t border-gray-200 dark:border-gray-800 px-4 py-3">
                {{ $stocks->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    window.pageXData = {
        page: 'distributor-inventory',
    };
</script>
@endpush