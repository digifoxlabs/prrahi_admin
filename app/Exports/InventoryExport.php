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
        return InventoryTransaction::with(['product.parent'])
            ->when($this->productId, function ($query) {
                $query->where('product_id', $this->productId);
            })
            ->orderByDesc('date')
            ->get()
            ->map(function ($inv) {
                $product = $inv->product;
                $isVariant = $product && $product->type === 'variant';
                $productName = $isVariant
                    ? ($product->parent->name ?? $product->name ?? '')
                    : ($product->name ?? '');
                $variantLabel = $isVariant
                    ? implode(', ', array_filter([
                        $product->attributes['fragrance'] ?? null,
                        $product->attributes['size'] ?? null,
                    ]))
                    : '';

                return [
                    'Product'    => $productName,
                    'Variant'    => $variantLabel,
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
