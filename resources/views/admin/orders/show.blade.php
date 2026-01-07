@extends('admin.admin-layout')

@section('page-content')
<div class="max-w-6xl mx-auto bg-white rounded-xl border p-6">


    @include('partials.flash')


    <!-- ================= HEADER ================= -->
    <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">

    <div>
        <h1 class="text-2xl font-bold">
            Order #{{ $order->order_number }}
        </h1>
        <p class="text-sm text-gray-600">
            Order Date: {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
        </p>
        
        <!-- Status and Admin Comments in same line -->
        <div class="flex flex-wrap items-center gap-3 mt-1">
            <div class="flex items-center gap-2">
                <span class="text-sm">Status:</span>
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'confirmed') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($order->status) }} 
                </span>   
            </div>
            
            @if($order->admin_comments)
            <div class="flex items-center gap-2">
                <div class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 border border-blue-100 rounded-full">
                    <span class="text-xs font-medium text-blue-700">Remarks:</span>
                    <span class="text-xs text-blue-600">{{ $order->admin_comments }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

        <div class="text-right">
            <p class="text-sm text-gray-600">Created By</p>
            <p class="font-medium">

                {{ 
                    optional($order->created_by)->fname ??
                    optional($order->created_by)->firm_name ??
                    optional($order->created_by)->name ??
                    'System'
                 }}


            </p>
        </div>
    </div>
    <div x-data="{ showModal: false, action: '', showInvoiceRemove: false, showInvoiceModal: false }">

    <!-- ================= INVOICE INFO ================= -->
    @if($order->invoice_status === 'generated' && $order->dispatch_status !== 'delivered')
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

        @elseif ($order->invoice_status === 'generated')

            <!-- Delivered order: print only -->
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

  
            </div>
        </div>

    @endif




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

    {{-- <div x-data="{ showInvoiceRemove: false, action: '' }"> --}}


    <!-- ================= DISTRIBUTOR & ADDRESS ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        <!-- Distributor -->
        <div>
            <h3 class="font-semibold mb-1">Distributor</h3>
            <p class="text-sm">{{ $order->distributor->firm_name }}</p>
            <p class="text-xs text-gray-600">
                {{ $order->distributor->contact_person }}
                {{ $order->distributor->contact_number }}
            </p>
        </div>

        <!-- Billing Address -->
        <div>
            <h3 class="font-semibold mb-1">Billing Address</h3>
            <pre class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 border rounded p-3">
{{ $order->billing_address }}
            </pre>
        </div>

    </div>

    <!-- ================= ORDER ITEMS ================= -->
    <div class="overflow-x-auto mb-6">
        <table class="w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">Product</th>
                    <th class="p-2">Code</th>
                    <th class="p-2">Qty</th>
                    <th class="p-2">Rate</th>
                    <th class="p-2">Disc %</th>
                    <th class="p-2 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>

                @php
                $stockErrors = session('stock_errors', []);
                @endphp

                @foreach($order->items as $item)
                <tr class="border-t">
                    <td class="p-2">
                        {{ $item->product->type === 'variant'
                            ? $item->product->parent->name
                            : $item->product->name
                        }}
                        @if($item->product->attributes)
                            <span class="text-xs text-gray-600">
                                —
                                {{ $item->product->attributes['fragrance'] ?? '' }}
                                @if(!empty($item->product->attributes['size']))
                                    ({{ $item->product->attributes['size'] }})
                                @endif
                            </span>
                        @endif


                        @if(isset($stockErrors[$item->id]))
                            <p class="text-xs text-red-600 mt-1">
                                {{ $stockErrors[$item->id] }}
                            </p>
                        @endif


                    </td>

                    <td class="p-2">{{ $item->product->code }}</td>

                    <td class="p-2 text-center">{{ $item->quantity }}</td>

                    <td class="p-2 text-center">
                        {{ number_format($item->rate, 2) }}
                        <span class="text-xs text-gray-500">
                            / {{ $item->base_unit }}
                        </span>
                    </td>

                    <td class="p-2 text-center">
                        {{ number_format($item->discount_percent ?? 0, 2) }}%
                    </td>

                    <td class="p-2 text-right">
                        {{ number_format($item->total, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ================= TOTALS ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div></div>

        <div class="text-right space-y-1 text-sm">
            <p>
                Sub Total:
                <span class="font-medium">{{ number_format($order->subtotal, 2) }}</span>
            </p>

            <p>
                Discount:
                <span class="font-medium">{{ number_format($order->discount, 2) }}</span>
            </p>

            {{-- ================= TAX BREAKUP ================= --}}

            @if($order->igst > 0)
                <p>
                    IGST (5%):
                    <span class="font-medium">{{ number_format($order->igst, 2) }}</span>
                </p>
            @else
                <p>
                    CGST (2.5%):
                    <span class="font-medium">{{ number_format($order->cgst, 2) }}</span>
                </p>

                <p>
                    SGST (2.5%):
                    <span class="font-medium">{{ number_format($order->sgst, 2) }}</span>
                </p>
            @endif

            <p>
                Round Off:
                <span class="font-medium">{{ number_format($order->round_off, 2) }}</span>
            </p>

            <p class="text-lg font-bold mt-2">
                Total:
                {{ number_format($order->total_amount, 2) }}
            </p>
        </div>
    </div>


<div class="flex justify-start gap-3 mb-4">


        @if($order->status === 'pending')
            <button @click="showModal=true; action='confirm'"
                class="px-4 py-2 bg-green-600 text-white rounded-lg">
            Confirm Order
            </button>

            <button @click="showModal=true; action='cancel'"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg">
                Cancel Order
            </button>                 

    
        @endif

        @if($order->status === 'confirmed' && $order->dispatch_status !== 'delivered')
                <button @click="showModal=true; action='cancel'"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg">
                Cancel Order
                </button>
        @endif


        @if($order->status === 'confirmed' && $order->invoice_status !== 'generated')
        <button @click="showInvoiceModal = true"
            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            Mark Invoice Generated
        </button>
        @endif


</div>


    <!-- ================= ACTIONS ================= -->
    <div class="flex justify-end gap-3 mt-6">


        <a href="{{ route('admin.orders.index') }}"
           class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
            Back
        </a>

        @if($order->status === 'pending')
            <a href="{{ route('admin.orders.edit', $order) }}"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Edit Order
            </a>
        @else
            <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed">
                Edit Locked
            </span>
        @endif

    </div>


<!-- ================= INVOICE GENERATED MODAL ================= -->
<div 
     x-show="showInvoiceModal"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         @click="showInvoiceModal = false"></div>

    <!-- Modal -->
    <div class="bg-white rounded-xl w-full max-w-md p-6 z-10">
        <h3 class="text-lg font-semibold mb-4 text-purple-700">
            Enter Invoice Details
        </h3>

        <form method="POST"
              action="{{ route('admin.orders.invoice.generate', $order) }}">
            @csrf

            <!-- Invoice Number -->
            <div class="mb-3">
                <label class="text-sm font-medium">Invoice Number</label>
                <input type="text"
                       name="invoice_no"
                       required
                       class="w-full border rounded-lg p-2 text-sm"
                       placeholder="INV-000123">
            </div>

            <!-- Invoice Date -->
            <div class="mb-4">
                <label class="text-sm font-medium">Invoice Date</label>
                <input type="date"
                       name="invoice_date"
                       required
                       value="{{ now()->toDateString() }}"
                       class="w-full border rounded-lg p-2 text-sm">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button"
                        @click="showInvoiceModal = false"
                        class="px-4 py-2 border rounded-lg">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg">
                    Save Invoice
                </button>
            </div>
        </form>
    </div>
</div>













<div x-show="showModal"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center">

    <div class="absolute inset-0 bg-gray-700/60 dark:bg-black/70 backdrop-blur-sm"
         @click="showModal=false"></div>

    <div class="bg-white rounded-xl w-full max-w-lg p-6 z-10">
        <h3 class="text-lg font-semibold mb-3"
            x-text="action === 'confirm' ? 'Confirm Order' : 'Cancel Order'">
        </h3>

        <form method="POST"
              :action="action === 'confirm'
                        ? '{{ route('admin.orders.confirm', $order) }}'
                        : '{{ route('admin.orders.cancel', $order) }}'">

            @csrf

            <textarea name="admin_comments"
                      rows="4"
                      required
                      class="w-full border rounded-lg p-2 text-sm"
                      placeholder="Enter remarks (Required)..."></textarea>

            <div class="flex justify-end gap-3 mt-4">
                <button type="button"
                        @click="showModal=false"
                        class="px-4 py-2 border rounded-lg">
                    Close
                </button>

                <button type="submit"
                        :class="action === 'confirm'
                            ? 'bg-green-600'
                            : 'bg-red-600'"
                        class="px-4 py-2 text-white rounded-lg">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>




<!-- ================= ORDER ACTIVITY TIMELINE ================= -->
<div class="mt-8 border rounded-xl p-5 bg-gray-50">
    <h3 class="text-lg font-semibold mb-4">
        Order Activity Timeline
    </h3>

    <div class="relative pl-6">
        <!-- Vertical Line -->
        <div class="absolute left-2 top-0 bottom-0 w-px bg-gray-300"></div>

        @forelse($order->activities as $activity)
            <div class="relative mb-6">

                <!-- Status Dot -->
                <div class="absolute -left-[5px] top-1 w-3 h-3 rounded-full
                    @if(in_array($activity->event, ['confirmed','invoice_generated','dispatched','delivered']))
                        bg-green-600
                    @elseif(in_array($activity->event, ['cancelled','invoice_removed']))
                        bg-red-600
                    @else
                        bg-gray-400
                    @endif
                ">
                </div>

                <!-- Content -->
                <div class="ml-4">
                    <p class="text-sm font-semibold text-gray-800">
                        {{ ucwords(str_replace('_',' ', $activity->event)) }}
                    </p>

                    @if($activity->remarks)
                        <p class="text-xs text-gray-600 mt-0.5">
                            {{ $activity->remarks }}
                        </p>
                    @endif

                    <p class="text-xs text-gray-500 mt-1">
                        {{ $activity->created_at->format('d M Y, h:i A') }}
                        •
                        <span class="font-medium">
                            {{ optional($activity->performedBy)->fname
                                ?? optional($activity->performedBy)->firm_name
                                ?? optional($activity->performedBy)->name
                                ?? 'System' }}
                        </span>
                    </p>
                </div>

            </div>
        @empty
            <p class="text-sm text-gray-500 ml-4">
                No activity recorded yet.
            </p>
        @endforelse
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

