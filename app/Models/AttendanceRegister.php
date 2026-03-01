<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function participants()
    {
        return $this->hasMany(AttendanceRegisterParticipant::class);
    }

    public function weekdayRules()
    {
        return $this->hasMany(AttendanceRegisterWeekdayRule::class);
    }

    public function dateOverrides()
    {
        return $this->hasMany(AttendanceRegisterDateOverride::class);
    }

    public function entries()
    {
        return $this->hasMany(AttendanceEntry::class);
    }
}