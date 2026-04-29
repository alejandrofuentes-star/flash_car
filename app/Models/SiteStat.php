<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteStat extends Model
{
    protected $primaryKey = 'key';
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $fillable   = ['key', 'value'];

    public static function get(string $key, int $default = 0): int
    {
        return (int) (static::find($key)?->value ?? $default);
    }

    public static function addOne(string $key): void
    {
        static::where('key', $key)->increment('value');
    }
}
