<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRegisterDateOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_register_id',
        'attendance_date',
        'is_holiday',
        'holiday_name',
        'in_time',
        'out_time',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_holiday' => 'boolean',
    ];

    public function register()
    {
        return $this->belongsTo(AttendanceRegister::class, 'attendance_register_id');
    }
}