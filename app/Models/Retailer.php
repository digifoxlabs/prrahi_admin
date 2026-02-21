<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use LogicException;

class Retailer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'retailer_name',
        'address_line_1',
        'address_line_2',
        'town',
        'district',
        'state',
        'pincode',
        'landmark',
        'contact_person',
        'contact_number',
        'email',
        'gst',
        'date_of_birth',
        'date_of_anniversary',
        'nature_of_outlet',
        'latitude',
        'longitude',
        'distributor_id',
        'appointed_by_id',
        'appointment_date',
        'appointed_by_type',
    ];

    /* ---------------- Relationships ---------------- */

    public function distributor()
    {
        return $this->belongsTo(Distributor::class)->withTrashed();
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // ✅ Polymorphic relationship
    public function appointedBy()
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::deleting(function (self $retailer): void {
            if (! $retailer->isForceDeleting()) {
                return;
            }

            $id = $retailer->getKey();
            $isReferenced = DB::table('orders')->where('retailer_id', $id)->exists()
                || DB::table('retailer_sales')->where('retailer_id', $id)->exists()
                || DB::table('retail_orders')->where('retailer_id', $id)->exists()
                || DB::table('orders')->where('created_by_type', self::class)->where('created_by_id', $id)->exists()
                || DB::table('retail_orders')->where('created_by_type', self::class)->where('created_by_id', $id)->exists()
                || DB::table('order_activities')->where('performed_by_type', self::class)->where('performed_by_id', $id)->exists()
                || DB::table('retail_order_activities')->where('performed_by_type', self::class)->where('performed_by_id', $id)->exists();

            if ($isReferenced) {
                throw new LogicException('Retailer is referenced in other records and cannot be permanently deleted.');
            }
        });
    }
}
