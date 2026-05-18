<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['section_name', 'grade_level', 'track', 'strand', 'max_capacity', 'school_year_id'];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledCount(): int
    {
        return $this->enrollments()->where('status', 'Enrolled')->count();
    }

    public function getDisplayNameAttribute(): string
    {
        $name = $this->section_name . ' - ' . $this->grade_level;
        if ($this->strand) {
            $name .= ' (' . $this->strand . ')';
        }
        return $name;
    }
}
