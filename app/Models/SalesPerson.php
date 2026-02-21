<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use LogicException;
use Laravel\Sanctum\HasApiTokens;

class SalesPerson extends Authenticatable
{
    use Notifiable;
    use HasApiTokens, SoftDeletes;

    // protected $guard = 'sales';


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

    public function appointedRetailers()
    {
        return $this->morphMany(Retailer::class, 'appointed_by');
    }

public function visitNotes()
{
    return $this->hasMany(\App\Models\VisitNote::class, 'sales_person_id');
}

    protected static function booted(): void
    {
        static::deleting(function (self $salesPerson): void {
            if (! $salesPerson->isForceDeleting()) {
                return;
            }

            $id = $salesPerson->getKey();
            $isReferenced = DB::table('distributors')->where('sales_persons_id', $id)->exists()
                || DB::table('visit_notes')->where('sales_person_id', $id)->exists()
                || DB::table('distributors')->where('appointed_by_type', self::class)->where('appointed_by_id', $id)->exists()
                || DB::table('retailers')->where('appointed_by_type', self::class)->where('appointed_by_id', $id)->exists()
                || DB::table('orders')->where('created_by_type', self::class)->where('created_by_id', $id)->exists()
                || DB::table('retail_orders')->where('created_by_type', self::class)->where('created_by_id', $id)->exists()
                || DB::table('order_activities')->where('performed_by_type', self::class)->where('performed_by_id', $id)->exists()
                || DB::table('retail_order_activities')->where('performed_by_type', self::class)->where('performed_by_id', $id)->exists();

            if ($isReferenced) {
                throw new LogicException('Sales person is referenced in other records and cannot be permanently deleted.');
            }
        });
    }


}
