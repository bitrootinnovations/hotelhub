<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'updated_by'];

    /** Get a setting value by key, with optional default. */
    public static function get(string $key, $default = null): ?string
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    /** Set (upsert) a setting value. */
    public static function set(string $key, $value, ?int $updatedBy = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'updated_by' => $updatedBy]
        );
    }
}
