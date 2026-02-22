<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use LogicException;
use Laravel\Sanctum\HasApiTokens;

class Distributor extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    // protected $guard = 'distributor';

    protected $casts = [
        'appointment_date' => 'date',
        'sales_persons_id' => 'integer',
    ];

    protected $fillable = [
        'sales_persons_id', 'appointment_date', 'firm_name', 'nature_of_firm',
        'address_line_1', 'address_line_2', 'town', 'district', 'state', 'pincode', 'landmark',
        'latitude', 'longitude', 'contact_person', 'designation_contact',
        'contact_number', 'email', 'gst', 'date_of_birth', 'date_of_anniversary',
        'profile_photo', 'login_id', 'password','firstname', 'lastname', 'address','appointed_by_type',
        'appointed_by_id'
    ];

    protected $hidden = ['password'];

    public function companies()
    {
        return $this->hasMany(DistributorCompany::class);
    }

    public function banks()
    {
        return $this->hasMany(DistributorBank::class);
    }

    public function godowns()
    {
        return $this->hasMany(DistributorGodown::class);
    }

    public function manpowers()
    {
        return $this->hasMany(DistributorManpower::class);
    }

    public function vehicles()
    {
        return $this->hasMany(DistributorVehicle::class);
    }

    public function salesPerson()
    {
        return $this->belongsTo(SalesPerson::class, 'sales_persons_id')->withTrashed();
    }


    public function appointedRetailers()
    {
        return $this->morphMany(Retailer::class, 'appointed_by');
    }


    // Polymorphic relationship
    public function appointedBy()
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::deleting(function (self $distributor): void {
            if (! $distributor->isForceDeleting()) {
                return;
            }

            $id = $distributor->getKey();
            $isReferenced = DB::table('distributor_documents')->where('distributor_id', $id)->exists()
                || DB::table('distributor_companies')->where('distributor_id', $id)->exists()
                || DB::table('distributor_banks')->where('distributor_id', $id)->exists()
                || DB::table('distributor_godowns')->where('distributor_id', $id)->exists()
                || DB::table('distributor_manpowers')->where('distributor_id', $id)->exists()
                || DB::table('distributor_vehicles')->where('distributor_id', $id)->exists()
                || DB::table('distributor_investments')->where('distributor_id', $id)->exists()
                || DB::table('visit_notes')->where('entity_type', 'distributor')->where('entity_id', $id)->exists()
                || DB::table('retailers')->where('distributor_id', $id)->exists()
                || DB::table('orders')->where('distributor_id', $id)->exists()
                || DB::table('retail_orders')->where('distributor_id', $id)->exists()
                || DB::table('distributor_products')->where('distributor_id', $id)->exists()
                || DB::table('distributor_stocks')->where('distributor_id', $id)->exists()
                || DB::table('distributor_inventory_transactions')->where('distributor_id', $id)->exists()
                || DB::table('retailer_sales')->where('distributor_id', $id)->exists()
                || DB::table('retailers')->where('appointed_by_type', self::class)->where('appointed_by_id', $id)->exists()
                || DB::table('distributors')->where('appointed_by_type', self::class)->where('appointed_by_id', $id)->exists()
                || DB::table('orders')->where('created_by_type', self::class)->where('created_by_id', $id)->exists()
                || DB::table('retail_orders')->where('created_by_type', self::class)->where('created_by_id', $id)->exists()
                || DB::table('order_activities')->where('performed_by_type', self::class)->where('performed_by_id', $id)->exists()
                || DB::table('retail_order_activities')->where('performed_by_type', self::class)->where('performed_by_id', $id)->exists();

            if ($isReferenced) {
                throw new LogicException('Distributor is referenced in other records and cannot be permanently deleted.');
            }
        });
    }


    //Get Billing Address

    public function getFormattedBillingAddressAttribute(): string
    {
        return collect([
            $this->firm_name,
            $this->address_line_1,
            $this->address_line_2,
            $this->town,
            $this->district,
            $this->state ? 'State: ' . $this->state : null,
            $this->pincode ? 'Pincode: ' . $this->pincode : null,
            $this->gst ? 'GST: ' . $this->gst : null,
        ])->filter()->implode("\n");
    }




}
