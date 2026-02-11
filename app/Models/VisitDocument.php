<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitDocument extends Model
{
    protected $fillable = [
        'visit_note_id',
        'file_path',
        'file_type',
    ];

    public function getFilePathAttribute($value)
{
    return $value
        ? asset('storage/' . $value)
        : null;
}

}
