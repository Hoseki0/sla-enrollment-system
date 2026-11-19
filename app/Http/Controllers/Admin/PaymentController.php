<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeSchedule;
use App\Models\QuarterlyPayment;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $sy = SchoolYear::active();

        $query = QuarterlyPayment::with(['student', 'enrollment', 'feeSchedule'])
            ->when($sy, fn ($q) => $q->where('school_year_id', $sy->id))
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('quarter')) {
            $query->where('quarter', $request->quarter);
        }

        if ($request->filled('grade_level')) {
            $query->whereHas('enrollment', fn ($q) => $q->where('grade_level', $request->grade_level));
        }

        $payments = $query->paginate(25)->withQueryString();

        $stats = [
            'total'   => QuarterlyPayment::when($sy, fn ($q) => $q->where('school_year_id', $sy->id))->count(),
            'paid'    => QuarterlyPayment::when($sy, fn ($q) => $q->where('school_year_id', $sy->id))->where('status', 'paid')->count(),
            'pending' => QuarterlyPayment::when($sy, fn ($q) => $q->where('school_year_id', $sy->id))->where('status', 'pending')->count(),
            'collected' => QuarterlyPayment::when($sy, fn ($q) => $q->where('school_year_id', $sy->id))->where('status', 'paid')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'sy', 'stats'));
    }

    public function markPaid(QuarterlyPayment $payment)
    {
        if ($payment->isPaid()) {
            return back()->with('error', 'Payment is already marked as paid.');
        }

        $payment->update([
            'status'      => 'paid',
            'paid_at'     => now(),
            'gateway_ref' => 'MANUAL-' . now()->format('YmdHis'),
            'remarks'     => 'Manually marked as paid by admin',
        ]);

        return back()->with('success', "Payment {$payment->payment_ref} marked as paid.");
    }
}
