@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
        @include('admin.orders._breadcrump')

        <!-- Flash Messages -->
        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                class="bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-400 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                class="bg-yellow-100 dark:bg-yellow-500/20 text-red-800 dark:text-red-400 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Main Content Container -->
        <div
            class="min-h-screen rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-white/[0.03] px-5 py-7 xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-6xl">
                <!-- Order Header -->
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Order Details</h1>
                    <div class="mt-2 h-1 w-20 bg-blue-500 mx-auto"></div>
                </div>

                <!-- Order Information Card -->
                <div class="bg-white dark:bg-white/[0.03] shadow-md rounded-lg p-6 mb-8 border border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        Order Information
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-1/3 font-medium text-gray-700 dark:text-gray-400">Order Number:</div>
                                <div class="w-2/3 text-gray-600 dark:text-gray-400">{{ $order->order_number ?? 'N/A' }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-1/3 font-medium text-gray-700 dark:text-gray-400">Distributor:</div>
                                <div class="w-2/3 text-gray-600 dark:text-gray-400">{{ $order->distributor->firm_name ?? '-' }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-1/3 font-medium text-gray-700 dark:text-gray-400">Created By:</div>
                                <div class="w-2/3 text-gray-600 dark:text-gray-400">
                                    @php
                                        $creator = $order->created_by;
                                        if ($order->created_by_type === \App\Models\User::class) {
                                            echo 'Admin: ' . optional($creator)->fname;
                                        } elseif ($order->created_by_type === \App\Models\Distributor::class) {
                                            echo 'Distributor: ' . optional($creator)->name;
                                        } elseif ($order->created_by_type === \App\Models\SalesPerson::class) {
                                            echo 'SalesPerson: ' . optional($creator)->name;
                                        } else {
                                            echo '—';
                                        }
                                    @endphp
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-1/3 font-medium text-gray-700 dark:text-gray-400">Order Date:</div>
                                <div class="w-2/3 text-gray-600 dark:text-gray-400">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-1/3 font-medium text-gray-700 dark:text-gray-400">Status:</div>
                                <div class="w-2/3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
            {{ $order->status === 'confirmed'
                ? 'bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-400'
                : ($order->status === 'pending'
                    ? 'bg-yellow-100 dark:bg-yellow-500/20 text-yellow-800 dark:text-yellow-400'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ordered Products Card -->
                <div class="bg-white dark:bg-white/[0.03] shadow-md rounded-lg p-6 mb-8 border border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Ordered Products
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-400">
                           <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-700 dark:text-white">
                                <tr>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        #</th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Product</th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Rate</th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Dozen/Case</th>      
                                        
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Free Dozen/Case</th>

                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Quantity</th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($order->items as $index => $item)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-white dark:bg-transparent' : 'bg-gray-50 dark:bg-gray-800/30' }}">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white/90">
                                            {{ $item->product->type === 'variant' ? ($item->product->parent->category->name ?? '') . ' - ' . collect($item->product->attributes)->implode(', ') : $item->product->name }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white/90">
                                            ₹{{ number_format($item->rate, 2) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white/90">
                                            {{ ($item->dozen_case * $item->quantity) - ($item->free_dozen_case *  $item->quantity) }}</td>          
                                            
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white/90">
                                            {{ $item->free_dozen_case *  $item->quantity }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white/90">{{ $item->quantity }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            ₹{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Summary Card -->
                <div class="bg-white dark:bg-white/[0.03] shadow-md rounded-lg p-6 mb-8 border border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Order Summary
                    </h2>

                    <div class="grid grid-cols-2 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="font-medium text-gray-700 dark:text-gray-400">Subtotal:</span>
                            <span class="text-gray-900 dark:text-white">₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="font-medium text-gray-700 dark:text-gray-400">Discount:</span>
                            <span class="text-gray-900 dark:text-white">₹{{ number_format($order->discount, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="font-medium text-gray-700 dark:text-gray-400">SGST (9%):</span>
                            <span class="text-gray-900 dark:text-white">₹{{ number_format($order->sgst, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="font-medium text-gray-700 dark:text-gray-400">CGST (9%):</span>
                            <span class="text-gray-900 dark:text-white">₹{{ number_format($order->cgst, 2) }}</span>
                        </div>
                        <div class="col-span-2 flex justify-between py-3 border-t border-gray-200 dark:border-gray-700 mt-2">
                            <span class="font-bold text-lg text-gray-800 dark:text-white">Total Amount:</span>
                            <span class="font-bold text-lg text-blue-600 dark:text-blue-400">₹{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div x-data="{ confirmModal: false, actionType: '' }">
                    <div class="flex gap-4 flex-wrap justify-end mb-6">

   <a href="{{ route('admin.orders.index') }}"
   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-400 bg-white dark:bg-transparent hover:bg-gray-50 transition dark:hover:bg-blue-700/40 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400 dark:group-hover:text-white" fill="none" stroke="currentColor"
         viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
    </svg>
    Back to Orders
</a>


<button @click="window.print()"
    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-400 bg-white dark:bg-transparent hover:bg-gray-50 transition dark:hover:bg-blue-700/40 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400 transition-colors group-hover:text-gray-700 dark:group-hover:text-white"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
    </svg>
    Print Order
</button>


                        @if ($order->status != 'confirmed')
                            <a href="{{ route('admin.orders.edit', $order->id) }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Edit Order
                            </a>
                        @endif

                        @if ($order->status === 'pending')
                            <button @click="confirmModal = true; actionType = 'confirm'"
                                class="inline-flex items-center px-4 py-2 border border-transparent show-shadow text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Confirm
                            </button>

                            <button @click="confirmModal = true; actionType = 'cancel'"
                                class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">
                                Cancel
                            </button>
                        @endif

                        @if ($order->status === 'cancelled')
                            <button @click="confirmModal = true; actionType = 'delete'"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                Delete
                            </button>
                        @endif
                    </div>

                    <!-- Modal -->
                    <div x-show="confirmModal" x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-400/50 backdrop-blur-[32px]"
                        style="display: none;">
                        <div @click.outside="confirmModal = false"
                            class="bg-white dark:bg-white/[0.03] rounded-lg shadow-lg max-w-sm w-full p-6 border border-gray-100 dark:border-gray-700">
                            <h2 class="text-lg font-semibold mb-4 capitalize text-gray-800 dark:text-white">
                                Are you sure you want to <span x-text="actionType"></span> this?
                            </h2>

                            <div class="flex justify-end gap-3">
                                <button @click="confirmModal = false"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600 text-sm">
                                    Cancel
                                </button>

                                <form method="POST"
                                    :action="actionType === 'confirm' ? '{{ route('admin.orders.confirm', $order->id) }}' :
                                        actionType === 'cancel' ? '{{ route('admin.orders.cancel', $order->id) }}' :
                                        '{{ route('admin.orders.destroy', $order->id) }}'">
                                    @csrf
                                    <template x-if="actionType === 'delete'">
                                        @method('DELETE')
                                    </template>
                                    <template x-if="actionType === 'confirm' || actionType === 'cancel'">
                                        @method('PUT')
                                    </template>
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 hover:bg-red-700 text-base font-medium text-white sm:ml-3 sm:w-auto sm:text-sm">
                                        Yes, Proceed
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'orders',
            confirmModal: false,
            actionType: '',
        };
    </script>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        function orderTableComponent() {
            return {
                search: '{{ $search ?? '' }}',
                debounceTimeout: null,
                updateQuery() {
                    clearTimeout(this.debounceTimeout);
                    this.debounceTimeout = setTimeout(() => {
                        const base = '{{ route('admin.orders.index') }}';
                        const query = this.search.trim() ? '?search=' + encodeURIComponent(this.search.trim()) : '';
                        window.location.href = base + query;
                    }, 500);
                },
                exportToExcel() {
                    const table = document.getElementById('permissionsTable');
                    const wb = XLSX.utils.table_to_book(table, {
                        sheet: "Distributors"
                    });
                    XLSX.writeFile(wb, "distributors.xlsx");
                }
            };
        }
    </script>
@endpush

@push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .max-w-\(--breakpoint-2xl\),
            .max-w-6xl {
                max-width: 100% !important;
                padding: 0 !important;
            }

            .rounded-2xl,
            .rounded-lg {
                border-radius: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            .min-h-screen,
            .mx-auto,
            .w-full {
                min-height: auto !important;
                margin: 0 !important;
                width: 100% !important;
            }

            .max-w-6xl,
            .p-4,
            .md\:p-6,
            .px-5,
            .py-7,
            .xl\:px-10,
            .xl\:py-12 {
                padding: 0 !important;
            }

            #printable-area,
            #printable-area * {
                visibility: visible;
            }

            #printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
@endpush
