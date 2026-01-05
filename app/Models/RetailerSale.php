<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerSale extends Model
{
    protected $fillable = [
        'distributor_id',
        'retailer_id',
        'sale_date',
        'total_qty',
    ];


    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function items()
    {
        return $this->hasMany(RetailerSaleItem::class);
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'retailer_id');
    }


}
