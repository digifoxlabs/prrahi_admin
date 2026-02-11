<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
