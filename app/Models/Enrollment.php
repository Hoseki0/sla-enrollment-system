<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'enrollment_no', 'student_id', 'school_year_id', 'section_id', 'encoded_by',
        'level_type', 'grade_level', 'track', 'strand', 'semester',
        'enrollment_type', 'status', 'enrollment_date', 'remarks',
    ];

    protected $casts = ['enrollment_date' => 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function encodedBy()
    {
        return $this->belongsTo(Admin::class, 'encoded_by');
    }

    public static function generateEnrollmentNo(int $schoolYearId): string
    {
        $sy    = SchoolYear::findOrFail($schoolYearId);
        $code  = str_replace('-', '', $sy->year_label);
        $count = static::where('school_year_id', $schoolYearId)->count() + 1;
        return 'ENR-' . $code . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'Enrolled'  => 'badge-enrolled',
            'Pending'   => 'badge-pending',
            'Cancelled' => 'badge-cancelled',
            'Dropped'   => 'badge-dropped',
            default     => 'badge-pending',
        };
    }
}
