<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRegisterWeekdayRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_register_id',
        'weekday',
        'default_in_time',
        'default_out_time',
    ];

    public function register()
    {
        return $this->belongsTo(AttendanceRegister::class, 'attendance_register_id');
    }
}