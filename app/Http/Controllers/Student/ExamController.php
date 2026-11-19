<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Examination;
use App\Models\SchoolYear;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function index()
    {
        $student    = Auth::guard('student')->user();
        $sy         = SchoolYear::active();
        $enrollment = $this->paymentService->getActiveEnrollment($student->id);

        if (!$enrollment) {
            return view('student.exams.index', [
                'student'    => $student,
                'sy'         => $sy,
                'enrollment' => null,
                'exams'      => collect(),
                'paidQuarters' => [],
            ]);
        }

        $this->paymentService->ensureQuarterlyPayments($enrollment);
        $summary = $this->paymentService->getPaymentSummary($enrollment);

        $paidQuarters = $summary['payments']
            ->where('status', 'paid')
            ->pluck('quarter')
            ->toArray();

        $exams = Examination::where('school_year_id', $sy->id)
            ->where('grade_level', $enrollment->grade_level)
            ->where('is_active', true)
            ->orderBy('quarter')
            ->orderBy('exam_date')
            ->get()
            ->map(function ($exam) use ($paidQuarters) {
                $exam->can_take = in_array($exam->quarter, $paidQuarters);
                return $exam;
            });

        return view('student.exams.index', compact(
            'student', 'sy', 'enrollment', 'exams', 'paidQuarters', 'summary'
        ));
    }
}
