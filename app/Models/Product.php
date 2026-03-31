<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use LogicException;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id', 'name','code','hsn','type', 'category_id', 'sub_category_id',
        'base_unit', 'base_quantity', 'dozen_per_case', 'mrp_per_unit', 'ptr_per_dozen',
        'ptd_per_dozen', 'weight_gm', 'size', 'attributes','distributor_discount_percent','retailer_discount_percent'
    ];

  

    protected $casts = ['attributes' => 'array'];

    // add this (below your $fillable / $casts etc.)
    protected $appends = ['total_stock'];

    public function parent() {
        return $this->belongsTo(Product::class, 'parent_id')->withTrashed();
    }

    public function variants() {
        return $this->hasMany(Product::class, 'parent_id')->where('type', 'variant');
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function subCategory() {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function inventoryTransactions() {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function children()
{
    return $this->hasMany(Product::class, 'parent_id');
}

    protected static function booted(): void
    {
        static::deleting(function (self $product): void {
            if (! $product->isForceDeleting()) {
                return;
            }

            $id = $product->getKey();
            $isReferenced = DB::table('order_items')->where('product_id', $id)->exists()
                || DB::table('retail_order_items')->where('product_id', $id)->exists()
                || DB::table('inventory_transactions')->where('product_id', $id)->exists()
                || DB::table('products')->where('parent_id', $id)->exists()
                || DB::table('distributor_products')->where('product_id', $id)->exists();

            if ($isReferenced) {
                throw new LogicException('Product is referenced in other records and cannot be permanently deleted.');
            }
        });
    }

    public function getAvailableStock()
    {
        return (int) $this->inventoryTransactions()
            ->selectRaw("COALESCE(SUM(CASE
                WHEN type IN ('in', 'opening', 'purchase', 'return') THEN quantity
                WHEN type IN ('out', 'sale', 'hold', 'reserved', 'adjustment') THEN -quantity
                ELSE 0 END), 0) as total")
            ->value('total');
    }


    public function getTotalStockAttribute()
    {
        return $this->inventoryTransactions()
            ->selectRaw("COALESCE(SUM(CASE 
                WHEN type IN ('in', 'opening', 'purchase', 'return') THEN quantity 
                WHEN type IN ('out', 'sale', 'hold', 'reserved', 'adjustment') THEN -quantity 
                ELSE 0 END), 0) as total")
            ->value('total');
    }




}
