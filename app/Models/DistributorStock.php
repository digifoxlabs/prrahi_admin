<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorStock extends Model
{
    protected $fillable = [
        'distributor_id',
        'distributor_product_id',
        'available_qty',
    ];

    public function product()
    {
        return $this->belongsTo(DistributorProduct::class, 'distributor_product_id');
    }
}
