@extends('admin.admin-layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">





        <div class="grid grid-cols-12 gap-4 md:gap-6">





            {{-- Left Side --}}

            <div class="col-span-12 space-y-6 xl:col-span-12">




                <!-- Metric Group One -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <!-- Metric Item Start -->
                    <div
                        class="relative overflow-hidden rounded-xl bg-white dark:border-gray-800 dark:bg-gray-dark p-6 shadow-sm transition-all duration-200 hover:shadow-md dark:bg-gray-800/50 dark:shadow-none dark:ring-1 dark:ring-gray-700/50">
                        <div
                            class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-blue-100 opacity-20 dark:bg-blue-900/30">
                        </div>
                        <div class="relative z-10 flex items-center">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 shadow-sm dark:bg-blue-900/30 dark:text-blue-400">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.80443 5.60156C7.59109 5.60156 6.60749 6.58517 6.60749 7.79851C6.60749 9.01185 7.59109 9.99545 8.80443 9.99545C10.0178 9.99545 11.0014 9.01185 11.0014 7.79851C11.0014 6.58517 10.0178 5.60156 8.80443 5.60156ZM5.10749 7.79851C5.10749 5.75674 6.76267 4.10156 8.80443 4.10156C10.8462 4.10156 12.5014 5.75674 12.5014 7.79851C12.5014 9.84027 10.8462 11.4955 8.80443 11.4955C6.76267 11.4955 5.10749 9.84027 5.10749 7.79851ZM4.86252 15.3208C4.08769 16.0881 3.70377 17.0608 3.51705 17.8611C3.48384 18.0034 3.5211 18.1175 3.60712 18.2112C3.70161 18.3141 3.86659 18.3987 4.07591 18.3987H13.4249C13.6343 18.3987 13.7992 18.3141 13.8937 18.2112C13.9797 18.1175 14.017 18.0034 13.9838 17.8611C13.7971 17.0608 13.4132 16.0881 12.6383 15.3208C11.8821 14.572 10.6899 13.955 8.75042 13.955C6.81096 13.955 5.61877 14.572 4.86252 15.3208ZM3.8071 14.2549C4.87163 13.2009 6.45602 12.455 8.75042 12.455C11.0448 12.455 12.6292 13.2009 13.6937 14.2549C14.7397 15.2906 15.2207 16.5607 15.4446 17.5202C15.7658 18.8971 14.6071 19.8987 13.4249 19.8987H4.07591C2.89369 19.8987 1.73504 18.8971 2.05628 17.5202C2.28015 16.5607 2.76117 15.2906 3.8071 14.2549ZM15.3042 11.4955C14.4702 11.4955 13.7006 11.2193 13.0821 10.7533C13.3742 10.3314 13.6054 9.86419 13.7632 9.36432C14.1597 9.75463 14.7039 9.99545 15.3042 9.99545C16.5176 9.99545 17.5012 9.01185 17.5012 7.79851C17.5012 6.58517 16.5176 5.60156 15.3042 5.60156C14.7039 5.60156 14.1597 5.84239 13.7632 6.23271C13.6054 5.73284 13.3741 5.26561 13.082 4.84371C13.7006 4.37777 14.4702 4.10156 15.3042 4.10156C17.346 4.10156 19.0012 5.75674 19.0012 7.79851C19.0012 9.84027 17.346 11.4955 15.3042 11.4955ZM19.9248 19.8987H16.3901C16.7014 19.4736 16.9159 18.969 16.9827 18.3987H19.9248C20.1341 18.3987 20.2991 18.3141 20.3936 18.2112C20.4796 18.1175 20.5169 18.0034 20.4837 17.861C20.2969 17.0607 19.913 16.088 19.1382 15.3208C18.4047 14.5945 17.261 13.9921 15.4231 13.9566C15.2232 13.6945 14.9995 13.437 14.7491 13.1891C14.5144 12.9566 14.262 12.7384 13.9916 12.5362C14.3853 12.4831 14.8044 12.4549 15.2503 12.4549C17.5447 12.4549 19.1291 13.2008 20.1936 14.2549C21.2395 15.2906 21.7206 16.5607 21.9444 17.5202C22.2657 18.8971 21.107 19.8987 19.9248 19.8987Z"
                                        fill="currentColor" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Distributors</p>
                                <h3 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $totalDistributor }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->

                    <!-- Metric Item Start -->
                    <div
                        class="relative overflow-hidden rounded-xl bg-white p-6 dark:border-gray-800 dark:bg-gray-dark shadow-sm transition-all duration-200 hover:shadow-md dark:bg-gray-800/50 dark:shadow-none dark:ring-1 dark:ring-gray-700/50">
                        <div
                            class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-purple-100 opacity-20 dark:bg-purple-900/30">
                        </div>
                        <div class="relative z-10 flex items-center">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600 shadow-sm dark:bg-purple-900/30 dark:text-purple-400">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.665 3.75621C11.8762 3.65064 12.1247 3.65064 12.3358 3.75621L18.7807 6.97856L12.3358 10.2009C12.1247 10.3065 11.8762 10.3065 11.665 10.2009L5.22014 6.97856L11.665 3.75621ZM4.29297 8.19203V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0366V11.6513C11.1631 11.6205 11.0777 11.5843 10.9942 11.5426L4.29297 8.19203ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19202L13.0066 11.5426C12.9229 11.5844 12.8372 11.6208 12.75 11.6516V20.037ZM13.0066 2.41456C12.3732 2.09786 11.6277 2.09786 10.9942 2.41456L4.03676 5.89319C3.27449 6.27432 2.79297 7.05342 2.79297 7.90566V16.0946C2.79297 16.9469 3.27448 17.726 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.726 21.2079 16.9469 21.2079 16.0946V7.90566C21.2079 7.05342 20.7264 6.27432 19.9641 5.89319L13.0066 2.41456Z"
                                        fill="currentColor" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sales Persons</p>
                                <h3 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $totalSalesPerson }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->

                    <!-- Metric Item Start -->
                    <div
                        class="relative overflow-hidden rounded-xl bg-white p-6 dark:border-gray-800 dark:bg-gray-dark shadow-sm transition-all duration-200 hover:shadow-md dark:bg-gray-800/50 dark:shadow-none dark:ring-1 dark:ring-gray-700/50">
                        <div
                            class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-green-100 opacity-20 dark:bg-green-900/30">
                        </div>
                        <div class="relative z-10 flex items-center">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600 shadow-sm dark:bg-green-900/30 dark:text-green-400">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.665 3.75621C11.8762 3.65064 12.1247 3.65064 12.3358 3.75621L18.7807 6.97856L12.3358 10.2009C12.1247 10.3065 11.8762 10.3065 11.665 10.2009L5.22014 6.97856L11.665 3.75621ZM4.29297 8.19203V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0366V11.6513C11.1631 11.6205 11.0777 11.5843 10.9942 11.5426L4.29297 8.19203ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19202L13.0066 11.5426C12.9229 11.5844 12.8372 11.6208 12.75 11.6516V20.037ZM13.0066 2.41456C12.3732 2.09786 11.6277 2.09786 10.9942 2.41456L4.03676 5.89319C3.27449 6.27432 2.79297 7.05342 2.79297 7.90566V16.0946C2.79297 16.9469 3.27448 17.726 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.726 21.2079 16.9469 21.2079 16.0946V7.90566C21.2079 7.05342 20.7264 6.27432 19.9641 5.89319L13.0066 2.41456Z"
                                        fill="currentColor" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Products</p>
                                <h3 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $totalProducts }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->
                </div>
                <!-- Metric Group One -->


























                <!-- ====== Chart One Start -->
                <div
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Monthly Sales
                        </h3>

                        <div x-data="{ openDropDown: false }" class="relative h-fit">
                            <button @click="openDropDown = !openDropDown"
                                :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                    'text-gray-400 hover:text-gray-700 dark:hover:text-white'">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                        fill="" />
                                </svg>
                            </button>
                            <div x-show="openDropDown" @click.outside="openDropDown = false"
                                class="absolute right-0 z-40 w-40 p-2 space-y-1 bg-white border border-gray-200 top-full rounded-2xl shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark">
                                <button
                                    class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    View More
                                </button>
                                <button
                                    class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <div class="-ml-5 min-w-[650px] pl-2 xl:min-w-full">
                            <div id="chartOne" class="-ml-5 h-full min-w-[650px] pl-2 xl:min-w-full"></div>
                        </div>
                    </div>
                </div>










            </div>
















            <div class="col-span-12 xl:col-span-5 h-screen">



                <!-- ====== Map One Start -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6 h-[600px] flex flex-col relative">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Distributors Location
                            </h3>
                            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                                Number of distributors plotted on India Map
                            </p>
                        </div>

                        <!-- Fullscreen Button -->
                        <button onclick="toggleFullscreen(document.getElementById('mapWrapper'))"
                            aria-label="Toggle fullscreen map"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm rounded-lg
                    bg-gray-200 text-gray-800 border border-gray-300 shadow-sm
                    hover:bg-gray-300 hover:text-gray-900
                    dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600
                    dark:hover:bg-gray-600 dark:hover:text-white
                    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                    transition duration-150 ease-in-out group">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-gray-600 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 3H5a2 2 0 00-2 2v3m0 8v3a2 2 0 002 2h3m8-16h3a2 2 0 012 2v3M8 21h3a2 2 0 002-2v-3" />
                            </svg>
                            <span class="leading-none">Fullscreen</span>
                        </button>

                    </div>

                    <!-- Map container -->
                    <div id="mapWrapper"
                        class="my-6 overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900 flex-1">
                        <div id="mapOne" class="h-full w-full rounded-lg"></div>
                    </div>
                </div>
                <!-- ====== Map One End -->





            </div>




            <div class="col-span-12 xl:col-span-7">







                <!-- ====== Table  Start -->
                <div
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
                    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Recent Orders
                            </h3>
                        </div>

                        <div class="flex items-center gap-3">

                            <a
                                href="{{ route('admin.orders.index') }}"cclass="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">See
                                All</a>


                        </div>
                    </div>

                    <div class="w-full overflow-x-auto">

                        <table class="min-w-full">
                            <!-- table header start -->
                            <thead>
                                <tr class="border-gray-100 border-y dark:border-gray-800">
                                    <th class="py-3">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                Order No
                                            </p>
                                        </div>
                                    </th>
                                    <th class="py-3">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                Distributor
                                            </p>
                                        </div>
                                    </th>
                                    <th class="py-3">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                Amount
                                            </p>
                                        </div>
                                    </th>
                                    <th class="py-3">
                                        <div class="flex items-center col-span-2">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                Date
                                            </p>
                                        </div>
                                    </th>

                                    <th class="py-3">
                                        <div class="flex items-center col-span-2">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                Created By
                                            </p>
                                        </div>
                                    </th>

                                    <th class="py-3">
                                        <div class="flex items-center col-span-2">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                Status
                                            </p>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <!-- table header end -->

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">


                                @forelse ($orders as $index => $order)
                                    <tr>
                                        <td class="py-3">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $order->order_number }}
                                                </p>
                                            </div>
                                        </td>

                                        <td class="py-3">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $order->distributor->firm_name }}
                                                </p>
                                            </div>

                                        </td>

                                        <td class="py-3">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $order->total_amount }}
                                                </p>
                                            </div>
                                        </td>

                                        <td class="py-3">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $order->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="p       

                                        <td class="py-3">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">

                                                    @php

                                                        $creator = $order->created_by;
                                                        if ($order->created_by_type === \App\Models\User::class) {
                                                            echo 'Admin: ' . optional($creator)->fname;
                                                        } elseif (
                                                            $order->created_by_type === \App\Models\Distributor::class
                                                        ) {
                                                            echo 'Distributor: ' . optional($creator)->name;
                                                        } elseif (
                                                            $order->created_by_type === \App\Models\SalesPerson::class
                                                        ) {
                                                            echo 'SalesPerson: ' . optional($creator)->name;
                                                        } else {
                                                            echo '—';
                                                        }

                                                    @endphp




                                                </p>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">

                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $order->status === 'confirmed'
                                                        ? 'bg-green-100 text-green-800'
                                                        : ($order->status === 'pending'
                                                            ? 'bg-yellow-100 text-yellow-800'
                                                            : 'bg-gray-100 text-gray-800') }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>



                                                </p>


                                            </div>
                                        </td>



                                    </tr>



                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-3 text-center text-gray-400">No Orders
                                            found.
                                        </td>
                                    </tr>
                                @endforelse




                                <!-- table body end -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- ====== Table One End -->










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
                            .bindPopup("<b>" + dist.firm_name + "</b><br>Lat: " + dist.latitude + "<br>Lng: " +
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
