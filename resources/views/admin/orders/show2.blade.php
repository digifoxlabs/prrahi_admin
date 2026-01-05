@extends('admin.admin-layout')

@section('page-content')
<div class="max-w-6xl mx-auto bg-white rounded-xl border p-6"
     x-data="{ showActionModal:false, action:'', showInvoiceRemove:false }">

    <!-- ================= HEADER ================= -->
    <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">
                Order #{{ $order->order_number }}
            </h1>
            <p class="text-sm text-gray-600">
                Order Date: {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
            </p>

            <div class="flex flex-wrap items-center gap-3 mt-1">
                <span class="text-sm">Status:</span>

                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'confirmed') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($order->status) }}
                </span>

                @if($order->admin_comments)
                    <span class="px-3 py-1 bg-blue-50 border rounded-full text-xs text-blue-700">
                        Remarks: {{ $order->admin_comments }}
                    </span>
                @endif
            </div>
        </div>

        <div class="text-right">
            <p class="text-sm text-gray-600">Created By</p>
            <p class="font-medium">{{ optional($order->created_by)->fname ?? 'System' }}</p>
        </div>
    </div>

    <!-- ================= INVOICE INFO ================= -->
    @if($order->invoice_status === 'generated')
        <div class="mb-6 p-4 bg-purple-50 border rounded-lg flex flex-col md:flex-row justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-purple-700">Invoice Details</p>
                <p>Invoice No: <strong>{{ $order->invoice_no }}</strong></p>
                <p>Invoice Date: <strong>{{ \Carbon\Carbon::parse($order->invoice_date)->format('d M Y') }}</strong></p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.orders.invoice.print', $order) }}"
                   target="_blank"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">
                    Download / Print Invoice
                </a>

                <button @click="showInvoiceRemove=true"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm">
                    Remove Invoice
                </button>
            </div>
        </div>
    @endif

    <!-- ================= DISTRIBUTOR & ADDRESS ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="font-semibold">Distributor</h3>
            <p>{{ $order->distributor->firm_name }}</p>
            <p class="text-xs text-gray-600">
                {{ $order->distributor->contact_person }}
                {{ $order->distributor->contact_number }}
            </p>
        </div>

        <div>
            <h3 class="font-semibold">Billing Address</h3>
            <pre class="bg-gray-50 border rounded p-3 text-sm whitespace-pre-wrap">
{{ $order->billing_address }}
            </pre>
        </div>
    </div>

    <!-- ================= ITEMS ================= -->
    <table class="w-full border text-sm mb-6">
        <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Product</th>
            <th class="p-2">Qty</th>
            <th class="p-2">Rate</th>
            <th class="p-2 text-right">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr class="border-t">
                <td class="p-2">
                    {{ $item->product->type === 'variant'
                        ? $item->product->parent->name
                        : $item->product->name }}
                </td>
                <td class="p-2 text-center">{{ $item->quantity }}</td>
                <td class="p-2 text-center">
                    {{ number_format($item->rate,2) }} / {{ $item->base_unit }}
                </td>
                <td class="p-2 text-right">{{ number_format($item->total,2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- ================= TOTALS ================= -->
    <div class="text-right space-y-1 text-sm">
        <p>Sub Total: {{ number_format($order->subtotal,2) }}</p>
        <p>Discount: {{ number_format($order->discount,2) }}</p>
        <p>CGST: {{ number_format($order->cgst,2) }}</p>
        <p>SGST: {{ number_format($order->sgst,2) }}</p>
        <p>Round Off: {{ number_format($order->round_off,2) }}</p>
        <p class="text-lg font-bold">Total: {{ number_format($order->total_amount,2) }}</p>
    </div>

    <!-- ================= ACTIONS ================= -->
    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('admin.orders.index') }}"
           class="px-4 py-2 border rounded-lg">Back</a>

        @if($order->status === 'pending')
            <a href="{{ route('admin.orders.edit',$order) }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                Edit Order
            </a>
        @else
            <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg">
                Edit Locked
            </span>
        @endif
    </div>

    <!-- ================= REMOVE INVOICE MODAL ================= -->
    <div x-show="showInvoiceRemove" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="showInvoiceRemove=false"></div>

        <div class="bg-white rounded-xl p-6 z-10 w-full max-w-md">
            <h3 class="font-semibold text-red-700 mb-2">Remove Invoice</h3>
            <p class="text-sm mb-4">Are you sure you want to remove invoice details?</p>

            <form method="POST" action="{{ route('admin.orders.invoice.remove',$order) }}">
                @csrf
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showInvoiceRemove=false"
                            class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg">
                        Confirm
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
            page: 'createOrder',
        };
    </script>
    
@endpush