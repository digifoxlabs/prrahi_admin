<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TallyInvoice extends Model
{
    protected $fillable = ['order_number', 'xml_data'];

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_number', 'order_number');
    }
}
