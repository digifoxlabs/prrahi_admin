@extends('admin.admin-layout')

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

    <h1 class="text-2xl font-semibold mb-4">{{ $title }}</h1>


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


<!-- ================= SEARCH & FILTER ================= -->
<form method="GET"
      class="mb-4 flex flex-wrap items-center gap-3">

    <!-- Search -->
    <div class="flex-1 min-w-[220px]">
        <input type="text"
               name="q"
               value="{{ request('q') }}"
               placeholder="Search Order Number..."
               class="w-full border rounded-lg px-3 py-2 text-sm">
    </div>

    <!-- Status Filter -->
    <div class="min-w-[180px]">
        <select name="status"
                class="w-full border rounded-lg px-3 py-2 text-sm">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                Pending
            </option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>
                Confirmed
            </option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                Cancelled
            </option>
        </select>
    </div>

    <!-- Buttons -->
    <div class="flex gap-2">
        <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            Filter
        </button>

        @if(request()->filled('q') || request()->filled('status'))
            <a href="{{ route('admin.orders.index') }}"
               class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">
                Clear
            </a>
        @endif
    </div>

</form>










    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 text-center" id="modalTitle"></h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 text-center" id="modalMessage"></p>
                </div>
                <div class="items-center px-4 py-3 flex justify-center space-x-4">
                    <button id="cancelBtn" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <form id="confirmForm" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Confirm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Order</th>
                    <th class="p-3">Distributor</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Amount</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>

            <tbody>
            @foreach($orders as $order)

                @php
                    $events = $order->activities->pluck('event')->toArray();
                    $timeline = config('order_timeline');
                    $isCancelled = in_array('cancelled', $events);
                @endphp

                <!-- ================= ORDER ROW ================= -->
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3 font-medium">{{ $order->order_number }}</td>
                    <td class="p-3">{{ $order->distributor->firm_name ?? '-' }}</td>
                    <td class="p-3">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="p-3 font-semibold">₹ {{ number_format($order->total_amount, 2) }}</td>

                    <td class="p-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'confirmed') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>

                <td class="p-3 text-right">
                    <div class="flex items-center justify-end space-x-2">
                        <!-- View Button -->
                        <a href="{{ route('admin.orders.show', $order) }}"
                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-md hover:bg-indigo-100 hover:shadow-sm transition-colors duration-150 text-sm font-medium">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>

                        {{-- ACTIONS AFTER INVOICE --}}
                        @if($order->status === 'confirmed' && $order->invoice_status === 'generated')
                            @if($order->dispatch_status === 'pending')
                                <!-- Dispatch Button -->
                                <button type="button" 
                                        onclick="showConfirmation('dispatch', '{{ route('admin.orders.dispatch', $order) }}', '{{ $order->order_number }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 hover:shadow-sm transition-colors duration-150 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Dispatch
                                </button>
                            @elseif($order->dispatch_status === 'dispatched')
                                <!-- Deliver Button -->
                                <button type="button" 
                                        onclick="showConfirmation('deliver', '{{ route('admin.orders.deliver', $order) }}', '{{ $order->order_number }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-md hover:bg-green-100 hover:shadow-sm transition-colors duration-150 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15l-2-2m0 0l-2-2m2 2l2-2m-2 2l2 2"/>
                                    </svg>
                                    Deliver
                                </button>

                                <!-- Cancel Button -->
                                <button type="button" 
                                        onclick="showConfirmation('cancel', '{{ route('admin.orders.cancel', $order) }}', '{{ $order->order_number }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 hover:shadow-sm transition-colors duration-150 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancel
                                </button>
                            @endif
                        @endif
                    </div>
                </td>

                </tr>

                <!-- ================= TIMELINE ROW ================= -->
                <tr class="bg-gray-50">
                    <td colspan="6" class="p-4">

                        <div class="flex items-center gap-2">

                            @foreach($timeline as $key => $step)

                                @if($isCancelled && $key !== 'cancelled' && !in_array($key, $events))
                                    @break
                                @endif

                                @php
                                    $completed = in_array($key, $events);
                                    $color = $key === 'cancelled'
                                        ? 'red'
                                        : 'green';
                                @endphp

                                <!-- ICON -->
                                <div class="relative group">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                        {{ $completed
                                            ? "bg-{$color}-600 text-white"
                                            : 'bg-gray-300 text-gray-600'
                                        }}">
                                        {{ $step['icon'] }}
                                    </div>

                                    <!-- TOOLTIP -->
                                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2
                                                whitespace-nowrap bg-black text-white text-xs px-2 py-1 rounded
                                                opacity-0 group-hover:opacity-100 transition">
                                        {{ $step['label'] }}
                                    </div>
                                </div>

                                @if(!$loop->last)
                                    <div class="w-8 h-1
                                        {{ $completed
                                            ? "bg-{$color}-600"
                                            : 'bg-gray-300'
                                        }}">
                                    </div>
                                @endif

                            @endforeach

                        </div>

                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>

<style>
#confirmationModal {
    backdrop-filter: blur(2px);
}
</style>
@endsection


@push('scripts')

    <script>
        window.pageXData = {
            page: 'orders',
        };
    </script>  

<script>
function showConfirmation(action, url, orderNumber) {
    const modal = document.getElementById('confirmationModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const confirmForm = document.getElementById('confirmForm');
    const cancelBtn = document.getElementById('cancelBtn');
    
    // Set modal content based on action
    if (action === 'dispatch') {
        modalTitle.textContent = 'Confirm Dispatch';
        modalMessage.textContent = `Are you sure you want to mark Order #${orderNumber} as dispatched?`;
    } else if (action === 'deliver') {
        modalTitle.textContent = 'Confirm Delivery';
        modalMessage.textContent = `Are you sure you want to mark Order #${orderNumber} as delivered?`;
    } else if (action === 'cancel') {
        modalTitle.textContent = 'Confirm Cancellation';
        modalMessage.textContent = `Are you sure you want to cancel Order #${orderNumber}? This action cannot be undone.`;
    }
    
    // Set form action
    confirmForm.action = url;
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Close modal on cancel button click
    cancelBtn.onclick = function() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };
    
    // Close modal when clicking outside
    modal.onclick = function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };
}
</script>



@endpush





