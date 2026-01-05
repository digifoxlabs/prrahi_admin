@extends('admin.admin-layout')

@section('page-content')
<div class="max-w-7xl mx-auto bg-white rounded-xl border p-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold">Invoices</h1>

        <!-- SEARCH -->
        <form method="GET" class="flex gap-2">
            <input type="text"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Search order / invoice / distributor..."
                   class="border rounded-lg px-3 py-2 text-sm w-72">

            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">
                Search
            </button>

            @if(request()->filled('q'))
                <a href="{{ route('admin.invoices.index') }}"
                   class="px-3 py-2 border rounded-lg text-sm">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">Invoice No</th>
                    <th class="p-2">Invoice Date</th>
                    <th class="p-2 text-left">Order No</th>
                    <th class="p-2 text-left">Distributor</th>
                    <th class="p-2 text-right">Amount</th>
                    <th class="p-2 text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($invoices as $order)
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-2 font-medium">
                        {{ $order->invoice_no }}
                    </td>

                    <td class="p-2 text-center">
                        {{ \Carbon\Carbon::parse($order->invoice_date)->format('d M Y') }}
                    </td>

                    <td class="p-2">
                        {{ $order->order_number }}
                    </td>

                    <td class="p-2">
                        {{ $order->distributor->firm_name }}
                    </td>

                    <td class="p-2 text-right font-semibold">
                        {{ number_format($order->total_amount, 2) }}
                    </td>

                    <td class="p-2 text-center">
                        <a href="{{ route('admin.orders.invoice.print', $order) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-600 text-white rounded-lg text-xs hover:bg-indigo-700">
                            🖨 Print
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">
                        No invoices found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>

</div>
@endsection


@push('scripts')

    <script>
        window.pageXData = {
            page: 'invoices',
        };
    </script>
    
@endpush