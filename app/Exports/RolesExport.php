<?php

namespace App\Exports;

use App\Models\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RolesExport implements FromCollection, WithHeadings
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Role::with('permissions')
            ->when($this->search, function ($query, $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($role, $index) {
                return [
                    '#' => $index + 1,
                    'Name' => $role->name,
                    'Permissions' => $role->permissions->pluck('name')->implode(', '),
                ];
            });
    }

    public function headings(): array
    {
        return ['#', 'Name', 'Permissions'];
    }
}
