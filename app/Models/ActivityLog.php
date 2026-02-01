<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'actor_type',
        'actor_id',
        'activity_type',
        'subject_type',
        'subject_id',
        'latitude',
        'longitude',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     */

    public function actor()
    {
        return $this->morphTo();
    }

    public function subject()
    {
        return $this->morphTo();
    }
}
