@extends('admin.admin-layout')

@section('page-content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 px-4 py-6 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-1xl lg:text-3xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Welcome back! Here's what's happening with your business today.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            
            {{-- ================= LEFT COLUMN - METRICS ================= --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- METRIC CARDS GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Distributors Card -->
                    <a href="{{ route('admin.distributors.index') }}" 
                       class="group relative bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden block">
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
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">View all</span>
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </a>

                    <!-- Sales Persons Card -->
                    <a href="{{ route('admin.sales-persons.index') }}"
                       class="group relative bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden block">
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
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-xs font-medium text-purple-600 dark:text-purple-400">View all</span>
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </a>

                    <!-- Retailers Card -->
                    <a href="{{ route('admin.retailers.index') }}"
                       class="group relative bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden block">
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
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-xs font-medium text-green-600 dark:text-green-400">View all</span>
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </a>

                    <!-- Products Card -->
                    <a href="{{ route('admin.products.index') }}"
                       class="group relative bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden block">
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
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
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
           class="group relative bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden">
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
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">Require attention</span>
            </div>
        </a>

        <!-- Confirmed Orders -->
        <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}"
           class="group relative bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden">
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
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <span class="text-xs font-medium text-blue-600 dark:text-blue-400">Ready for processing</span>
            </div>
        </a>

        <!-- Dispatched/Delivered -->
        <a href="{{ route('admin.orders.index', ['dispatch_status' => 'dispatched']) }}"
           class="group relative bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden">
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
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <span class="text-xs font-medium text-green-600 dark:text-green-400">Completed shipments</span>
            </div>
        </a>

        <!-- Cancelled Orders -->
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}"
           class="group relative bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden">
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
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <span class="text-xs font-medium text-red-600 dark:text-red-400">Review required</span>
            </div>
        </a>
    </div>
</div>




                {{-- MAP SECTION --}}
                <div class="mt-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Distributor Map --}}
                        <div class="bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
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
                            <div class="h-[300px] rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                <div id="mapOne" class="h-full w-full"></div>
                            </div>
                        </div>

                        {{-- Retailer Map --}}
                        <div class="bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
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
                            <div class="h-[300px] rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                <div id="mapTwo" class="h-full w-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= RIGHT COLUMN - RECENT ORDERS ================= --}}
            <div class="lg:col-span-3">
                <div class="bg-gradient-to-br from-white to-white/90 dark:from-gray-800 dark:to-gray-800/80 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 h-full">
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
                        <div class="group bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800/50 rounded-xl p-4 hover:shadow-md transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800/50">
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
@endsection

@push('scripts')
<script>
    window.pageXData = {
        page: 'dashboard',
    };
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
            elem.requestFullscreen().catch(err => {
                console.log(`Error attempting fullscreen: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    }
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