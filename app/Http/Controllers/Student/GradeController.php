<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GradeEntry;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function index()
    {
        $student    = Auth::guard('student')->user();
        $sy         = SchoolYear::active();
        $enrollment = $this->paymentService->getActiveEnrollment($student->id);

        if (!$enrollment) {
            return view('student.grades.index', [
                'student'    => $student,
                'sy'         => $sy,
                'enrollment' => null,
                'subjects'   => collect(),
                'grades'     => collect(),
                'averages'   => [],
            ]);
        }

        $subjects = Subject::forEnrollment($enrollment)->get();

        $grades = GradeEntry::where('enrollment_id', $enrollment->id)
            ->where('status', 'published')
            ->with('subject')
            ->get()
            ->groupBy(fn ($g) => $g->subject_id . '-' . $g->quarter);

        $gradeMap = GradeEntry::where('enrollment_id', $enrollment->id)
            ->where('status', 'published')
            ->get()
            ->groupBy('subject_id');

        $averages = [];
        foreach ([1, 2, 3, 4] as $q) {
            $qGrades = GradeEntry::where('enrollment_id', $enrollment->id)
                ->where('quarter', $q)
                ->where('status', 'published')
                ->whereNotNull('grade')
                ->pluck('grade');

            $averages[$q] = $qGrades->count() > 0 ? round($qGrades->avg(), 2) : null;
        }

        $generalAverage = collect($averages)->filter()->avg();
        $generalAverage = $generalAverage ? round($generalAverage, 2) : null;

        return view('student.grades.index', compact(
            'student', 'sy', 'enrollment', 'subjects', 'grades', 'gradeMap', 'averages', 'generalAverage'
        ));
    }
}
