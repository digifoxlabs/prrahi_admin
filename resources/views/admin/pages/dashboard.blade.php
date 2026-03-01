@extends('admin.admin-layout')

<style>
/* Fullscreen fix */
.fullscreen-active {
    padding: 0 !important;
    border-radius: 0 !important;
}

.fullscreen-active .map-wrapper {
    height: 100vh !important;
}

.fullscreen-active .map-container {
    height: calc(100vh - 64px) !important; /* header height */
}
</style>


@section('page-content')
<div class="min-h-screen bg-white dark:bg-gray-800 px-4 py-6 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-1xl lg:text-3xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Welcome back! Here's what's happening with your business today.</p>
        </div>

        @include('partials.flash')

        <div class="mb-8 rounded-2xl border border-gray-100 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-700">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Attendance</h2>
                    @if ($attendanceRegister)
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Register: {{ $attendanceRegister->name }} | Date: {{ now()->format('d M Y') }}
                        </p>
                    @else
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            No active attendance register is available for today.
                        </p>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $todayAttendanceEntry && $todayAttendanceEntry->in_time ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' }}">
                        IN: {{ $todayAttendanceEntry && $todayAttendanceEntry->in_time ? substr((string) $todayAttendanceEntry->in_time, 0, 5) : '--:--' }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $todayAttendanceEntry && $todayAttendanceEntry->out_time ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700' }}">
                        OUT: {{ $todayAttendanceEntry && $todayAttendanceEntry->out_time ? substr((string) $todayAttendanceEntry->out_time, 0, 5) : '--:--' }}
                    </span>
                </div>
            </div>

            @if ($attendanceRegister && $nextAttendanceAction !== 'done')
                <div class="mt-4">
                    <button type="button" id="openAttendanceMarkModal"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                        Mark {{ strtoupper($nextAttendanceAction ?? 'in') }}
                    </button>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Time and location will be captured automatically when you confirm.
                    </p>
                </div>
            @elseif ($attendanceRegister && $nextAttendanceAction === 'done')
                <p class="mt-4 text-sm text-green-700 dark:text-green-300">
                    Today's attendance is already completed.
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            
            {{-- ================= LEFT COLUMN - METRICS ================= --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- METRIC CARDS GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Distributors Card -->
                    <a href="{{ route('admin.distributors.index') }}" 
                       class="group relative dark:border-gray-700  dark:bg-gray-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden block">
                        <div class="absolute top-4 right-4 w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Distributors</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalDistributor }}</h3>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-500 dark:border-gray-300 flex items-center justify-between">
                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">View all</span>
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </a>

                    <!-- Sales Persons Card -->
                    <a href="{{ route('admin.sales-persons.index') }}"
                       class="group relative dark:border-gray-700  dark:bg-gray-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden block">
                        <div class="absolute top-4 right-4 w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Sales Persons</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalSalesPerson }}</h3>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-500 dark:border-gray-300 flex items-center justify-between">
                            <span class="text-xs font-medium text-purple-600 dark:text-purple-400">View all</span>
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </a>

                    <!-- Retailers Card -->
                    <a href="{{ route('admin.retailers.index') }}"
                       class="group relative dark:border-gray-700  dark:bg-gray-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden block">
                        <div class="absolute top-4 right-4 w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Retailers</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalRetailers }}</h3>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-500 dark:border-gray-300 flex items-center justify-between">
                            <span class="text-xs font-medium text-green-600 dark:text-green-400">View all</span>
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </a>

                    <!-- Products Card -->
                    <a href="{{ route('admin.products.index') }}"
                       class="group relative dark:border-gray-700  dark:bg-gray-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden block">
                        <div class="absolute top-4 right-4 w-12 h-12 bg-orange-500/10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Products</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalProducts }}</h3>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-500 dark:border-gray-300 flex items-center justify-between">
                            <span class="text-xs font-medium text-orange-600 dark:text-orange-400">View all</span>
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </a>
                </div>


                {{-- ORDER STATUS CARDS - PROFESSIONAL VERSION --}}
<div class="mt-8">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Order Status Overview</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Pending Orders -->
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
           class="group relative dark:border-gray-700  dark:bg-gray-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-yellow-500/5 rounded-full"></div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Orders</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $ordersPending }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-500 dark:border-gray-300">
                <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">Require attention</span>
            </div>
        </a>

        <!-- Confirmed Orders -->
        <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}"
           class="group relative dark:border-gray-700  dark:bg-gray-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full"></div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Confirmed / Invoiced</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $ordersConfirmed }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-500 dark:border-gray-300">
                <span class="text-xs font-medium text-blue-600 dark:text-blue-400">Ready for processing</span>
            </div>
        </a>

        <!-- Dispatched/Delivered -->
        <a href="{{ route('admin.orders.index', ['dispatch_status' => 'dispatched']) }}"
           class="group relative dark:border-gray-700  dark:bg-gray-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-500/5 rounded-full"></div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Dispatched / Delivered</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $ordersDispatchedDelivered }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-500 dark:border-gray-300">
                <span class="text-xs font-medium text-green-600 dark:text-green-400">Completed shipments</span>
            </div>
        </a>

        <!-- Cancelled Orders -->
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}"
           class="group relative dark:border-gray-700  dark:bg-gray-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-500/5 rounded-full"></div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cancelled Orders</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $ordersCancelled }}</h3>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-500 dark:border-gray-300">
                <span class="text-xs font-medium text-red-600 dark:text-red-400">Review required</span>
            </div>
        </a>
    </div>
