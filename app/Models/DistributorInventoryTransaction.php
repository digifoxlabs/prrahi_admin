<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorInventoryTransaction extends Model
{
    protected $fillable = [
        'distributor_id',
        'distributor_product_id',
        'type',
        'quantity',
        'source_type',
        'source_id',
        'remarks',
    ];

    public function source()
    {
        return $this->morphTo();
    }


    public function distributorProduct()
    {
        return $this->belongsTo(DistributorProduct::class, 'distributor_product_id');
    }


}
