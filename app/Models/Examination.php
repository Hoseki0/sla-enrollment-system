<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    protected $fillable = [
        'school_year_id', 'title', 'grade_level', 'quarter',
        'exam_date', 'start_time', 'end_time', 'venue', 'instructions', 'is_active',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function getQuarterLabelAttribute(): string
    {
        return 'Quarter ' . $this->quarter;
    }
}
