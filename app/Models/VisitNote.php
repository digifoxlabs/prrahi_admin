<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Distributor;
use App\Models\Retailer;

class VisitNote extends Model
{
    protected $fillable = [
        'sales_person_id',
        'entity_id',
        'entity_type',
        'message',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function documents()
    {
        return $this->hasMany(VisitDocument::class);
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'entity_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'entity_id');
    }

    /**
     * Helper accessor for UI
     */
    public function getEntityLabelAttribute()
    {
        return $this->entity_type === 'retailer'
            ? 'Retailer'
            : 'Distributor';
    }

    public function getEntityNameAttribute()
    {
        if ($this->entity_type === 'retailer') {
            return optional($this->retailer)->retailer_name;
        }

        return optional($this->distributor)->firm_name;
    }

}

