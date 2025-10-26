<?php

namespace App\Exports;

use App\Models\Permission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection; // ✅ This is what should be imported

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PermissionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $permissions;

    public function __construct(Collection $permissions)
    {
        $this->permissions = $permissions;
    }

    public function collection()
    {
        return $this->permissions;
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Created At'];
    }

    public function map($permission): array
    {
        return [
            $permission->id,
            $permission->name,
            $permission->created_at ? $permission->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }
}