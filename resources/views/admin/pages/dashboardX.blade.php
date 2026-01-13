@extends('admin.admin-layout')

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) px-4 py-6 md:px-6 md:py-8">

    <div class="grid grid-cols-12 gap-6 lg:gap-8">

        {{-- ================= METRICS & ORDER CARDS ================= --}}
        <div class="col-span-12 space-y-8">

            {{-- METRIC CARDS --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                <!-- Distributors -->
                <div class="relative rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800/50 dark:ring-1 dark:ring-gray-700/50">
                    <div class="flex items-center">
                        <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">👤</div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Distributors</p>
                            <h3 class="text-2xl font-semibold">{{ $totalDistributor }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Sales -->
                <div class="relative rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800/50 dark:ring-1 dark:ring-gray-700/50">
                    <div class="flex items-center">
                        <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">🧑‍💼</div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Sales Persons</p>
                            <h3 class="text-2xl font-semibold">{{ $totalSalesPerson }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="relative rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800/50 dark:ring-1 dark:ring-gray-700/50">
                    <div class="flex items-center">
                        <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">📦</div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Retailers</p>
                            <h3 class="text-2xl font-semibold">{{ $totalRetailers }}</h3>
                        </div>
                    </div>
                </div>
                <!-- Products -->
                <div class="relative rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800/50 dark:ring-1 dark:ring-gray-700/50">
                    <div class="flex items-center">
                        <div class="h-12 w-12 flex items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">📦</div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Products</p>
                            <h3 class="text-2xl font-semibold">{{ $totalProducts }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Orders</h3>
            
            {{-- ORDER STATUS CARDS --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                   class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800/50">
                    <p class="text-sm text-gray-500">Pending Orders</p>
                    <h3 class="text-2xl font-semibold mt-1">{{ $ordersPending }}</h3>
                </a>

                <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}"
                   class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800/50">
                    <p class="text-sm text-gray-500">Confirmed / Invoiced</p>
                    <h3 class="text-2xl font-semibold mt-1">{{ $ordersConfirmed }}</h3>
                </a>

                <a href="{{ route('admin.orders.index', ['dispatch_status' => 'dispatched']) }}"
                   class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800/50">
                    <p class="text-sm text-gray-500">Dispatched / Delivered</p>
                    <h3 class="text-2xl font-semibold mt-1">{{ $ordersDispatchedDelivered }}</h3>
                </a>

                <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}"
                   class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800/50">
                    <p class="text-sm text-gray-500">Cancelled Orders</p>
                    <h3 class="text-2xl font-semibold mt-1">{{ $ordersCancelled }}</h3>
                </a>
            </div>
        </div>

        {{-- ================= MAP SECTION (SIDE BY SIDE ON LG+) ================= --}}
        <div class="col-span-12">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- Distributor Map --}}
                <div class="rounded-2xl border bg-white p-5 dark:bg-white/[0.03]">
                    <div class="mb-3">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Distributor Map
                        </h3>
                        <p class="text-sm text-gray-500">All distributor locations</p>
                    </div>
                    <div class="h-[300px] sm:h-[400px] lg:h-[500px] rounded-lg overflow-hidden">
                        <div id="mapOne" class="h-full w-full"></div>
                    </div>
                </div>

                {{-- Retailer Map --}}
                <div class="rounded-2xl border bg-white p-5 dark:bg-white/[0.03]">
                    <div class="mb-3">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Retailer Map
                        </h3>
                        <p class="text-sm text-gray-500">All retailer locations</p>
                    </div>
                    <div class="h-[300px] sm:h-[400px] lg:h-[500px] rounded-lg overflow-hidden">
                        <div id="mapTwo" class="h-full w-full"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= RECENT ORDERS ================= --}}
        <div class="col-span-12">
            <div class="rounded-2xl border bg-white p-4 sm:p-6 dark:bg-white/[0.03]">
                <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between"> <div> <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90"> Recent Orders </h3> </div> <div class="flex items-center gap-3"> <a href="{{ route('admin.orders.index') }}" cclass="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">See All</a> </div> </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-y text-gray-500">
                            <tr>
                                <th class="py-2 text-left">Order No</th>
                                <th>Distributor</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Created By</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="py-2">{{ $order->order_number }}</td>
                                    <td>{{ $order->distributor->firm_name }}</td>
                                    <td>{{ $order->total_amount }}</td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            if ($order->created_by_type === \App\Models\User::class) echo 'Admin';
                                            elseif ($order->created_by_type === \App\Models\Distributor::class) echo 'Distributor';
                                            elseif ($order->created_by_type === \App\Models\SalesPerson::class) echo 'Sales';
                                        @endphp
                                    </td>
                                    <td>{{ ucfirst($order->status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-gray-400">No orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection



    @push('scripts')
    <script>
        window.pageXData = {
            page: 'dashboard',
        };
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Create map focused on India
            // Initialize map centered on North-East India
            var map = L.map('mapOne').setView([26.2006, 92.9376], 6);
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            // Distributors from Laravel
            var distributors = @json($distributors);
            distributors.forEach(function(dist) {
                if (dist.latitude && dist.longitude) {
                    L.marker([dist.latitude, dist.longitude])
                        .addTo(map)
                        .bindPopup("<b>" + dist.firm_name + "</b><br>Lat: " + dist.latitude +
                            "<br>Lng: " +
                            dist.longitude);
                }
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            // Create map focused on India
            // Initialize map centered on North-East India
            var map = L.map('mapTwo').setView([26.2006, 92.9376], 6);
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            // Distributors from Laravel
            var retailers = @json($retailers);
            retailers.forEach(function(dist) {
                if (dist.latitude && dist.longitude) {
                    L.marker([dist.latitude, dist.longitude])
                        .addTo(map)
                        .bindPopup("<b>" + dist.retailer_name + "</b><br>Lat: " + dist.latitude +
                            "<br>Lng: " +
                            dist.longitude);
                }
            });
        });
    </script>

    <script>
        function toggleFullscreen(elem) {
            if (!document.fullscreenElement) {
                elem.requestFullscreen().catch(err => {
                    alert(`Error attempting fullscreen: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }
    </script>
    @endpush