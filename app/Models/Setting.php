<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'label'];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting?->value ?? $default;
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $label = ''): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'label' => $label]
        );
        Cache::forget("setting_{$key}");
    }

    /**
     * Get all settings as a keyed collection.
     */
    public static function allKeyed(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Seed default settings if they don't exist.
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            ['key' => 'school_name',       'value' => 'Sta. Lucia Academy',          'group' => 'general',  'label' => 'School Name'],
            ['key' => 'school_tagline',    'value' => 'Excellence in Education',     'group' => 'general',  'label' => 'School Tagline'],
            ['key' => 'school_address',    'value' => '',                            'group' => 'contact',  'label' => 'School Address'],
            ['key' => 'school_phone',      'value' => '',                            'group' => 'contact',  'label' => 'Phone Number'],
            ['key' => 'school_email',      'value' => '',                            'group' => 'contact',  'label' => 'Email Address'],
            ['key' => 'school_principal',  'value' => '',                            'group' => 'general',  'label' => 'Principal / Administrator'],
            ['key' => 'school_registrar',  'value' => '',                            'group' => 'general',  'label' => 'Registrar'],
            ['key' => 'enrollment_open',   'value' => '1',                           'group' => 'academic', 'label' => 'Enrollment Open (1=Yes, 0=No)'],
            ['key' => 'enrollment_notice', 'value' => 'Enrollment is now open!',     'group' => 'academic', 'label' => 'Enrollment Notice Message'],
        ];

        foreach ($defaults as $d) {
            static::firstOrCreate(['key' => $d['key']], $d);
        }
    }
}
