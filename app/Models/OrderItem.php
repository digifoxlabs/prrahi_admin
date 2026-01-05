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
        'rate',
        'base_unit',
        'quantity',
        'discount_percent',
        'total',
        
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