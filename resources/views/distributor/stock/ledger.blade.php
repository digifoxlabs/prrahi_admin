@extends('distributor.layout')

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
            Inventory Ledger
        </h2>

        <a href="{{ route('distributor.stock.index') }}"
           class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
            ← Back to Inventory
        </a>
    </div>

    {{-- Ledger Card --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm
                dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-center">Type</th>
                        <th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3 text-left">Source</th>
                        <th class="px-4 py-3 text-left">Remarks</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $tx->created_at->format('d M Y, h:i A') }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">
                                {{ $tx->distributorProduct->product_name }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if($tx->type === 'in')
                                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1
                                                 text-xs font-semibold text-green-700
                                                 dark:bg-green-900/30 dark:text-green-300">
                                        IN
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-red-100 px-3 py-1
                                                 text-xs font-semibold text-red-700
                                                 dark:bg-red-900/30 dark:text-red-300">
                                        OUT
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right font-semibold">
                                {{ $tx->quantity }}
                            </td>

                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                @if($tx->source)
                                    {{ class_basename($tx->source_type) }} #{{ $tx->source_id }}
                                @else
                                    —
                                @endif
                            </td>

                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $tx->remarks ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="px-4 py-8 text-center text-gray-400">
                                No inventory transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="border-t border-gray-200 dark:border-gray-800 px-4 py-3">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>   
    window.pageXData = {
        page: 'retailers-sales',
    };

</script>
    
@endpush
