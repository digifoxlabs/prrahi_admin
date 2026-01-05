<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerSaleItem extends Model
{
    protected $fillable = [
        'retailer_sale_id',
        'distributor_product_id',
        'quantity',
        'rate',
    ];

        public function sale()
    {
        return $this->belongsTo(RetailerSale::class, 'retailer_sale_id', 'id');
    }


    public function product()
    {
        return $this->belongsTo(DistributorProduct::class, 'distributor_product_id');
    }
}
