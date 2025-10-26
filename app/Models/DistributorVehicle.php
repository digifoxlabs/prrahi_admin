<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorVehicle extends Model
{
    protected $fillable = [
        'distributor_id', 'two_wheeler', 'three_wheeler', 'four_wheeler'
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}