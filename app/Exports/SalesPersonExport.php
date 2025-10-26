<?php

namespace App\Exports;

use App\Models\SalesPerson;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesPersonExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{

     protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }


public function collection()
    {
        return SalesPerson::when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Sl',
            'Name',
            'Designation',
            'Headquarter',
            'AddressLine_1',
            'AddressLine_2',
            'Town',
            'District',
            'State',
            'Pincode',
            'Phone',
            'Office Email',
            'Personal Email',
            'DOB',
            'Anniversary Date',
            'Zone',
            'State Covered',
            'District Covered',
            'Town Covered',
            'Login ID',
            'Created At',
        ];
    }

    public function map($salesPerson): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $salesPerson->name,
            $salesPerson->designation,
            $salesPerson->headquarter,
            $salesPerson->address_line_1,
            $salesPerson->address_line_2,
            $salesPerson->town,
            $salesPerson->district,
            $salesPerson->state,
            $salesPerson->pincode,
            $salesPerson->phone,
            $salesPerson->official_email,
            $salesPerson->personal_email,
            $salesPerson->date_of_birth,
            $salesPerson->date_of_anniversary,
            $salesPerson->zone,
            $salesPerson->state_covered,
            $salesPerson->district_covered,
            $salesPerson->town_covered,
            $salesPerson->login_id,
            $salesPerson->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet
                    ->getStyle('A1:L1')
                    ->getFont()
                    ->setBold(true);
            },
        ];
    }








}
