<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorBank extends Model
{
    protected $fillable = [
        'distributor_id', 'bank_name', 'branch_name', 'current_ac', 'ifsc'
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}