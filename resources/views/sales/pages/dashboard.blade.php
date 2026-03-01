@extends('sales.layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
        @include('partials.flash')

        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <div class="col-span-12">
                <h1 class="mb-4 text-2xl font-semibold text-gray-800 dark:text-white">
                    Sales Dashboard
                </h1>
            </div>

            <div
                class="col-span-12 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">My Attendance</h2>
                        @if ($attendanceRegister)
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Register: {{ $attendanceRegister->name }} | Date: {{ now()->format('d M Y') }}
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                No active attendance register is available for today.
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $todayAttendanceEntry && $todayAttendanceEntry->in_time ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' }}">
                            IN:
                            {{ $todayAttendanceEntry && $todayAttendanceEntry->in_time ? substr((string) $todayAttendanceEntry->in_time, 0, 5) : '--:--' }}
                        </span>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $todayAttendanceEntry && $todayAttendanceEntry->out_time ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700' }}">
                            OUT:
                            {{ $todayAttendanceEntry && $todayAttendanceEntry->out_time ? substr((string) $todayAttendanceEntry->out_time, 0, 5) : '--:--' }}
                        </span>
                    </div>
                </div>

                @if ($attendanceRegister && $nextAttendanceAction !== 'done')
                    <div class="mt-4">
                        <button type="button" id="openSalesAttendanceMarkModal"
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

            <div class="col-span-12 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
                <a href="{{ route('sales.orders.index') }}"
                    class="group relative rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                My Orders
                            </p>
                            <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white">
                                View Orders
                            </h3>
                        </div>

                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white">
                            📦
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        Track and manage all orders placed by you.
                    </p>
                </a>

                <a href="{{ route('sales.retailers.index') }}"
                    class="group relative rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                My Retailers
                            </p>
                            <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white">
                                Retail Network
                            </h3>
                        </div>

                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-600 transition group-hover:bg-green-600 group-hover:text-white">
                            🏪
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        View and manage your appointed retailers.
                    </p>
                </a>

                <a href="{{ route('sales.distributors.index') }}"
                    class="group relative rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                My Distributors
                            </p>
                            <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white">
                                Distributor Network
                            </h3>
                        </div>

                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-600 transition group-hover:bg-purple-600 group-hover:text-white">
                            📊
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        View and manage your appointed distributors.
                    </p>
                </a>

                <a href="{{ route('sales.retail.orders.index') }}"
                    class="group relative rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Retail Orders
                            </p>
                            <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white">
                                Orders from Retailers
                            </h3>
                        </div>

                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-orange-600 transition group-hover:bg-orange-600 group-hover:text-white">
                            🧾
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        Review orders placed by your retailers.
                    </p>
                </a>
            </div>
        </div>

        <div id="salesAttendanceMarkModal" class="fixed inset-0 z-[200] hidden items-center justify-center p-4">
            <div id="salesAttendanceMarkBackdrop" class="absolute inset-0 bg-black/50"></div>
            <div class="relative z-[201] w-full max-w-md rounded-xl bg-white p-5 shadow-xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Attendance</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    You are about to mark
                    <span class="font-semibold text-blue-600">{{ strtoupper($nextAttendanceAction ?? 'IN') }}</span>.
                </p>

                <div class="mt-4 space-y-2 rounded-lg border border-gray-200 p-3 text-sm dark:border-gray-700">
                    <p class="text-gray-700 dark:text-gray-200">
                        Time: <span id="salesAttendanceCurrentTime" class="font-medium">--:--:--</span>
                    </p>
                    <p id="salesAttendanceLocationMsg" class="text-gray-500 dark:text-gray-400">Capturing location...</p>
                </div>

                <form method="POST" action="{{ route('sales.dashboard.attendance.mark') }}" id="salesAttendanceMarkForm"
                    class="mt-4">
                    @csrf
                    <input type="hidden" name="latitude" id="salesAttendanceLatitude">
                    <input type="hidden" name="longitude" id="salesAttendanceLongitude">

                    <div class="flex justify-end gap-2">
                        <button type="button" id="salesAttendanceMarkCancel"
                            class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                            Cancel
                        </button>
                        <button type="submit" id="salesAttendanceMarkSubmit"
                            class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
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
            page: 'dashboard',
        };
    </script>

    <script>
        (function() {
            const openBtn = document.getElementById('openSalesAttendanceMarkModal');
            const modal = document.getElementById('salesAttendanceMarkModal');
            const backdrop = document.getElementById('salesAttendanceMarkBackdrop');
            const cancelBtn = document.getElementById('salesAttendanceMarkCancel');
            const form = document.getElementById('salesAttendanceMarkForm');
            const submitBtn = document.getElementById('salesAttendanceMarkSubmit');
            const timeEl = document.getElementById('salesAttendanceCurrentTime');
            const locationMsgEl = document.getElementById('salesAttendanceLocationMsg');
            const latitudeEl = document.getElementById('salesAttendanceLatitude');
            const longitudeEl = document.getElementById('salesAttendanceLongitude');
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
                    }, {
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
@endpush
