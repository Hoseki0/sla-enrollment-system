<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\GradeEntry;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $sy = SchoolYear::active();

        $enrollments = Enrollment::with(['student', 'section'])
            ->when($sy, fn ($q) => $q->where('school_year_id', $sy->id))
            ->where('status', 'Enrolled')
            ->when($request->grade_level, fn ($q, $gl) => $q->where('grade_level', $gl))
            ->orderBy('grade_level')
            ->get();

        $gradeLevels = $enrollments->pluck('grade_level')->unique()->sort()->values();

        return view('admin.grades.index', compact('enrollments', 'sy', 'gradeLevels'));
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'section', 'schoolYear']);
        $subjects = Subject::forEnrollment($enrollment)->get();

        $entries = GradeEntry::where('enrollment_id', $enrollment->id)
            ->get()
            ->groupBy(fn ($e) => $e->subject_id . '-' . $e->quarter);

        return view('admin.grades.show', compact('enrollment', 'subjects', 'entries'));
    }

    public function store(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'quarter'    => 'required|integer|min:1|max:4',
            'grade'      => 'nullable|numeric|min:0|max:100',
            'status'     => 'required|in:draft,published',
        ]);

        GradeEntry::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'subject_id'    => $data['subject_id'],
                'quarter'       => $data['quarter'],
            ],
            [
                'student_id'     => $enrollment->student_id,
                'school_year_id' => $enrollment->school_year_id,
                'semester'       => $enrollment->semester,
                'grade'          => $data['grade'],
                'status'         => $data['status'],
                'encoded_by'     => Auth::guard('admin')->id(),
            ]
        );

        return back()->with('success', 'Grade saved successfully.');
    }

    public function publishAll(Enrollment $enrollment)
    {
        GradeEntry::where('enrollment_id', $enrollment->id)
            ->whereNotNull('grade')
            ->update(['status' => 'published']);

        return back()->with('success', 'All grades published for this student.');
    }
}
