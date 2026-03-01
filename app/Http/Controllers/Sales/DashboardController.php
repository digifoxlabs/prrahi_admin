<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceEntry;
use App\Models\AttendanceRegister;
use App\Models\AttendanceRegisterParticipant;
use App\Models\SalesPerson;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $title = 'Dashboard';
        $salesPerson = auth('sales')->user();
        $today = Carbon::today()->toDateString();

        $attendanceRegister = $this->findTodayAttendanceRegister();
        $todayAttendanceEntry = null;
        $nextAttendanceAction = null;

        if ($attendanceRegister && $salesPerson) {
            $attendanceParticipant = $this->findSalesParticipant($attendanceRegister, $salesPerson);

            if ($attendanceParticipant) {
                $todayAttendanceEntry = AttendanceEntry::query()
                    ->where('attendance_register_id', $attendanceRegister->id)
                    ->where('participant_id', $attendanceParticipant->id)
                    ->whereDate('attendance_date', $today)
                    ->first();
            }

            if (! $todayAttendanceEntry || ! $todayAttendanceEntry->in_time) {
                $nextAttendanceAction = 'in';
            } elseif (! $todayAttendanceEntry->out_time) {
                $nextAttendanceAction = 'out';
            } else {
                $nextAttendanceAction = 'done';
            }
        }

        return view('sales.pages.dashboard', compact(
            'title',
            'attendanceRegister',
            'todayAttendanceEntry',
            'nextAttendanceAction'
        ));
    }

    public function markMyAttendance(Request $request)
    {
        $validated = $request->validate([
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        /** @var SalesPerson|null $salesPerson */
        $salesPerson = auth('sales')->user();
        if (! $salesPerson) {
            return redirect()->route('sales.login')->with('error', 'Please login to continue.');
        }

        $today = Carbon::today()->toDateString();
        $register = $this->findTodayAttendanceRegister();

        if (! $register) {
            return redirect()->back()->with('error', 'No attendance register found for today.');
        }

        $holidayOverride = $register->dateOverrides()->whereDate('attendance_date', $today)->first();
        if ($holidayOverride && $holidayOverride->is_holiday) {
            return redirect()->back()->with('error', 'Today is marked as holiday. Attendance cannot be marked.');
        }

        $participant = $this->ensureSalesParticipant($register, $salesPerson);
        $entry = AttendanceEntry::firstOrNew([
            'attendance_register_id' => $register->id,
            'participant_id' => $participant->id,
            'attendance_date' => $today,
        ]);

        $now = now();
        $currentTime = $now->format('H:i:s');
        $latitude = $validated['latitude'] ?? null;
        $longitude = $validated['longitude'] ?? null;

        if (! $entry->in_time) {
            $entry->status = 'present';
            $entry->in_time = $currentTime;
            $entry->in_latitude = $latitude;
            $entry->in_longitude = $longitude;
            $entry->marked_by = null;
            $entry->source = 'sales';
            $entry->save();

            return redirect()->back()->with('success', 'Attendance IN marked at ' . $now->format('h:i A') . '.');
        }

        if (! $entry->out_time) {
            $outTime = $now;
            $inDateTime = Carbon::parse($today . ' ' . $entry->in_time);
            if ($outTime->lte($inDateTime)) {
                $outTime = $inDateTime->copy()->addMinute();
            }

            $entry->status = 'present';
            $entry->out_time = $outTime->format('H:i:s');
            $entry->out_latitude = $latitude;
            $entry->out_longitude = $longitude;
            $entry->marked_by = null;
            $entry->source = 'sales';
            $entry->save();

            return redirect()->back()->with('success', 'Attendance OUT marked at ' . $outTime->format('h:i A') . '.');
        }

        return redirect()->back()->with('error', 'Today attendance is already fully marked (IN and OUT).');
    }

    protected function findTodayAttendanceRegister(): ?AttendanceRegister
    {
        $today = Carbon::today()->toDateString();

        return AttendanceRegister::query()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->first();
    }

    protected function ensureSalesParticipant(AttendanceRegister $register, SalesPerson $salesPerson): AttendanceRegisterParticipant
    {
        $displayName = trim((string) ($salesPerson->name ?? ''));
        if ($displayName === '') {
            $displayName = (string) ($salesPerson->login_id ?? ('Sales Person #' . $salesPerson->id));
        }

        return $register->participants()->firstOrCreate(
            [
                'employee_type' => 'sales_person',
                'employee_id' => $salesPerson->id,
            ],
            [
                'identifier' => (string) ($salesPerson->login_id ?? ('sales-' . $salesPerson->id)),
                'display_name' => $displayName,
                'sort_name' => strtolower($displayName),
            ]
        );
    }

    protected function findSalesParticipant(AttendanceRegister $register, SalesPerson $salesPerson): ?AttendanceRegisterParticipant
    {
        return $register->participants()
            ->where('employee_type', 'sales_person')
            ->where('employee_id', $salesPerson->id)
            ->first();
    }
}
