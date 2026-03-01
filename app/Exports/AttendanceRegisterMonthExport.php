<?php

namespace App\Exports;

use App\Models\AttendanceRegister;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AttendanceRegisterMonthExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected AttendanceRegister $register;
    protected Carbon $viewMonth;
    protected array $days;
    protected Collection $participants;
    protected Collection $entries;
    protected Collection $overrides;

    public function __construct(
        AttendanceRegister $register,
        Carbon $viewMonth,
        array $days,
        Collection $participants,
        Collection $entries,
        Collection $overrides
    ) {
        $this->register = $register;
        $this->viewMonth = $viewMonth;
        $this->days = $days;
        $this->participants = $participants;
        $this->entries = $entries;
        $this->overrides = $overrides;
    }

    public function headings(): array
    {
        $headings = ['Name'];

        foreach ($this->days as $day) {
            $headings[] = $day->format('d-M-Y');
        }

        $headings[] = 'Total Present';
        $headings[] = 'Total Absent';

        return $headings;
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->participants as $participant) {
            $row = [$participant->display_name];
            $presentCount = 0;
            $absentCount = 0;

            foreach ($this->days as $day) {
                $dateStr = $day->toDateString();
                $withinRegister = $dateStr >= $this->register->start_date->toDateString()
                    && $dateStr <= $this->register->end_date->toDateString();

                if (! $withinRegister) {
                    $row[] = '-';
                    continue;
                }

                $override = $this->overrides->get($dateStr);
                $isHoliday = $override && $override->is_holiday;
                if ($isHoliday) {
                    $row[] = 'Holiday';
                    continue;
                }

                $entry = $this->entries->get($participant->id . '|' . $dateStr);
                if ($entry && $entry->status === 'present') {
                    $in = $entry->in_time ? substr((string) $entry->in_time, 0, 5) : '';
                    $out = $entry->out_time ? substr((string) $entry->out_time, 0, 5) : '';
                    $row[] = $out !== '' ? "IN {$in} | OUT {$out}" : "IN {$in}";
                    $presentCount++;
                } else {
                    $row[] = 'Absent';
                    $absentCount++;
                }
            }

            $row[] = $presentCount;
            $row[] = $absentCount;
            $rows[] = $row;
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $highestColumn = $event->sheet->getHighestColumn();
                $event->sheet->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);
            },
        ];
    }
}

