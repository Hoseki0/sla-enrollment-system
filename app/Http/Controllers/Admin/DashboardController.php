<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\DocumentRequest;
use App\Models\Enrollment;
use App\Models\QuarterlyPayment;
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
            ->limit(8)
            ->get();

        // Monthly enrollment trend (last 6 months)
        $monthlyTrend = Enrollment::where('school_year_id', $syId)
            ->select(
                DB::raw("DATE_FORMAT(enrollment_date, '%Y-%m') as month"),
                DB::raw('count(*) as cnt')
            )
            ->where('enrollment_date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Payment summary by quarter
        $paymentByQuarter = QuarterlyPayment::where('school_year_id', $syId)
            ->select('quarter', 'status', DB::raw('sum(amount) as total'), DB::raw('count(*) as cnt'))
            ->groupBy('quarter', 'status')
            ->get();

        // Today's stats
        $todayStats = [
            'enrollments_today' => Enrollment::whereDate('created_at', today())->count(),
            'payments_today'    => QuarterlyPayment::whereDate('paid_at', today())->where('status', 'paid')->count(),
            'collected_today'   => QuarterlyPayment::whereDate('paid_at', today())->where('status', 'paid')->sum('amount'),
        ];

        // Pending document requests
        $pendingDocRequests = DocumentRequest::where('status', 'pending')->count();

        // Recent announcements count
        $activeAnnouncements = Announcement::published()->count();

        return view('admin.dashboard', compact(
            'stats', 'gradeBreakdown', 'strandBreakdown', 'recentEnrollments', 'sy',
            'monthlyTrend', 'paymentByQuarter', 'todayStats',
            'pendingDocRequests', 'activeAnnouncements'
        ));
    }
}
