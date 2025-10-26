<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorManpower extends Model
{
    protected $fillable = [
        'distributor_id', 'sales', 'accounts', 'godown'
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}