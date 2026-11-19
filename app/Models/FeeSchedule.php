<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeSchedule extends Model
{
    protected $fillable = [
        'school_year_id', 'grade_level', 'quarter', 'amount', 'due_date', 'description',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'due_date' => 'date',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function payments()
    {
        return $this->hasMany(QuarterlyPayment::class);
    }

    public function getQuarterLabelAttribute(): string
    {
        return 'Quarter ' . $this->quarter;
    }
}
