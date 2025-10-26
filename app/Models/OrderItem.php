<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'rate',
        'total',
        'dozen_case',
        'free_dozen_case',
        
    ];

    // Relationships

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    // Accessors

    public function getProductDisplayNameAttribute()
    {
        if ($this->product->type === 'variant') {
            $fragrance = $this->product->attributes['fragrance'] ?? '';
            $size = $this->product->attributes['size'] ?? '';
            return "{$this->product->parent->name} - {$fragrance} {$size}";
        }

        return $this->product->name;
    }
}