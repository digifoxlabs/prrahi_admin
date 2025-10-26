<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;


class ProductsExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function array(): array
    {
        $query = Product::with(['category', 'subCategory', 'variants']);

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orWhereHas('subCategory', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        $products = $query->get();
        $data = [];
        $rowNumber = 1;

        foreach ($products as $product) {
            if ($product->type === 'simple') {
                $data[] = [
                    $rowNumber++,
                    $product->name,
                    $this->formatCategory($product),
                    'Simple',
                    $product->total_stock ?? 0 ,
                    $product->dozen_per_case,
                    $product->mrp_per_unit,
                    $product->ptr_per_dozen,
                    $product->ptd_per_dozen,
                ];
            }
             elseif ($product->type === 'variant')
             {
             
                    $data[] = [
                        $rowNumber++,
                        $product->parent->name . ' (' . $product->attributes['fragrance'] ?? null . ')',
                        $this->formatCategory($product->parent),
                        'Variable',
                        $product->total_stock ?? 0 ,
                        $product->dozen_per_case,
                        $product->mrp_per_unit,
                        $product->ptr_per_dozen,
                        $product->ptd_per_dozen,
                    ];
               
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'SL',
            'Name',
            'Category',
            'Type',
            'Stock',
            'Dozen Per Case',
            'MRP / Unit',
            'PTR / Dozen',
            'PTD / Dozen',
        ];
    }

protected function formatCategory($product): string
{
    $main = optional($product->category)->name;
    $sub  = optional($product->subCategory)->name;

    if ($main && $sub) {
        return "$main / $sub";
    }

    return $main ?? $sub ?? '';
}



}