<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceRegisterMonthExport;
use App\Http\Controllers\Controller;
use App\Models\AttendanceEntry;
use App\Models\AttendanceRegister;
use App\Models\AttendanceRegisterDateOverride;
use App\Models\AttendanceRegisterParticipant;
use App\Models\User;
use App\Models\SalesPerson;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_attendance')->only(['index', 'show', 'month']);
        $this->middleware('permission:view_attendance')->only(['exportMonth']);
        $this->middleware('permission:create_attendance')->only(['create', 'store']);
        $this->middleware('permission:edit_attendance')->only(['edit', 'update', 'mark', 'saveOverride']);
        $this->middleware('permission:delete_attendance')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $title = 'Attendance Registers';
        $search = trim((string) $request->query('search', ''));

        $registers = AttendanceRegister::query()
            ->withCount('participants')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderByDesc('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.attendance-registers.index', compact('registers', 'search', 'title'));
    }

    public function create()
    {
        $title = 'Attendance Registers';
        $weekdayNames = $this->weekdayNames();

        return view('admin.attendance-registers.create', compact('title', 'weekdayNames'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRegisterData($request);

        DB::transaction(function () use ($validated) {
            if (! empty($validated['is_active'])) {
                AttendanceRegister::query()->update(['is_active' => false]);
            }

            $register = AttendanceRegister::create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ]);

            $this->saveWeekdayRules($register, $validated['weekday_rules'] ?? []);
            $this->createParticipantsSnapshot($register);
        });

        return redirect()
            ->route('admin.attendance-registers.index')
            ->with('success', 'Attendance register created successfully.');
    }

    public function show(AttendanceRegister $attendanceRegister)
    {
        $defaultMonth = $this->resolveDefaultViewMonth($attendanceRegister);

        return redirect()->route('admin.attendance-registers.month', [
            'attendance_register' => $attendanceRegister->id,
            'year' => $defaultMonth->year,
            'month' => $defaultMonth->month,
        ]);
    }

    public function edit(AttendanceRegister $attendanceRegister)
    {
        $title = 'Attendance Registers';
        $weekdayNames = $this->weekdayNames();
        $weekdayRules = $attendanceRegister->weekdayRules()->get()->keyBy('weekday');

        return view('admin.attendance-registers.edit', compact('attendanceRegister', 'title', 'weekdayNames', 'weekdayRules'));
    }

    public function update(Request $request, AttendanceRegister $attendanceRegister)
    {
        $validated = $this->validateRegisterData($request, $attendanceRegister->id);

        DB::transaction(function () use ($attendanceRegister, $validated) {
            if (! empty($validated['is_active'])) {
                AttendanceRegister::where('id', '!=', $attendanceRegister->id)->update(['is_active' => false]);
            }

            $attendanceRegister->update([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ]);

            $this->saveWeekdayRules($attendanceRegister, $validated['weekday_rules'] ?? []);

            if (! $attendanceRegister->participants()->exists()) {
                $this->createParticipantsSnapshot($attendanceRegister);
            }
        });

        return redirect()
            ->route('admin.attendance-registers.index')
            ->with('success', 'Attendance register updated successfully.');
    }

    public function destroy(AttendanceRegister $attendanceRegister)
    {
        $attendanceRegister->delete();

        return redirect()->route('admin.attendance-registers.index')->with('success', 'Attendance register deleted successfully.');
    }

    public function month(AttendanceRegister $attendanceRegister, int $year, int $month)
    {
        if ($month < 1 || $month > 12) {
            abort(404);
        }

        $viewMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $registerStartMonth = $attendanceRegister->start_date->copy()->startOfMonth();
        $registerEndMonth = $attendanceRegister->end_date->copy()->startOfMonth();

        if ($viewMonth->copy()->startOfMonth()->lt($registerStartMonth) || $viewMonth->copy()->startOfMonth()->gt($registerEndMonth)) {
            abort(404);
        }

        $monthStart = $viewMonth->copy()->startOfMonth();
        $monthEnd = $viewMonth->copy()->endOfMonth();

        $days = [];
        $cursor = $monthStart->copy();
        while ($cursor->lte($monthEnd)) {
            $days[] = $cursor->copy();
            $cursor->addDay();
        }

        $participants = $attendanceRegister->participants()
            ->orderBy('sort_name')
            ->orderBy('display_name')
            ->get();

        $entries = AttendanceEntry::query()
            ->where('attendance_register_id', $attendanceRegister->id)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(function ($entry) {
                return $entry->participant_id . '|' . Carbon::parse($entry->attendance_date)->toDateString();
            });

        $overrides = $attendanceRegister->dateOverrides()
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(function ($override) {
                return Carbon::parse($override->attendance_date)->toDateString();
            });

        $months = [];
        $monthCursor = $attendanceRegister->start_date->copy()->startOfMonth();
        while ($monthCursor->lte($attendanceRegister->end_date->copy()->startOfMonth())) {
            $months[] = $monthCursor->copy();
            $monthCursor->addMonth();
        }

        $today = Carbon::today()->toDateString();
        $title = 'Attendance Registers';

        return view('admin.attendance-registers.month', compact(
            'attendanceRegister',
            'title',
            'participants',
            'days',
            'entries',
            'overrides',
            'months',
            'today',
            'viewMonth'
        ));
    }

    public function exportMonth(AttendanceRegister $attendanceRegister, int $year, int $month)
    {
        if ($month < 1 || $month > 12) {
            abort(404);
        }

        $viewMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $registerStartMonth = $attendanceRegister->start_date->copy()->startOfMonth();
        $registerEndMonth = $attendanceRegister->end_date->copy()->startOfMonth();

        if ($viewMonth->copy()->startOfMonth()->lt($registerStartMonth) || $viewMonth->copy()->startOfMonth()->gt($registerEndMonth)) {
            abort(404);
        }

        $monthStart = $viewMonth->copy()->startOfMonth();
        $monthEnd = $viewMonth->copy()->endOfMonth();

        $days = [];
        $cursor = $monthStart->copy();
        while ($cursor->lte($monthEnd)) {
            $days[] = $cursor->copy();
            $cursor->addDay();
        }

        $participants = $attendanceRegister->participants()
            ->orderBy('sort_name')
            ->orderBy('display_name')
            ->get();

        $entries = AttendanceEntry::query()
            ->where('attendance_register_id', $attendanceRegister->id)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(function ($entry) {
                return $entry->participant_id . '|' . Carbon::parse($entry->attendance_date)->toDateString();
            });

        $overrides = $attendanceRegister->dateOverrides()
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(function ($override) {
                return Carbon::parse($override->attendance_date)->toDateString();
            });

        $filename = 'attendance_' . $attendanceRegister->id . '_' . $viewMonth->format('Y_m') . '.xlsx';

        return Excel::download(
            new AttendanceRegisterMonthExport($attendanceRegister, $viewMonth, $days, $participants, $entries, $overrides),
            $filename
        );
    }

    public function mark(Request $request, AttendanceRegister $attendanceRegister)
    {
        $validated = $request->validate([
            'participant_id' => ['required', 'integer'],
            'attendance_date' => ['required', 'date'],
            'action' => ['required', 'in:in,out,absent'],
            'mark_time' => ['nullable', 'date_format:H:i'],
            'in_time' => ['nullable', 'date_format:H:i'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $date = Carbon::parse($validated['attendance_date'])->toDateString();
        $today = Carbon::today()->toDateString();

        if ($date !== $today) {
            return redirect()->back()->with('error', 'Only current date attendance can be marked.');
        }

        if ($date < $attendanceRegister->start_date->toDateString() || $date > $attendanceRegister->end_date->toDateString()) {
            return redirect()->back()->with('error', 'Selected date is outside register range.');
        }

        $participant = $attendanceRegister->participants()->where('id', $validated['participant_id'])->first();
        if (! $participant) {
            return redirect()->back()->with('error', 'Invalid participant selected.');
        }

        $override = $attendanceRegister->dateOverrides()->whereDate('attendance_date', $date)->first();
        if ($override && $override->is_holiday) {
            return redirect()->back()->with('error', 'Attendance cannot be marked on holidays.');
        }

        $entry = AttendanceEntry::firstOrNew([
            'attendance_register_id' => $attendanceRegister->id,
            'participant_id' => $participant->id,
            'attendance_date' => $date,
        ]);

        $action = $validated['action'];
        $latitude = $validated['latitude'] ?? null;
        $longitude = $validated['longitude'] ?? null;

        if ($action === 'absent') {
            $entry->status = 'absent';
            $entry->in_time = null;
            $entry->out_time = null;
            $entry->in_latitude = null;
            $entry->in_longitude = null;
            $entry->out_latitude = null;
            $entry->out_longitude = null;
        }

        if ($action === 'in') {
            $entry->status = 'present';
            $entry->in_time = $this->resolveActionTime($attendanceRegister, $date, 'in', $validated['mark_time'] ?? null);
            $entry->in_latitude = $latitude;
            $entry->in_longitude = $longitude;
        }

        if ($action === 'out') {
            if (! $entry->in_time) {
                return redirect()->back()->with('error', 'Mark IN first before OUT.');
            }

            if (! empty($validated['in_time'])) {
                $entry->in_time = $validated['in_time'];
            }

            $resolvedOutTime = $this->resolveActionTime($attendanceRegister, $date, 'out', $validated['mark_time'] ?? null);
            if ($entry->in_time && $resolvedOutTime <= $entry->in_time) {
                return redirect()->back()->with('error', 'OUT time must be after IN time.');
            }

            $entry->status = 'present';
            $entry->out_time = $resolvedOutTime;
            $entry->out_latitude = $latitude;
            $entry->out_longitude = $longitude;
        }

        $entry->marked_by = optional(auth('admin')->user())->id;
        $entry->source = 'admin';
        $entry->save();

        return redirect()->back()->with('success', 'Attendance updated.');
    }

    public function saveOverride(Request $request, AttendanceRegister $attendanceRegister)
    {
        $validated = $request->validate([
            'attendance_date' => ['required', 'date'],
            'is_holiday' => ['nullable', 'boolean'],
            'holiday_name' => ['nullable', 'string', 'max:255'],
            'in_time' => ['nullable', 'date_format:H:i'],
            'out_time' => ['nullable', 'date_format:H:i'],
            'action' => ['nullable', 'in:save,clear'],
        ]);

        $date = Carbon::parse($validated['attendance_date'])->toDateString();
        if ($date < $attendanceRegister->start_date->toDateString() || $date > $attendanceRegister->end_date->toDateString()) {
            return redirect()->back()->with('error', 'Override date must be within register range.');
        }

        $action = $validated['action'] ?? 'save';
        if ($action === 'clear') {
            $attendanceRegister->dateOverrides()->whereDate('attendance_date', $date)->delete();
            return redirect()->back()->with('success', 'Date override removed.');
        }

        $isHoliday = (bool) ($validated['is_holiday'] ?? false);

        AttendanceRegisterDateOverride::updateOrCreate(
            [
                'attendance_register_id' => $attendanceRegister->id,
                'attendance_date' => $date,
            ],
            [
                'is_holiday' => $isHoliday,
                'holiday_name' => $isHoliday ? ($validated['holiday_name'] ?? null) : null,
                'in_time' => $validated['in_time'] ?? null,
                'out_time' => $validated['out_time'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Date override saved.');
    }

    protected function validateRegisterData(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
            'weekday_rules' => ['nullable', 'array'],
            'weekday_rules.*.in_time' => ['nullable', 'date_format:H:i'],
            'weekday_rules.*.out_time' => ['nullable', 'date_format:H:i'],
        ]);

        $start = Carbon::parse($validated['start_date'])->toDateString();
        $end = Carbon::parse($validated['end_date'])->toDateString();

        $overlapExists = AttendanceRegister::query()
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start)
            ->exists();

        if ($overlapExists) {
            throw ValidationException::withMessages([
                'start_date' => 'Register dates overlap with an existing attendance register.',
            ]);
        }

        $rules = $validated['weekday_rules'] ?? [];
        foreach ($rules as $day => $rule) {
            if (! is_numeric($day) || (int) $day < 0 || (int) $day > 6) {
                continue;
            }

            $in = $rule['in_time'] ?? null;
            $out = $rule['out_time'] ?? null;

            if ($in && $out && $out <= $in) {
                throw ValidationException::withMessages([
                    "weekday_rules.$day.out_time" => 'Out time must be after in time.',
                ]);
            }
        }

        return $validated;
    }

    protected function saveWeekdayRules(AttendanceRegister $register, array $weekdayRules): void
    {
        for ($day = 0; $day <= 6; $day++) {
            $inTime = $weekdayRules[$day]['in_time'] ?? null;
            $outTime = $weekdayRules[$day]['out_time'] ?? null;

            $register->weekdayRules()->updateOrCreate(
                ['weekday' => $day],
                [
                    'default_in_time' => $inTime ?: null,
                    'default_out_time' => $outTime ?: null,
                ]
            );
        }
    }

    protected function createParticipantsSnapshot(AttendanceRegister $register): void
    {
        $now = now();
        $rows = [];

        $users = User::query()->select(['id', 'fname', 'lname', 'email'])->get();
        foreach ($users as $user) {
            $displayName = trim(($user->fname ?? '') . ' ' . ($user->lname ?? ''));
            if ($displayName === '') {
                $displayName = (string) $user->email;
            }

            $rows[] = [
                'attendance_register_id' => $register->id,
                'employee_type' => 'user',
                'employee_id' => $user->id,
                'identifier' => (string) $user->email,
                'display_name' => $displayName,
                'sort_name' => strtolower($displayName),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $salesPeople = SalesPerson::query()->select(['id', 'name', 'login_id'])->get();
        foreach ($salesPeople as $salesPerson) {
            $displayName = trim((string) $salesPerson->name);
            if ($displayName === '') {
                $displayName = (string) $salesPerson->login_id;
            }

            $rows[] = [
                'attendance_register_id' => $register->id,
                'employee_type' => 'sales_person',
                'employee_id' => $salesPerson->id,
                'identifier' => (string) $salesPerson->login_id,
                'display_name' => $displayName,
                'sort_name' => strtolower($displayName),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (empty($rows)) {
            return;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            AttendanceRegisterParticipant::insert($chunk);
        }
    }

    protected function weekdayNames(): array
    {
        return [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
    }

    protected function resolveActionTime(AttendanceRegister $register, string $date, string $action, ?string $requestedTime): string
    {
        if (! empty($requestedTime)) {
            return $requestedTime;
        }

        $override = $register->dateOverrides()->whereDate('attendance_date', $date)->first();
        if ($override) {
            if ($action === 'in' && $override->in_time) {
                return substr((string) $override->in_time, 0, 5);
            }
            if ($action === 'out' && $override->out_time) {
                return substr((string) $override->out_time, 0, 5);
            }
        }

        $weekday = Carbon::parse($date)->dayOfWeek;
        $rule = $register->weekdayRules()->where('weekday', $weekday)->first();
        if ($rule) {
            if ($action === 'in' && $rule->default_in_time) {
                return substr((string) $rule->default_in_time, 0, 5);
            }
            if ($action === 'out' && $rule->default_out_time) {
                return substr((string) $rule->default_out_time, 0, 5);
            }
        }

        return now()->format('H:i');
    }

    protected function resolveDefaultViewMonth(AttendanceRegister $register): Carbon
    {
        $todayMonth = Carbon::today()->startOfMonth();
        $startMonth = $register->start_date->copy()->startOfMonth();
        $endMonth = $register->end_date->copy()->startOfMonth();

        if ($todayMonth->lt($startMonth)) {
            return $startMonth;
        }

        if ($todayMonth->gt($endMonth)) {
            return $endMonth;
        }

        return $todayMonth;
    }
}
