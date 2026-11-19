<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuarterlyPayment extends Model
{
    protected $fillable = [
        'payment_ref', 'student_id', 'enrollment_id', 'school_year_id',
        'fee_schedule_id', 'quarter', 'amount', 'payment_method',
        'status', 'gateway_ref', 'paid_at', 'remarks',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function feeSchedule()
    {
        return $this->belongsTo(FeeSchedule::class);
    }

    public static function generatePaymentRef(): string
    {
        return 'PAY-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'paid'      => 'badge-enrolled',
            'pending'   => 'badge-pending',
            'failed'    => 'badge-cancelled',
            'cancelled' => 'badge-dropped',
            default     => 'badge-pending',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'gcash' => 'GCash',
            'maya'  => 'Maya',
            default => '—',
        };
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