</div>




                {{-- MAP SECTION --}}
                <div class="mt-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Distributor Map --}}
                        <div class="dark:border-gray-700  dark:bg-gray-700 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="mb-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Distributor Map</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">All distributor locations across regions</p>
                                    </div>
                                    <button onclick="toggleFullscreen(this.closest('.rounded-2xl'))" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                                <div class="map-wrapper h-[300px] sm:h-[400px] lg:h-[500px] rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <div id="mapOne" class="map-container h-full w-full"></div>
                                </div>
                        </div>

                        {{-- Retailer Map --}}
                        <div class="dark:border-gray-700  dark:bg-gray-700 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="mb-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Retailer Map</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Retail network distribution points</p>
                                    </div>
                                    <button onclick="toggleFullscreen(this.closest('.rounded-2xl'))" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                                   <div class="map-wrapper h-[300px] sm:h-[400px] lg:h-[500px] rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <div id="mapTwo" class="map-container h-full w-full"></div>
                                </div>
                        </div>
                    </div>
                </div>


            </div>

            {{-- ================= RIGHT COLUMN - RECENT ORDERS ================= --}}
            <div class="lg:col-span-3">
                <div class="dark:border-gray-700  dark:bg-gray-200 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 h-full">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Latest order activities</p>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-medium rounded-xl transition-all duration-300 hover:shadow-lg">
                            View All
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>

                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                        @forelse ($orders as $order)
                        <div class="group bg-gray dark:bg-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800/50">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                            @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @else bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $order->distributor->firm_name }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">₹{{ number_format($order->total_amount) }}</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $order->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <span class="text-gray-600 dark:text-gray-400">
                                        @php
                                            if ($order->created_by_type === \App\Models\User::class) echo 'Admin';
                                            elseif ($order->created_by_type === \App\Models\Distributor::class) echo 'Distributor';
                                            elseif ($order->created_by_type === \App\Models\SalesPerson::class) echo 'Sales';
                                        @endphp
                                    </span>
                                </div>
                                {{-- <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                                    View Details →
                                </button> --}}
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">View Details →</a>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <h4 class="text-gray-900 dark:text-white font-medium">No orders found</h4>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">No recent orders available</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="attendanceMarkModal" class="fixed inset-0 z-[200] hidden items-center justify-center p-4">
    <div id="attendanceMarkBackdrop" class="absolute inset-0 bg-black/50"></div>
    <div class="relative z-[201] w-full max-w-md rounded-xl bg-white p-5 shadow-xl dark:bg-gray-900">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Attendance</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            You are about to mark <span class="font-semibold text-blue-600">{{ strtoupper($nextAttendanceAction ?? 'IN') }}</span>.
        </p>

        <div class="mt-4 space-y-2 rounded-lg border border-gray-200 p-3 text-sm dark:border-gray-700">
            <p class="text-gray-700 dark:text-gray-200">
                Time: <span id="attendanceCurrentTime" class="font-medium">--:--:--</span>
            </p>
            <p id="attendanceLocationMsg" class="text-gray-500 dark:text-gray-400">Capturing location...</p>
        </div>

        <form method="POST" action="{{ route('admin.dashboard.attendance.mark') }}" id="attendanceMarkForm" class="mt-4">
            @csrf
            <input type="hidden" name="latitude" id="attendanceLatitude">
            <input type="hidden" name="longitude" id="attendanceLongitude">

            <div class="flex justify-end gap-2">
                <button type="button" id="attendanceMarkCancel"
                    class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" id="attendanceMarkSubmit"
                    class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                    Confirm
                </button>
            </div>
        </form>
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
    (function () {
        const openBtn = document.getElementById('openAttendanceMarkModal');
        const modal = document.getElementById('attendanceMarkModal');
        const backdrop = document.getElementById('attendanceMarkBackdrop');
        const cancelBtn = document.getElementById('attendanceMarkCancel');
        const form = document.getElementById('attendanceMarkForm');
        const submitBtn = document.getElementById('attendanceMarkSubmit');
        const timeEl = document.getElementById('attendanceCurrentTime');
        const locationMsgEl = document.getElementById('attendanceLocationMsg');
        const latitudeEl = document.getElementById('attendanceLatitude');
        const longitudeEl = document.getElementById('attendanceLongitude');
        let timer = null;

        if (!openBtn || !modal) {
            return;
        }

        const setSubmitReady = (isReady) => {
            submitBtn.disabled = !isReady;
            submitBtn.textContent = isReady ? 'Confirm' : 'Capturing...';
            submitBtn.classList.toggle('opacity-60', !isReady);
            submitBtn.classList.toggle('cursor-not-allowed', !isReady);
        };

        const updateTime = () => {
            const now = new Date();
            timeEl.textContent = now.toLocaleTimeString('en-IN', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
        };

        const captureLocation = () => {
            latitudeEl.value = '';
            longitudeEl.value = '';
            setSubmitReady(false);

            if (!navigator.geolocation) {
                locationMsgEl.textContent = 'Geolocation is not supported by this browser.';
                setSubmitReady(true);
                return;
            }

            locationMsgEl.textContent = 'Capturing location...';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    latitudeEl.value = Number(position.coords.latitude).toFixed(7);
                    longitudeEl.value = Number(position.coords.longitude).toFixed(7);
                    locationMsgEl.textContent = 'Location captured successfully.';
                    setSubmitReady(true);
                },
                () => {
                    locationMsgEl.textContent = 'Unable to capture location. Attendance will still be marked with time.';
                    setSubmitReady(true);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                }
            );
        };

        const openModal = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            updateTime();
            timer = window.setInterval(updateTime, 1000);
            setSubmitReady(false);
            captureLocation();
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            if (timer) {
                window.clearInterval(timer);
                timer = null;
            }
        };

        openBtn.addEventListener('click', openModal);
        backdrop.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('flex')) {
                closeModal();
            }
        });

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
        });
    })();
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize map centered on North-East India
        var map = L.map('mapOne').setView([26.2006, 92.9376], 6);
        
        // Add OpenStreetMap tiles with dark mode support
        const isDarkMode = document.documentElement.classList.contains('dark');
        const tileLayer = isDarkMode 
            ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
            : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        
        L.tileLayer(tileLayer, {
            maxZoom: 19,
            attribution: isDarkMode 
                ? '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                : '&copy; OpenStreetMap contributors'
        }).addTo(map);
        
        // Add custom markers for distributors
        var distributors = @json($distributors);
        distributors.forEach(function(dist) {
            if (dist.latitude && dist.longitude) {
                const customIcon = L.divIcon({
                    className: 'custom-distributor-marker',
                    html: `<div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                             <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                               <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                             </svg>
                           </div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 24]
                });
                
                L.marker([dist.latitude, dist.longitude], { icon: customIcon })
                    .addTo(map)
                    .bindPopup(`
                        <div class="font-sans">
                            <h4 class="font-bold text-gray-900 mb-1">${dist.firm_name}</h4>
                            <p class="text-sm text-gray-600">Location: ${dist.latitude}, ${dist.longitude}</p>
                            <button class="mt-2 px-3 py-1 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors">
                                View Details
                            </button>
                        </div>
                    `);
            }
        });
    });
    
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize map centered on North-East India
        var map = L.map('mapTwo').setView([26.2006, 92.9376], 6);
        
        // Add OpenStreetMap tiles with dark mode support
        const isDarkMode = document.documentElement.classList.contains('dark');
        const tileLayer = isDarkMode 
            ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
            : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        
        L.tileLayer(tileLayer, {
            maxZoom: 19,
            attribution: isDarkMode 
                ? '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                : '&copy; OpenStreetMap contributors'
        }).addTo(map);
        
        // Add custom markers for retailers
        var retailers = @json($retailers);
        retailers.forEach(function(retailer) {
            if (retailer.latitude && retailer.longitude) {
                const customIcon = L.divIcon({
                    className: 'custom-retailer-marker',
                    html: `<div class="w-6 h-6 bg-gradient-to-br from-green-500 to-green-600 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                             <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                               <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                             </svg>
                           </div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 24]
                });
                
                L.marker([retailer.latitude, retailer.longitude], { icon: customIcon })
                    .addTo(map)
                    .bindPopup(`
                        <div class="font-sans">
                            <h4 class="font-bold text-gray-900 mb-1">${retailer.retailer_name}</h4>
                            <p class="text-sm text-gray-600">Location: ${retailer.latitude}, ${retailer.longitude}</p>
                            <button class="mt-2 px-3 py-1 bg-green-500 text-white text-xs rounded-lg hover:bg-green-600 transition-colors">
                                View Details
                            </button>
                        </div>
                    `);
            }
        });
    });
</script>

<script>

function toggleFullscreen(elem) {
    if (!document.fullscreenElement) {
        elem.classList.add('fullscreen-active');
        elem.requestFullscreen();

        setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
        }, 300);
    } else {
        document.exitFullscreen();
    }
}

document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement) {
        document
            .querySelectorAll('.fullscreen-active')
            .forEach(el => el.classList.remove('fullscreen-active'));
    }
});









</script>

<style>
    /* Custom scrollbar for dark mode */
    .dark ::-webkit-scrollbar {
        width: 6px;
    }
    
    .dark ::-webkit-scrollbar-track {
        background: rgba(75, 85, 99, 0.3);
        border-radius: 3px;
    }
    
    .dark ::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.5);
        border-radius: 3px;
    }
    
    .dark ::-webkit-scrollbar-thumb:hover {
        background: rgba(209, 213, 219, 0.6);
    }
    
    /* Custom marker styles */
    .custom-distributor-marker, .custom-retailer-marker {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }
    
    .dark .custom-distributor-marker, .dark .custom-retailer-marker {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.4));
    }
</style>
@endpush
