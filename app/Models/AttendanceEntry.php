<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_register_id',
        'participant_id',
        'attendance_date',
        'status',
        'in_time',
        'in_latitude',
        'in_longitude',
        'out_time',
        'out_latitude',
        'out_longitude',
        'marked_by',
        'source',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function register()
    {
        return $this->belongsTo(AttendanceRegister::class, 'attendance_register_id');
    }

    public function participant()
    {
        return $this->belongsTo(AttendanceRegisterParticipant::class, 'participant_id');
    }
}
