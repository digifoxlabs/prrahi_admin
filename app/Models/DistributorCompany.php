<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorCompany extends Model
{
    protected $fillable = [
        'distributor_id', 'company_name', 'segment', 'brand_name', 'products',
        'working_as', 'margin', 'payment_terms', 'working_since', 'area_operation',
        'monthly_to', 'dsr_no', 'details'
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}