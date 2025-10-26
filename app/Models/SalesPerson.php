<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SalesPerson extends Authenticatable
{
    use Notifiable;

    protected $table = 'sales_persons';

    protected $fillable = [
        'name', 'designation', 'headquarter', 'address_line_1', 'address_line_2', 'town', 'district',
        'state', 'pincode', 'phone', 'official_email', 'personal_email', 'date_of_birth',
        'date_of_anniversary', 'zone', 'state_covered', 'district_covered','town_covered',
        'profile_photo', 'login_id', 'password',
    ];

    protected $hidden = ['password'];


    public function distributors()
{
    return $this->hasMany(Distributor::class, 'sales_persons_id');
}

}