<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'distributor_id',
        'subtotal',
        'sgst',
        'cgst',
        'dsicount',
        'total_amount',
        'status',
        'created_by_id',     // ✅ Add this
        'created_by_type',   // ✅ Add this
        'created_at',
    ];

    // Relationships

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => '🕒 Pending',
            'confirmed' => '✅ Confirmed',
            'cancelled' => '❌ Cancelled',
            default => ucfirst($this->status),
        };
    }



    //Auto Generate Order Number
protected static function booted()
{
    static::creating(function ($order) {
        if (empty($order->order_number)) {
            $today = now()->format('Ymd');
            $prefix = "ORD-{$today}-";

            // Get the max number for today with the pattern
            $lastOrderNumber = DB::table('orders')
                ->where('order_number', 'like', $prefix . '%')
                ->orderByDesc('order_number')
                ->value('order_number');

            // Extract last number
            if ($lastOrderNumber) {
                $lastNumber = (int) Str::afterLast($lastOrderNumber, '-');
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }

            $order->order_number = $prefix . $nextNumber;
        }
    });
}


    public function created_by()
    {
        return $this->morphTo();
    }


    public function tallyInvoice()
    {
        return $this->hasOne(\App\Models\TallyInvoice::class, 'order_number', 'order_number');
    }




}