<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'subject_code', 'subject_name', 'grade_level', 'strand', 'semester', 'units', 'is_active',
    ];

    protected $casts = [
        'units'     => 'decimal:1',
        'is_active' => 'boolean',
    ];

    public function gradeEntries()
    {
        return $this->hasMany(GradeEntry::class);
    }

    public static function forEnrollment(Enrollment $enrollment)
    {
        $query = static::where('grade_level', $enrollment->grade_level)
            ->where('is_active', true);

        if ($enrollment->strand) {
            $query->where(function ($q) use ($enrollment) {
                $q->whereNull('strand')->orWhere('strand', $enrollment->strand);
            });
        }

        if ($enrollment->semester) {
            $sem = is_numeric($enrollment->semester)
                ? (int) $enrollment->semester
                : (str_starts_with((string) $enrollment->semester, '2') ? 2 : 1);
            $query->where(function ($q) use ($sem) {
                $q->whereNull('semester')->orWhere('semester', $sem);
            });
        }

        return $query->orderBy('subject_code');
    }
}
