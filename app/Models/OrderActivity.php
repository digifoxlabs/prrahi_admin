<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderActivity extends Model
{
    protected $fillable = [
        'order_id',
        'event',
        'remarks',
        'performed_by_id',
        'performed_by_type',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // public function performedBy()
    // {
    //     return $this->morphTo();
    // }


        public function performedBy()
    {
        return $this->morphTo(
            name: 'performed_by',
            type: 'performed_by_type',
            id: 'performed_by_id'
        );
    }



}
