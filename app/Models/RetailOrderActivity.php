<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailOrderActivity extends Model
{
    use HasFactory;

        protected $fillable = [
        'retail_order_id',
        'event',
        'remarks',
        'performed_by_id',
        'performed_by_type',
    ];

    public function order()
    {
        return $this->belongsTo(RetailOrder::class);
    }

    public function performedBy()
    {
        return $this->morphTo(
            name: 'performed_by',
            type: 'performed_by_type',
            id: 'performed_by_id'
        );
    }

}
