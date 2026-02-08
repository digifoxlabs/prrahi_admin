<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Distributor extends Authenticatable
{
    use HasApiTokens;

    protected $guard = 'distributor';

    protected $casts = [
    'appointment_date' => 'date',
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
        return $this->belongsTo(SalesPerson::class, 'sales_persons_id');
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