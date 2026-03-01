<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRegisterParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_register_id',
        'employee_type',
        'employee_id',
        'identifier',
        'display_name',
        'sort_name',
    ];

    public function register()
    {
        return $this->belongsTo(AttendanceRegister::class, 'attendance_register_id');
    }

    public function entries()
    {
        return $this->hasMany(AttendanceEntry::class, 'participant_id');
    }
}