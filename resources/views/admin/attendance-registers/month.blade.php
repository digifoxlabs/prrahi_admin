@extends('admin.admin-layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
        @include('admin.attendance-registers._breadcrump', ['pageName' => 'Attendance Register'])

        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                class="mb-4 rounded bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
                class="mb-4 rounded bg-yellow-100 p-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div
            class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-full">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white/90">{{ $attendanceRegister->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Period: {{ $attendanceRegister->start_date->format('d M Y') }} -
                            {{ $attendanceRegister->end_date->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.attendance-registers.month.export', ['attendance_register' => $attendanceRegister->id, 'year' => $viewMonth->year, 'month' => $viewMonth->month]) }}"
                            class="rounded-md border border-emerald-300 bg-emerald-600 px-3 py-2 text-sm text-white hover:bg-emerald-700 dark:border-emerald-700">
                            Export Excel
                        </a>
                        <a href="{{ route('admin.attendance-registers.index') }}"
                            class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                            Back to Registers
                        </a>
                    </div>
                </div>

                <div class="mb-6 flex flex-wrap gap-2">
                    @foreach ($months as $monthObj)
                        <a href="{{ route('admin.attendance-registers.month', ['attendance_register' => $attendanceRegister->id, 'year' => $monthObj->year, 'month' => $monthObj->month]) }}"
                            class="rounded-lg px-3 py-2 text-sm {{ $monthObj->year === $viewMonth->year && $monthObj->month === $viewMonth->month ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200' }}">
                            {{ $monthObj->format('M Y') }}
                        </a>
                    @endforeach
                </div>

                @if (Auth::guard('admin')->user()->hasPermission('edit_attendance'))
                    <div class="mb-6 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white/90">Holiday / Date Override</h3>
                        <form method="POST"
                            action="{{ route('admin.attendance-registers.overrides.save', $attendanceRegister) }}"
                            class="grid grid-cols-1 gap-3 md:grid-cols-5">
                            @csrf
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Date</label>
                                <input type="date" name="attendance_date" required
                                    min="{{ $attendanceRegister->start_date->format('Y-m-d') }}"
                                    max="{{ $attendanceRegister->end_date->format('Y-m-d') }}"
                                    value="{{ old('attendance_date', $viewMonth->copy()->startOfMonth()->lt($attendanceRegister->start_date) ? $attendanceRegister->start_date->format('Y-m-d') : $viewMonth->copy()->startOfMonth()->format('Y-m-d')) }}"
                                    class="js-native-picker w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">IN Time</label>
                                <input type="time" name="in_time" value="{{ old('in_time') }}"
                                    class="js-native-picker w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">OUT Time</label>
                                <input type="time" name="out_time" value="{{ old('out_time') }}"
                                    class="js-native-picker w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                            </div>
                            <div class="flex items-center gap-2 md:pt-7">
                                <input id="is_holiday" type="checkbox" name="is_holiday" value="1"
                                    {{ old('is_holiday') ? 'checked' : '' }}>
                                <label for="is_holiday" class="text-sm text-gray-700 dark:text-gray-200">Holiday</label>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Holiday Name</label>
                                <input type="text" name="holiday_name" value="{{ old('holiday_name') }}" placeholder="Optional"
                                    class="w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                            </div>
                            <div class="md:col-span-5 flex flex-wrap gap-2">
                                <button type="submit" name="action" value="save"
                                    class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">Save Override</button>
                                <button type="submit" name="action" value="clear"
                                    class="rounded border border-red-200 px-4 py-2 text-sm text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950/40">Clear
                                    Override</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="mb-4 flex flex-wrap items-center gap-3 text-xs">
                    <span class="inline-flex items-center rounded bg-green-100 px-2 py-1 text-green-700">IN / OUT Marked</span>
                    <span class="inline-flex items-center rounded bg-red-100 px-2 py-1 text-red-700">Absent</span>
                    <span class="inline-flex items-center rounded bg-yellow-100 px-2 py-1 text-yellow-800">Holiday</span>
                    <span class="inline-flex items-center rounded bg-gray-100 px-2 py-1 text-gray-700">Out of Register</span>
                    <span class="inline-flex items-center rounded bg-slate-100 px-2 py-1 text-slate-700">Not Marked</span>
                    <span class="text-gray-500 dark:text-gray-400">Only {{ \Carbon\Carbon::today()->format('d M Y') }} can be edited.</span>
                </div>

                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="w-full sm:max-w-sm">
                        <label for="employeeSearchInput" class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                            Search Employee
                        </label>
                        <div class="relative">
                            <input id="employeeSearchInput" type="text" placeholder="Type employee name..."
                                class="w-full rounded border border-gray-300 px-3 py-2 pr-8 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                            <button type="button" id="employeeSearchClear"
                                class="absolute right-2 top-1/2 hidden -translate-y-1/2 text-sm text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
                                aria-label="Clear search" title="Clear">
                                ×
                            </button>
                        </div>
                    </div>
                    <p id="employeeSearchInfo" class="text-xs text-gray-500 dark:text-gray-400"></p>
                </div>

                @php
                    $dayLocationData = [];
                    foreach ($days as $day) {
                        $dateKey = $day->toDateString();
                        $dayPoints = [];

                        foreach ($participants as $participant) {
                            $entry = $entries->get($participant->id . '|' . $dateKey);
                            if (! $entry) {
                                continue;
                            }

                            $userType = $participant->employee_type === 'user' ? 'User' : 'Sales Person';
                            $inTime = $entry->in_time ? substr((string) $entry->in_time, 0, 5) : null;
                            $outTime = $entry->out_time ? substr((string) $entry->out_time, 0, 5) : null;

                            if (! is_null($entry->in_latitude) && ! is_null($entry->in_longitude)) {
                                $dayPoints[] = [
                                    'name' => $participant->display_name,
                                    'user_type' => $userType,
                                    'mark_type' => 'IN',
                                    'time' => $inTime,
                                    'latitude' => (float) $entry->in_latitude,
                                    'longitude' => (float) $entry->in_longitude,
                                ];
                            }

                            if (! is_null($entry->out_latitude) && ! is_null($entry->out_longitude)) {
                                $dayPoints[] = [
                                    'name' => $participant->display_name,
                                    'user_type' => $userType,
                                    'mark_type' => 'OUT',
                                    'time' => $outTime,
                                    'latitude' => (float) $entry->out_latitude,
                                    'longitude' => (float) $entry->out_longitude,
                                ];
                            }
                        }

                        $dayLocationData[$dateKey] = $dayPoints;
                    }
                @endphp

                <div id="attendanceTableScroll" class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-max text-xs">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="sticky left-0 z-20 w-64 border-r border-gray-200 bg-gray-100 px-3 py-2 text-left dark:border-gray-700 dark:bg-gray-800">
                                    Employee
                                </th>
                                @foreach ($days as $day)
                                    @php $dayDate = $day->toDateString(); @endphp
                                    @php $locationCount = count($dayLocationData[$dayDate] ?? []); @endphp
                                    <th
                                        @if ($dayDate === $today) id="attendanceTodayHeader" @endif
                                        data-date="{{ $dayDate }}"
                                        class="border-r border-gray-200 px-2 py-2 text-center dark:border-gray-700 {{ $dayDate === $today ? 'bg-blue-50 dark:bg-blue-900/40' : '' }}">
                                        <div>{{ $day->format('d') }}</div>
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ $day->format('D') }}</div>
                                        <button type="button"
                                            class="js-open-day-map mt-1 inline-flex items-center justify-center rounded px-1 text-[11px] {{ $locationCount > 0 ? 'text-blue-700 hover:bg-blue-100 hover:text-blue-900 dark:text-blue-300 dark:hover:bg-blue-900/40' : 'text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-gray-700' }}"
                                            data-date="{{ $dayDate }}"
                                            data-date-label="{{ $day->format('d M Y') }}"
                                            title="View attendance locations for {{ $day->format('d M Y') }}">
                                            📍
                                        </button>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($participants as $participant)
                                <tr class="js-employee-row" data-employee-name="{{ strtolower($participant->display_name) }}">
                                    <td
                                        class="sticky left-0 z-10 border-r border-gray-200 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
                                        <div class="font-medium text-gray-900 dark:text-white/90">{{ $participant->display_name }}</div>
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400">
                                            {{ $participant->employee_type === 'user' ? 'User' : 'Sales Person' }}:
                                            {{ $participant->identifier }}
                                        </div>
                                    </td>
                                    @foreach ($days as $day)
                                        @php
                                            $dateStr = $day->toDateString();
                                            $withinRegister = $dateStr >= $attendanceRegister->start_date->toDateString() && $dateStr <= $attendanceRegister->end_date->toDateString();
                                            $override = $overrides->get($dateStr);
                                            $isHoliday = $withinRegister && $override && $override->is_holiday;
                                            $entry = $entries->get($participant->id . '|' . $dateStr);
                                            $status = $entry?->status;
                                            $inTime = $entry?->in_time ? substr((string) $entry->in_time, 0, 5) : null;
                                            $outTime = $entry?->out_time ? substr((string) $entry->out_time, 0, 5) : null;
                                            $inLat = $entry?->in_latitude;
                                            $inLng = $entry?->in_longitude;
                                            $outLat = $entry?->out_latitude;
                                            $outLng = $entry?->out_longitude;
                                        @endphp
                                        <td class="border-r border-gray-200 px-1 py-2 text-center align-middle dark:border-gray-700">
                                            @if (! $withinRegister)
                                                <span
                                                    class="inline-flex min-w-[54px] justify-center rounded bg-gray-100 px-2 py-1 text-gray-500">-</span>
                                            @elseif ($isHoliday)
                                                <span
                                                    class="inline-flex min-w-[54px] justify-center rounded bg-yellow-100 px-2 py-1 text-yellow-800">Holiday</span>
                                            @elseif ($dateStr === $today && Auth::guard('admin')->user()->hasPermission('edit_attendance'))
                                                <div class="space-y-1">
                                                    @if ($status === 'present' && $inTime)
                                                        <div class="text-[10px] text-green-700">
                                                            IN {{ $inTime }}
                                                            @if (! is_null($inLat) && ! is_null($inLng))
                                                                <a href="https://www.google.com/maps?q={{ $inLat }},{{ $inLng }}" target="_blank"
                                                                    rel="noopener noreferrer" class="ml-1 text-blue-600 hover:text-blue-800"
                                                                    title="View IN location ({{ $inLat }}, {{ $inLng }})">📍</a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if ($status === 'present' && $outTime)
                                                        <div class="text-[10px] text-blue-700">
                                                            OUT {{ $outTime }}
                                                            @if (! is_null($outLat) && ! is_null($outLng))
                                                                <a href="https://www.google.com/maps?q={{ $outLat }},{{ $outLng }}" target="_blank"
                                                                    rel="noopener noreferrer" class="ml-1 text-blue-600 hover:text-blue-800"
                                                                    title="View OUT location ({{ $outLat }}, {{ $outLng }})">📍</a>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <div class="flex items-center justify-center gap-1">
                                                        @if ($status === 'present' && $inTime)
                                                            <button type="button"
                                                                class="js-open-mark rounded px-2 py-1 text-[10px] {{ $outTime ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700' }}"
                                                                data-action="out" data-participant-id="{{ $participant->id }}"
                                                                data-date="{{ $dateStr }}" data-mark-time="{{ $outTime }}"
                                                                data-in-time="{{ $inTime }}">
                                                                {{ $outTime ? 'Edit OUT' : 'OUT' }}
                                                            </button>
                                                        @else
                                                            <button type="button"
                                                                class="js-open-mark rounded bg-green-100 px-2 py-1 text-[10px] text-green-700"
                                                                data-action="in" data-participant-id="{{ $participant->id }}"
                                                                data-date="{{ $dateStr }}" data-mark-time="{{ $inTime }}">
                                                                IN
                                                            </button>
                                                            <button type="button"
                                                                class="js-open-mark rounded px-2 py-1 text-[10px] {{ $status === 'absent' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700' }}"
                                                                data-action="absent" data-participant-id="{{ $participant->id }}"
                                                                data-date="{{ $dateStr }}">
                                                                Absent
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                @if (! $entry)
                                                    <span
                                                        class="inline-flex min-w-[64px] justify-center rounded bg-slate-100 px-2 py-1 text-slate-700">--</span>
                                                @elseif ($status === 'present')
                                                    <div
                                                        class="inline-flex min-w-[84px] flex-col items-center rounded bg-green-100 px-2 py-1 text-[10px] text-green-700">
                                                        @if ($inTime)
                                                            <div>
                                                                IN {{ $inTime }}
                                                                @if (! is_null($inLat) && ! is_null($inLng))
                                                                    <a href="https://www.google.com/maps?q={{ $inLat }},{{ $inLng }}" target="_blank"
                                                                        rel="noopener noreferrer" class="ml-1 text-blue-600 hover:text-blue-800"
                                                                        title="View IN location ({{ $inLat }}, {{ $inLng }})">📍</a>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        @if ($outTime)
                                                            <div class="text-blue-700">
                                                                OUT {{ $outTime }}
                                                                @if (! is_null($outLat) && ! is_null($outLng))
                                                                    <a href="https://www.google.com/maps?q={{ $outLat }},{{ $outLng }}" target="_blank"
                                                                        rel="noopener noreferrer" class="ml-1 text-blue-600 hover:text-blue-800"
                                                                        title="View OUT location ({{ $outLat }}, {{ $outLng }})">📍</a>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span
                                                        class="inline-flex min-w-[54px] justify-center rounded bg-red-100 px-2 py-1 text-red-700">Absent</span>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($days) + 1 }}" class="px-4 py-6 text-center text-gray-500">
                                        No participants in this register.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="markModal" class="fixed inset-0 z-[200] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" id="markModalBackdrop"></div>
        <div class="relative z-[201] w-full max-w-md rounded-xl bg-white p-5 shadow-xl dark:bg-gray-900">
            <h3 id="markModalTitle" class="mb-1 text-lg font-semibold text-gray-900 dark:text-white/90">Mark Attendance</h3>
            <p id="markModalSubTitle" class="mb-4 text-sm text-gray-500 dark:text-gray-400"></p>

            <form method="POST" action="{{ route('admin.attendance-registers.mark', $attendanceRegister) }}" id="markModalForm"
                class="space-y-3">
                @csrf
                <input type="hidden" name="participant_id" id="modalParticipantId">
                <input type="hidden" name="attendance_date" id="modalAttendanceDate">
                <input type="hidden" name="action" id="modalAction">

                <div id="modalMarkTimeWrap">
                    <label for="modalMarkTime" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Time</label>
                    <input type="time" name="mark_time" id="modalMarkTime"
                        class="w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                </div>

                <div id="modalInTimeWrap" class="hidden">
                    <label for="modalInTime" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Edit IN Time</label>
                    <input type="time" name="in_time" id="modalInTime"
                        class="w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                </div>

                <div id="modalLocationWrap" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Location (Lat/Lng)</label>
                        <button type="button" id="refreshLocationBtn"
                            class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                            Refresh
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="latitude" id="modalLatitude" placeholder="Latitude"
                            class="w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                        <input type="text" name="longitude" id="modalLongitude" placeholder="Longitude"
                            class="w-full rounded border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                    </div>
                    <p id="modalLocationMsg" class="text-xs text-gray-500 dark:text-gray-400"></p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" id="markModalCancel"
                        class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">Cancel</button>
                    <button type="submit" id="markModalSubmit"
                        class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="dayMapModal" class="fixed inset-0 z-[100000] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" id="dayMapBackdrop"></div>
        <div id="dayMapDialog" class="relative z-[100001] w-full max-w-5xl rounded-xl bg-white p-4 shadow-xl dark:bg-gray-900 sm:p-5">
            <div class="mb-3 flex items-start justify-between gap-3">
                <div>
                    <h3 id="dayMapTitle" class="text-lg font-semibold text-gray-900 dark:text-white/90">Attendance Location Map</h3>
                    <p id="dayMapSubTitle" class="text-sm text-gray-500 dark:text-gray-400"></p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="dayMapFullscreen"
                        class="rounded border border-gray-300 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                        Fullscreen
                    </button>
                    <button type="button" id="dayMapClose"
                        class="rounded border border-gray-300 px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                        Close
                    </button>
                </div>
            </div>
            <p id="dayMapEmptyState" class="hidden rounded border border-yellow-200 bg-yellow-50 px-3 py-2 text-sm text-yellow-800">
                No attendance locations found for this date.
            </p>
            <div id="dayAttendanceMap" class="mt-3 h-[420px] w-full rounded border border-gray-200 dark:border-gray-700"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'attendance-registers',
        };
    </script>
    <script>
        (function() {
            const modal = document.getElementById('markModal');
            const backdrop = document.getElementById('markModalBackdrop');
            const cancelBtn = document.getElementById('markModalCancel');
            const titleEl = document.getElementById('markModalTitle');
            const subtitleEl = document.getElementById('markModalSubTitle');
            const participantEl = document.getElementById('modalParticipantId');
            const dateEl = document.getElementById('modalAttendanceDate');
            const actionEl = document.getElementById('modalAction');
            const markTimeWrap = document.getElementById('modalMarkTimeWrap');
            const markTimeEl = document.getElementById('modalMarkTime');
            const inTimeWrap = document.getElementById('modalInTimeWrap');
            const inTimeEl = document.getElementById('modalInTime');
            const locationWrap = document.getElementById('modalLocationWrap');
            const locationMsg = document.getElementById('modalLocationMsg');
            const latEl = document.getElementById('modalLatitude');
            const lngEl = document.getElementById('modalLongitude');
            const submitBtn = document.getElementById('markModalSubmit');
            const refreshLocationBtn = document.getElementById('refreshLocationBtn');
            const tableScrollContainer = document.getElementById('attendanceTableScroll');
            const employeeSearchInput = document.getElementById('employeeSearchInput');
            const employeeSearchClear = document.getElementById('employeeSearchClear');
            const employeeSearchInfo = document.getElementById('employeeSearchInfo');
            const dayMapModal = document.getElementById('dayMapModal');
            const dayMapBackdrop = document.getElementById('dayMapBackdrop');
            const dayMapDialog = document.getElementById('dayMapDialog');
            const dayMapCloseBtn = document.getElementById('dayMapClose');
            const dayMapFullscreenBtn = document.getElementById('dayMapFullscreen');
            const dayMapTitle = document.getElementById('dayMapTitle');
            const dayMapSubTitle = document.getElementById('dayMapSubTitle');
            const dayMapEmptyState = document.getElementById('dayMapEmptyState');
            const dayMapCanvas = document.getElementById('dayAttendanceMap');
            const dayLocationData = @json($dayLocationData);

            let dayMap = null;
            let dayMapLayer = null;
            let dayMapIsFullscreen = false;

            const nowHHMM = () => {
                const now = new Date();
                return `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
            };

            const escapeHtml = (value) => {
                const str = String(value ?? '');
                return str
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            const scrollToCurrentDateColumn = () => {
                if (!tableScrollContainer) return;

                const todayHeader = document.getElementById('attendanceTodayHeader');
                if (!todayHeader) return;

                const stickyHeader = tableScrollContainer.querySelector('thead th.sticky');
                const stickyWidth = stickyHeader ? stickyHeader.getBoundingClientRect().width : 0;
                const targetLeft = Math.max(todayHeader.offsetLeft - stickyWidth - 16, 0);

                tableScrollContainer.scrollTo({
                    left: targetLeft,
                    behavior: 'auto'
                });
            };

            const attachNativePickers = () => {
                const pickerInputs = document.querySelectorAll('.js-native-picker');
                pickerInputs.forEach((input) => {
                    if (typeof input.showPicker !== 'function') return;

                    const openPicker = () => {
                        try {
                            input.showPicker();
                        } catch (e) {
                            // ignore browser restrictions
                        }
                    };

                    input.addEventListener('focus', openPicker);
                    input.addEventListener('click', openPicker);
                    input.addEventListener('keydown', (event) => {
                        if (event.key === 'Enter' || event.key === ' ' || event.key === 'ArrowDown') {
                            event.preventDefault();
                            openPicker();
                        }
                    });
                });
            };

            const openModal = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            const applyDayMapFullscreen = (isFullscreen) => {
                dayMapIsFullscreen = isFullscreen;

                if (isFullscreen) {
                    dayMapModal.style.padding = '0';
                    dayMapDialog.style.maxWidth = '100vw';
                    dayMapDialog.style.width = '100vw';
                    dayMapDialog.style.height = '100vh';
                    dayMapDialog.style.borderRadius = '0';
                    dayMapCanvas.style.height = 'calc(100vh - 140px)';
                    dayMapFullscreenBtn.textContent = 'Exit Fullscreen';
                } else {
                    dayMapModal.style.padding = '';
                    dayMapDialog.style.maxWidth = '';
                    dayMapDialog.style.width = '';
                    dayMapDialog.style.height = '';
                    dayMapDialog.style.borderRadius = '';
                    dayMapCanvas.style.height = '';
                    dayMapFullscreenBtn.textContent = 'Fullscreen';
                }

                if (dayMap) {
                    setTimeout(() => dayMap.invalidateSize(), 60);
                }
            };

            const openDayMapModal = () => {
                dayMapModal.classList.remove('hidden');
                dayMapModal.classList.add('flex');
            };

            const closeDayMapModal = () => {
                applyDayMapFullscreen(false);
                dayMapModal.classList.add('hidden');
                dayMapModal.classList.remove('flex');
            };

            const ensureDayMap = () => {
                if (dayMap) {
                    return true;
                }

                if (typeof L === 'undefined') {
                    dayMapEmptyState.textContent = 'Map library could not be loaded.';
                    dayMapEmptyState.classList.remove('hidden');
                    dayMapCanvas.classList.add('hidden');
                    return false;
                }

                dayMap = L.map('dayAttendanceMap', {
                    zoomControl: true,
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(dayMap);

                dayMapLayer = L.layerGroup().addTo(dayMap);

                return true;
            };

            const zoomDayMapToCurrentLocation = () => {
                if (!dayMap || !navigator.geolocation) {
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = Number(position.coords.latitude);
                        const lng = Number(position.coords.longitude);
                        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                            return;
                        }

                        dayMap.setView([lat, lng], 15, {
                            animate: true,
                        });
                    },
                    () => {
                        // keep attendance marker bounds when location is blocked/unavailable
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 8000,
                        maximumAge: 0,
                    }
                );
            };

            const renderDayMap = (date, dateLabel) => {
                const points = Array.isArray(dayLocationData[date]) ? dayLocationData[date] : [];
                dayMapTitle.textContent = `Attendance Location Map - ${dateLabel}`;
                dayMapSubTitle.textContent = `${points.length} point(s) marked`;

                openDayMapModal();

                if (!ensureDayMap()) {
                    return;
                }

                dayMapLayer.clearLayers();

                if (points.length === 0) {
                    dayMapEmptyState.textContent = 'No attendance locations found for this date.';
                    dayMapEmptyState.classList.remove('hidden');
                    dayMapCanvas.classList.add('hidden');
                    return;
                }

                dayMapEmptyState.classList.add('hidden');
                dayMapCanvas.classList.remove('hidden');

                const bounds = L.latLngBounds();
                let plottedPoints = 0;

                points.forEach((point) => {
                    const lat = Number(point.latitude);
                    const lng = Number(point.longitude);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                        return;
                    }

                    const markerColor = point.mark_type === 'IN' ? '#16a34a' : '#2563eb';
                    const marker = L.circleMarker([lat, lng], {
                        radius: 7,
                        color: markerColor,
                        fillColor: markerColor,
                        fillOpacity: 0.85,
                        weight: 2,
                    });

                    marker.bindPopup(
                        `<div class="text-xs"><div><strong>${escapeHtml(point.name)}</strong></div><div>${escapeHtml(point.user_type)} | ${escapeHtml(point.mark_type)}${point.time ? ` | ${escapeHtml(point.time)}` : ''}</div></div>`
                    );

                    marker.addTo(dayMapLayer);
                    bounds.extend([lat, lng]);
                    plottedPoints += 1;
                });

                if (plottedPoints === 0) {
                    dayMapEmptyState.textContent = 'Location data exists, but coordinates are invalid.';
                    dayMapEmptyState.classList.remove('hidden');
                    dayMapCanvas.classList.add('hidden');
                    return;
                }

                setTimeout(() => {
                    dayMap.invalidateSize();
                    if (bounds.isValid()) {
                        dayMap.fitBounds(bounds.pad(0.25));
                    } else {
                        dayMap.setView([20.5937, 78.9629], 4);
                    }
                    zoomDayMapToCurrentLocation();
                }, 50);
            };

            const fetchLocation = () => {
                if (!navigator.geolocation) {
                    locationMsg.textContent = 'Geolocation is not supported by this browser.';
                    return;
                }

                locationMsg.textContent = 'Capturing location...';
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        latEl.value = Number(position.coords.latitude).toFixed(7);
                        lngEl.value = Number(position.coords.longitude).toFixed(7);
                        locationMsg.textContent = 'Location captured.';
                    },
                    () => {
                        locationMsg.textContent = 'Unable to capture location. You can enter latitude/longitude manually.';
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            };

            document.querySelectorAll('.js-open-mark').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const action = btn.dataset.action;
                    const participantId = btn.dataset.participantId;
                    const date = btn.dataset.date;
                    const markTime = btn.dataset.markTime || nowHHMM();
                    const inTime = btn.dataset.inTime || '';

                    participantEl.value = participantId;
                    dateEl.value = date;
                    actionEl.value = action;
                    markTimeEl.value = markTime;
                    inTimeEl.value = inTime;
                    latEl.value = '';
                    lngEl.value = '';
                    locationMsg.textContent = '';

                    if (action === 'in') {
                        titleEl.textContent = 'Mark IN';
                        subtitleEl.textContent = 'Capture IN time and location.';
                        markTimeWrap.classList.remove('hidden');
                        inTimeWrap.classList.add('hidden');
                        locationWrap.classList.remove('hidden');
                        submitBtn.textContent = 'Save IN';
                        fetchLocation();
                    }

                    if (action === 'out') {
                        titleEl.textContent = 'Mark OUT';
                        subtitleEl.textContent = 'Capture OUT time and location. You can also adjust IN time.';
                        markTimeWrap.classList.remove('hidden');
                        inTimeWrap.classList.remove('hidden');
                        locationWrap.classList.remove('hidden');
                        submitBtn.textContent = 'Save OUT';
                        fetchLocation();
                    }

                    if (action === 'absent') {
                        titleEl.textContent = 'Mark Absent';
                        subtitleEl.textContent = 'Confirm absent marking for current date.';
                        markTimeWrap.classList.add('hidden');
                        inTimeWrap.classList.add('hidden');
                        locationWrap.classList.add('hidden');
                        submitBtn.textContent = 'Mark Absent';
                    }

                    openModal();
                });
            });

            document.querySelectorAll('.js-open-day-map').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const date = btn.dataset.date;
                    const dateLabel = btn.dataset.dateLabel || date;
                    renderDayMap(date, dateLabel);
                });
            });

            backdrop.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            refreshLocationBtn.addEventListener('click', fetchLocation);
            dayMapBackdrop.addEventListener('click', closeDayMapModal);
            dayMapCloseBtn.addEventListener('click', closeDayMapModal);
            dayMapFullscreenBtn.addEventListener('click', () => {
                applyDayMapFullscreen(!dayMapIsFullscreen);
            });
            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') {
                    return;
                }

                if (modal.classList.contains('flex')) {
                    closeModal();
                }

                if (dayMapModal.classList.contains('flex')) {
                    closeDayMapModal();
                }
            });

            requestAnimationFrame(() => {
                scrollToCurrentDateColumn();
            });
            attachNativePickers();

            const filterEmployeeRows = () => {
                if (!employeeSearchInput) return;

                const query = employeeSearchInput.value.trim().toLowerCase();
                const rows = document.querySelectorAll('.js-employee-row');
                let visible = 0;

                rows.forEach((row) => {
                    const name = (row.dataset.employeeName || '').toLowerCase();
                    const matches = query === '' || name.includes(query);
                    row.style.display = matches ? '' : 'none';
                    if (matches) visible += 1;
                });

                if (employeeSearchInfo) {
                    employeeSearchInfo.textContent = query ? `${visible} result(s) found` : '';
                }

                if (employeeSearchClear) {
                    employeeSearchClear.classList.toggle('hidden', query === '');
                }
            };

            if (employeeSearchInput) {
                employeeSearchInput.addEventListener('input', filterEmployeeRows);
            }

            if (employeeSearchClear && employeeSearchInput) {
                employeeSearchClear.addEventListener('click', () => {
                    employeeSearchInput.value = '';
                    filterEmployeeRows();
                    employeeSearchInput.focus();
                });
            }
        })();
    </script>
@endpush
