<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorDocument extends Model
{
    use HasFactory;
    protected $fillable = ['distributor_id', 'doc_title', 'document'];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
