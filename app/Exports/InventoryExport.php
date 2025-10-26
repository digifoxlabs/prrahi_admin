<?php

namespace App\Exports;

use App\Models\InventoryTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class InventoryExport implements FromCollection, WithHeadings
{
    protected $productId;

    public function __construct($productId = null)
    {
        $this->productId = $productId;
    }

    public function collection()
    {
        return InventoryTransaction::with(['product', 'variant'])
            ->when($this->productId, function ($query) {
                $query->where('product_id', $this->productId);
            })
            ->latest()
            ->get()
            ->map(function ($inv) {
                return [
                    'Product'    => $inv->product->name ?? '',
                    'Variant'    => $inv->variant?->attributes['fragrance'] ?? '',
                    'Type'       => ucfirst($inv->type),
                    'Quantity'   => $inv->quantity,
                    'Date'       => Carbon::parse($inv->date)->format('Y-m-d'),
                    'Remarks'    => $inv->remarks,
                ];
            });
    }

    public function headings(): array
    {
        return ['Product', 'Variant', 'Type', 'Quantity', 'Date', 'Remarks'];
    }
}