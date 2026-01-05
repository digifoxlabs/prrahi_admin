<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retailer extends Model
{
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
        'distributor_id',
        'appointed_by_id',
        'appointment_date',
        'appointed_by_type',
    ];

    /* ---------------- Relationships ---------------- */

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
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
}