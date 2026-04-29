<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $fillable   = ['key', 'value', 'label'];

    public static function get(string $key, string $default = ''): string
    {
        return Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            return (string) (static::find($key)?->value ?? $default);
        });
    }

    public static function set(string $key, string $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
        Cache::forget('setting_' . $key);
    }
}
