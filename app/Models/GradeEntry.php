<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeEntry extends Model
{
    protected $fillable = [
        'student_id', 'enrollment_id', 'subject_id', 'school_year_id',
        'quarter', 'semester', 'grade', 'status', 'encoded_by',
    ];

    protected $casts = ['grade' => 'decimal:2'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function encodedBy()
    {
        return $this->belongsTo(Admin::class, 'encoded_by');
    }

    public function getRemarksAttribute(): string
    {
        if ($this->grade === null) {
            return '—';
        }

        return match (true) {
            $this->grade >= 90 => 'Outstanding',
            $this->grade >= 85 => 'Very Satisfactory',
            $this->grade >= 80 => 'Satisfactory',
            $this->grade >= 75 => 'Fairly Satisfactory',
            default            => 'Did Not Meet Expectations',
        };
    }
}
