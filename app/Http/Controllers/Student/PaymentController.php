<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\QuarterlyPayment;
use App\Models\SchoolYear;
use App\Services\PaymentGatewayService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected PaymentGatewayService $gateway
    ) {}

    public function index()
    {
        $student    = Auth::guard('student')->user();
        $sy         = SchoolYear::active();
        $enrollment = $this->paymentService->getActiveEnrollment($student->id);

        if (!$enrollment) {
            return view('student.payments.index', [
                'student'    => $student,
                'sy'         => $sy,
                'enrollment' => null,
                'summary'    => null,
            ]);
        }

        $this->paymentService->ensureQuarterlyPayments($enrollment);
        $summary = $this->paymentService->getPaymentSummary($enrollment);

        return view('student.payments.index', compact('student', 'sy', 'enrollment', 'summary'));
    }

    public function pay(Request $request, int $quarter)
    {
        $request->validate([
            'payment_method' => 'required|in:gcash,maya',
        ]);

        $student    = Auth::guard('student')->user();
        $enrollment = $this->paymentService->getActiveEnrollment($student->id);

        if (!$enrollment) {
            return back()->with('error', 'No active enrollment found.');
        }

        $this->paymentService->ensureQuarterlyPayments($enrollment);

        $payment = QuarterlyPayment::where('enrollment_id', $enrollment->id)
            ->where('quarter', $quarter)
            ->firstOrFail();

        if ($payment->isPaid()) {
            return back()->with('error', 'This quarter is already paid.');
        }

        $payment->update([
            'payment_method' => $request->payment_method,
            'status'         => 'pending',
        ]);

        $result = $this->gateway->initiateCheckout($payment, $request->payment_method);

        if (!$result['success']) {
            return back()->with('error', $result['message'] ?? 'Payment initiation failed.');
        }

        return redirect($result['checkout_url']);
    }

    public function checkout(Request $request, string $payment)
    {
        $record = QuarterlyPayment::where('payment_ref', $payment)
            ->where('student_id', Auth::guard('student')->id())
            ->firstOrFail();

        if ($record->isPaid()) {
            return redirect()->route('student.payments.index')
                ->with('success', 'Payment already completed.');
        }

        $method = $request->query('method', $record->payment_method);

        return view('student.payments.checkout', compact('record', 'method'));
    }

    public function processCheckout(Request $request, string $payment)
    {
        $record = QuarterlyPayment::where('payment_ref', $payment)
            ->where('student_id', Auth::guard('student')->id())
            ->firstOrFail();

        if ($record->isPaid()) {
            return redirect()->route('student.payments.index')
                ->with('success', 'Payment already completed.');
        }

        $request->validate(['action' => 'required|in:confirm,cancel']);

        if ($request->action === 'cancel') {
            $record->update(['status' => 'cancelled']);
            return redirect()->route('student.payments.index')
                ->with('error', 'Payment was cancelled.');
        }

        $this->gateway->confirmPayment($record);

        return redirect()->route('student.payments.index')
            ->with('success', "Quarter {$record->quarter} payment of ₱" . number_format($record->amount, 2) . " via {$record->payment_method_label} was successful!");
    }

    public function callback(Request $request, string $payment)
    {
        $record = QuarterlyPayment::where('payment_ref', $payment)
            ->where('student_id', Auth::guard('student')->id())
            ->firstOrFail();

        if ($request->query('status') === 'success') {
            $this->gateway->confirmPayment($record, $request->query('gateway_ref'));
            return redirect()->route('student.payments.index')
                ->with('success', "Quarter {$record->quarter} payment completed successfully!");
        }

        $this->gateway->failPayment($record, 'Payment cancelled by user');
        return redirect()->route('student.payments.index')
            ->with('error', 'Payment was not completed.');
    }
}
