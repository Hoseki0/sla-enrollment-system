<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\QuarterlyPayment;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function enrollmentReport(Request $request)
    {
        $sy   = SchoolYear::active();
        $syId = $request->get('sy', $sy?->id ?? 0);

        $query = Enrollment::with(['student', 'section', 'schoolYear'])
            ->where('school_year_id', $syId)
            ->where('status', 'Enrolled');

        if ($request->filled('level'))  $query->where('level_type', $request->level);
        if ($request->filled('grade'))  $query->where('grade_level', $request->grade);
        if ($request->filled('strand')) $query->where('strand', $request->strand);

        $enrollments = $query->join('students', 'students.id', '=', 'enrollments.student_id')
            ->select('enrollments.*')
            ->orderBy('enrollments.grade_level')
            ->orderBy('students.last_name')
            ->orderBy('students.first_name')
            ->get();
        $schoolYears = SchoolYear::orderByDesc('id')->get();
        $selectedSy  = SchoolYear::find($syId);

        if ($request->boolean('print')) {
            return view('admin.reports.enrollment-print', compact('enrollments', 'selectedSy', 'request'));
        }

        return view('admin.reports.enrollment', compact('enrollments', 'schoolYears', 'sy', 'syId', 'selectedSy'));
    }

    public function paymentReport(Request $request)
    {
        $sy   = SchoolYear::active();
        $syId = $request->get('sy', $sy?->id ?? 0);

        $query = QuarterlyPayment::with(['student', 'enrollment', 'feeSchedule'])
            ->where('school_year_id', $syId);

        if ($request->filled('quarter')) $query->where('quarter', $request->quarter);
        if ($request->filled('status'))  $query->where('status', $request->status);

        $payments    = $query->latest()->get();
        $schoolYears = SchoolYear::orderByDesc('id')->get();
        $selectedSy  = SchoolYear::find($syId);

        $summary = [
            'total_collected' => $payments->where('status', 'paid')->sum('amount'),
            'total_pending'   => $payments->where('status', 'pending')->sum('amount'),
            'count_paid'      => $payments->where('status', 'paid')->count(),
            'count_pending'   => $payments->where('status', 'pending')->count(),
        ];

        if ($request->boolean('print')) {
            return view('admin.reports.payment-print', compact('payments', 'selectedSy', 'summary', 'request'));
        }

        return view('admin.reports.payment', compact('payments', 'schoolYears', 'sy', 'syId', 'selectedSy', 'summary'));
    }

    public function reportCard(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'section', 'schoolYear', 'gradeEntries.subject']);

        $gradeEntries = $enrollment->gradeEntries
            ->where('status', 'published')
            ->groupBy(fn ($e) => $e->subject->subject_name);

        return view('admin.reports.report-card', compact('enrollment', 'gradeEntries'));
    }
}
