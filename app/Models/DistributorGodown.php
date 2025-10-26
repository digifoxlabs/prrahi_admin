<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorGodown extends Model
{
    protected $fillable = [
        'distributor_id', 'no_godown', 'godown_size'
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}