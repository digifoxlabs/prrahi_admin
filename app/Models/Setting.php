<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
        protected $fillable = ['group', 'key', 'value'];

    public $timestamps = true;

    public static function get($group, $key, $default = null)
    {
        return static::where('group', $group)
                     ->where('key', $key)
                     ->value('value') ?? $default;
    }

    public static function set($group, $key, $value)
    {
        return static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );
    }
}
