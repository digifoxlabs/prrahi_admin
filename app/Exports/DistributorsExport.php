<?php

namespace App\Exports;

use App\Models\Distributor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DistributorsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Distributor::when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('firstname', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Sl',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Address',
            'District',
            'City/Town',
            'State',
            'Pincode',
            'GST',
            'Created At',
        ];
    }

    public function map($distributor): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $distributor->firstname,
            $distributor->lastname,
            $distributor->email,
            $distributor->phone,
            $distributor->address,
            $distributor->district,
            $distributor->city_town,
            $distributor->state,
            $distributor->pincode,
            $distributor->gst,
            $distributor->created_at->format('Y-m-d H:i:s'),
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