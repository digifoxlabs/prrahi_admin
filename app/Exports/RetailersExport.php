<?php

namespace App\Exports;

use App\Models\Retailer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RetailersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    public function __construct(
        protected ?string $search = null,
        protected string $view = 'active'
    ) {
    }

    public function collection()
    {
        $query = Retailer::with(['distributor', 'appointedBy']);

        if ($this->view === 'trashed') {
            $query->onlyTrashed();
        } elseif ($this->view === 'all') {
            $query->withTrashed();
        }

        return $query
            ->when($this->search, function ($builder) {
                $builder->where(function ($subQuery) {
                    $subQuery->where('retailer_name', 'like', "%{$this->search}%")
                        ->orWhere('contact_person', 'like', "%{$this->search}%")
                        ->orWhere('contact_number', 'like', "%{$this->search}%")
                        ->orWhere('town', 'like', "%{$this->search}%");
                });
            })
            ->latest('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Sl',
            'Retailer',
            'Contact Person',
            'Phone',
            'District',
            'Town',
            'Distributor',
            'Appointed By Type',
            'Appointed By Name',
            'Appointment Date',
            'Deleted At',
            'Created At',
        ];
    }

    public function map($retailer): array
    {
        static $index = 0;
        $index++;

        [$appointedLabel, $appointedName] = $this->appointedByDetails($retailer);

        return [
            $index,
            $retailer->retailer_name,
            $retailer->contact_person,
            $retailer->contact_number,
            $retailer->district ?? '-',
            $retailer->town ?? '-',
            optional($retailer->distributor)->firm_name ?? '-',
            $appointedLabel,
            $appointedName,
            $retailer->appointment_date ?? '-',
            optional($retailer->deleted_at)?->format('Y-m-d H:i:s') ?? '-',
            optional($retailer->created_at)?->format('Y-m-d H:i:s') ?? '-',
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

    private function appointedByDetails(Retailer $retailer): array
    {
        $appointedBy = $retailer->appointedBy;

        if (! $appointedBy) {
            return ['-', '-'];
        }

        return match (class_basename($appointedBy)) {
            'User' => ['Admin', $appointedBy->fname ?? '-'],
            'Distributor' => ['Distributor', $appointedBy->firm_name ?? '-'],
            'SalesPerson' => ['Sales Person', $appointedBy->name ?? '-'],
            default => ['-', '-'],
        };
    }
}
