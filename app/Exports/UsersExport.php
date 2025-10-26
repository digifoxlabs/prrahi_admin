<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return User::with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('fname', 'like', "%{$this->search}%")
                        ->orWhere('lname', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('mobile_number', 'like', "%{$this->search}%");
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
            'Mobile Number',
            'Address',
            'District',
            'City/Town',
            'State',
            'Country',
            'Pincode',
            'Created At',
            'Roles',
        ];
    }

    public function map($user): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $user->fname,
            $user->lname,
            $user->email,
            $user->mobile_number,
            $user->address,
            $user->district,
            $user->city_town,
            $user->state,
            $user->country,
            $user->pincode,
            $user->created_at->format('Y-m-d H:i:s'),
            $user->roles->pluck('name')->implode(', ')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet
                    ->getStyle('A1:N1')
                    ->getFont()
                    ->setBold(true);
            },
        ];
    }
}