<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $fillable = ['year_label', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public static function active()
    {
        return static::where('is_active', true)->first();
    }

    public function setActive(): void
    {
        static::where('id', '!=', $this->id)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }
}
