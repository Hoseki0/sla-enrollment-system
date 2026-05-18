<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $student->load(['enrollments.schoolYear', 'enrollments.section']);

        $sy              = SchoolYear::active();
        $latestEnrollment = $student->enrollments
            ->where('school_year_id', $sy?->id)
            ->sortByDesc('created_at')
            ->first();

        $enrollmentHistory = $student->enrollments->sortByDesc('created_at');

        return view('student.dashboard', compact('student', 'latestEnrollment', 'enrollmentHistory', 'sy'));
    }

    public function profile()
    {
        $student = Auth::guard('student')->user();
        return view('student.profile', compact('student'));
    }
}
