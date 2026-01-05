<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorProduct extends Model
{
    protected $fillable = [
        'distributor_id',
        'product_id',
        'product_name',
        'sku',
        'variant',
        'mrp',
        'ptr',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function stock()
    {
        return $this->hasOne(DistributorStock::class);
    }
}