<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\FeeSchedule;
use App\Models\QuarterlyPayment;
use App\Models\SchoolYear;

class PaymentService
{
    public function getActiveEnrollment(int $studentId): ?Enrollment
    {
        $sy = SchoolYear::active();
        if (!$sy) {
            return null;
        }

        return Enrollment::where('student_id', $studentId)
            ->where('school_year_id', $sy->id)
            ->where('status', 'Enrolled')
            ->latest()
            ->first();
    }

    /**
     * Ensure payment records exist for all 4 quarters of an enrollment.
     */
    public function ensureQuarterlyPayments(Enrollment $enrollment): void
    {
        $schedules = FeeSchedule::where('school_year_id', $enrollment->school_year_id)
            ->where('grade_level', $enrollment->grade_level)
            ->get()
            ->keyBy('quarter');

        for ($q = 1; $q <= 4; $q++) {
            $schedule = $schedules->get($q);
            if (!$schedule) {
                continue;
            }

            QuarterlyPayment::firstOrCreate(
                ['enrollment_id' => $enrollment->id, 'quarter' => $q],
                [
                    'payment_ref'     => QuarterlyPayment::generatePaymentRef(),
                    'student_id'      => $enrollment->student_id,
                    'school_year_id'  => $enrollment->school_year_id,
                    'fee_schedule_id' => $schedule->id,
                    'amount'          => $schedule->amount,
                    'status'          => 'pending',
                ]
            );
        }
    }

    public function isQuarterPaid(Enrollment $enrollment, int $quarter): bool
    {
        return QuarterlyPayment::where('enrollment_id', $enrollment->id)
            ->where('quarter', $quarter)
            ->where('status', 'paid')
            ->exists();
    }

    public function getPaymentSummary(Enrollment $enrollment): array
    {
        $payments = QuarterlyPayment::where('enrollment_id', $enrollment->id)
            ->with('feeSchedule')
            ->orderBy('quarter')
            ->get();

        $totalDue    = $payments->sum('amount');
        $totalPaid   = $payments->where('status', 'paid')->sum('amount');
        $paidCount   = $payments->where('status', 'paid')->count();

        return [
            'payments'   => $payments,
            'total_due'  => $totalDue,
            'total_paid' => $totalPaid,
            'balance'    => $totalDue - $totalPaid,
            'paid_count' => $paidCount,
        ];
    }
}
