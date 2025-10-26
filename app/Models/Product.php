<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'name','code','type', 'category_id', 'sub_category_id',
        'unit', 'dozen_per_case', 'mrp_per_unit', 'ptr_per_dozen',
        'ptd_per_dozen', 'weight_gm', 'size', 'attributes','has_free_qty','free_dozen_per_case'
    ];

    protected $casts = ['attributes' => 'array'];

    public function parent() {
        return $this->belongsTo(Product::class, 'parent_id');
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

    // public function getTotalStockAttribute()
    // {
    //     return $this->inventoryTransactions()->sum(DB::raw("CASE 
    //         WHEN type IN ('opening', 'purchase', 'return') THEN quantity
    //         WHEN type IN ('sale', 'adjustment') THEN -quantity
    //         ELSE 0 END"));
    // }


    public function getTotalStockAttribute()
    {
        return $this->inventoryTransactions()
            ->selectRaw("COALESCE(SUM(CASE 
                WHEN type = 'in' THEN quantity 
                WHEN type IN ('out', 'adjustment') THEN -quantity 
                ELSE 0 END), 0) as total")
            ->value('total');
    }


    public function getAvailableStock()
        {
            $in = $this->inventoryTransactions()->where('type', 'in')->sum('quantity');
            $out = $this->inventoryTransactions()->whereIn('type', ['out', 'reserved'])->sum('quantity');

            return $in - $out;
        }






}
