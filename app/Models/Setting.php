<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function reRegistrationStartForLevel(int $levelId): ?string
    {
        return static::get("re_registration_start_{$levelId}") ?? static::get('re_registration_start');
    }

    public static function reRegistrationEndForLevel(int $levelId): ?string
    {
        return static::get("re_registration_end_{$levelId}") ?? static::get('re_registration_end');
    }
}