<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $sy   = SchoolYear::active();
        $syId = $sy?->id ?? 0;

        $stats = [
            'total_students'  => Student::count(),
            'total_enrolled'  => Enrollment::where('school_year_id', $syId)->where('status', 'Enrolled')->count(),
            'total_pending'   => Enrollment::where('school_year_id', $syId)->where('status', 'Pending')->count(),
            'total_jhs'       => Enrollment::where('school_year_id', $syId)->where('level_type', 'JHS')->where('status', 'Enrolled')->count(),
            'total_shs'       => Enrollment::where('school_year_id', $syId)->where('level_type', 'SHS')->where('status', 'Enrolled')->count(),
            'total_new'       => Enrollment::where('school_year_id', $syId)->where('enrollment_type', 'New')->count(),
        ];

        $gradeBreakdown = Enrollment::where('school_year_id', $syId)
            ->where('status', 'Enrolled')
            ->select('grade_level', DB::raw('count(*) as cnt'))
            ->groupBy('grade_level')
            ->orderBy('grade_level')
            ->get();

        $strandBreakdown = Enrollment::where('school_year_id', $syId)
            ->where('level_type', 'SHS')
            ->where('status', 'Enrolled')
            ->whereNotNull('strand')
            ->select('strand', DB::raw('count(*) as cnt'))
            ->groupBy('strand')
            ->orderByDesc('cnt')
            ->get();

        $recentEnrollments = Enrollment::with(['student', 'schoolYear'])
            ->where('school_year_id', $syId)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'gradeBreakdown', 'strandBreakdown', 'recentEnrollments', 'sy'));
    }
}
