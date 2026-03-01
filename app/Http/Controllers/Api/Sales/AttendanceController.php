<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEntry;
use App\Models\AttendanceRegister;
use App\Models\AttendanceRegisterParticipant;
use App\Models\SalesPerson;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function today(Request $request): JsonResponse
    {
        /** @var SalesPerson|null $salesPerson */
        $salesPerson = auth('sales_api')->user();

        if (! $salesPerson) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $today = Carbon::today()->toDateString();
        $register = $this->findTodayAttendanceRegister();

        if (! $register) {
            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $today,
                    'register' => null,
                    'is_holiday' => false,
                    'holiday_name' => null,
                    'can_mark' => false,
                    'next_action' => null,
                    'in_time' => null,
                    'out_time' => null,
                    'in_latitude' => null,
                    'in_longitude' => null,
                    'out_latitude' => null,
                    'out_longitude' => null,
                ],
            ]);
        }

        $holidayOverride = $register->dateOverrides()->whereDate('attendance_date', $today)->first();
        $isHoliday = (bool) ($holidayOverride?->is_holiday);

        $participant = $this->findSalesParticipant($register, $salesPerson);
        $entry = null;

        if ($participant) {
            $entry = AttendanceEntry::query()
                ->where('attendance_register_id', $register->id)
                ->where('participant_id', $participant->id)
                ->whereDate('attendance_date', $today)
                ->first();
        }

        $nextAction = null;
        if (! $entry || ! $entry->in_time) {
            $nextAction = 'in';
        } elseif (! $entry->out_time) {
            $nextAction = 'out';
        } else {
            $nextAction = 'done';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $today,
                'register' => [
                    'id' => $register->id,
                    'name' => $register->name,
                    'start_date' => $register->start_date->toDateString(),
                    'end_date' => $register->end_date->toDateString(),
                ],
                'is_holiday' => $isHoliday,
                'holiday_name' => $holidayOverride?->holiday_name,
                'can_mark' => ! $isHoliday && $nextAction !== 'done',
                'next_action' => $nextAction,
                'in_time' => $entry?->in_time ? substr((string) $entry->in_time, 0, 5) : null,
                'out_time' => $entry?->out_time ? substr((string) $entry->out_time, 0, 5) : null,
                'in_latitude' => $entry?->in_latitude,
                'in_longitude' => $entry?->in_longitude,
                'out_latitude' => $entry?->out_latitude,
                'out_longitude' => $entry?->out_longitude,
            ],
        ]);
    }

    public function mark(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:in,out'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        /** @var SalesPerson|null $salesPerson */
        $salesPerson = auth('sales_api')->user();

        if (! $salesPerson) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $today = Carbon::today()->toDateString();
        $register = $this->findTodayAttendanceRegister();

        if (! $register) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance register found for today.',
            ], 422);
        }

        $holidayOverride = $register->dateOverrides()->whereDate('attendance_date', $today)->first();
        if ($holidayOverride && $holidayOverride->is_holiday) {
            return response()->json([
                'success' => false,
                'message' => 'Today is marked as holiday. Attendance cannot be marked.',
            ], 422);
        }

        $participant = $this->ensureSalesParticipant($register, $salesPerson);
        $entry = AttendanceEntry::firstOrNew([
            'attendance_register_id' => $register->id,
            'participant_id' => $participant->id,
            'attendance_date' => $today,
        ]);

        $action = $validated['action'];
        $latitude = $validated['latitude'];
        $longitude = $validated['longitude'];
        $now = now();

        if ($action === 'in') {
            if ($entry->in_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'IN is already marked for today.',
                ], 422);
            }

            $entry->status = 'present';
            $entry->in_time = $now->format('H:i:s');
            $entry->in_latitude = $latitude;
            $entry->in_longitude = $longitude;
            $entry->marked_by = null;
            $entry->source = 'sales_api';
            $entry->save();

            return response()->json([
                'success' => true,
                'message' => 'Attendance IN marked successfully.',
                'data' => [
                    'date' => $today,
                    'action' => 'in',
                    'in_time' => substr((string) $entry->in_time, 0, 5),
                    'out_time' => $entry->out_time ? substr((string) $entry->out_time, 0, 5) : null,
                ],
            ]);
        }

        if (! $entry->in_time) {
            return response()->json([
                'success' => false,
                'message' => 'Mark IN first before OUT.',
            ], 422);
        }

        if ($entry->out_time) {
            return response()->json([
                'success' => false,
                'message' => 'OUT is already marked for today.',
            ], 422);
        }

        $inDateTime = Carbon::parse($today . ' ' . $entry->in_time);
        $outDateTime = $now->copy();
        if ($outDateTime->lte($inDateTime)) {
            $outDateTime = $inDateTime->copy()->addMinute();
        }

        $entry->status = 'present';
        $entry->out_time = $outDateTime->format('H:i:s');
        $entry->out_latitude = $latitude;
        $entry->out_longitude = $longitude;
        $entry->marked_by = null;
        $entry->source = 'sales_api';
        $entry->save();

        return response()->json([
            'success' => true,
            'message' => 'Attendance OUT marked successfully.',
            'data' => [
                'date' => $today,
                'action' => 'out',
                'in_time' => $entry->in_time ? substr((string) $entry->in_time, 0, 5) : null,
                'out_time' => substr((string) $entry->out_time, 0, 5),
            ],
        ]);
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
