<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GradeEntry;
use App\Models\QuarterlyPayment;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    /**
     * Resolve the currently selected child linked to the authenticated parent.
     */
    protected function resolveChild(Request $request): array
    {
        $parent   = auth('parent')->user();
        $children = $parent->students()->with(['latestEnrollment.section', 'latestEnrollment.schoolYear'])->get();

        $selectedId = $request->query('student_id') ?? session('parent_selected_student_id');
        $child      = $children->firstWhere('id', (int) $selectedId) ?? $children->first();

        if ($child) {
            session(['parent_selected_student_id' => $child->id]);
        }

        return [$parent, $children, $child];
    }

    public function dashboard(Request $request)
    {
        [$parent, $children, $child] = $this->resolveChild($request);
        $sy = SchoolYear::where('is_active', true)->first();

        $latestEnrollment  = null;
        $todayAttendance   = null;
        $recentAttendance  = collect();
        $attendanceSummary = ['Present' => 0, 'Late' => 0, 'Absent' => 0, 'Excused' => 0];
        $paidCount         = 0;
        $pendingCount      = 0;
        $totalPaid         = 0;
        $totalBalance      = 0;
        $recentGrades      = collect();

        if ($child) {
            $latestEnrollment = $child->enrollments()
                ->with(['schoolYear', 'section'])
                ->latest()
                ->first();

            $todayAttendance = Attendance::where('student_id', $child->id)
                ->whereDate('attendance_date', now()->toDateString())
                ->first();

            $allAttendance = Attendance::where('student_id', $child->id)
                ->when($sy, fn ($q) => $q->where('school_year_id', $sy->id))
                ->orderByDesc('attendance_date')
                ->get();

            foreach ($allAttendance as $att) {
                if (isset($attendanceSummary[$att->status])) {
                    $attendanceSummary[$att->status]++;
                }
            }

            $recentAttendance = $allAttendance->take(5);

            $payments = QuarterlyPayment::where('student_id', $child->id)
                ->when($sy, fn ($q) => $q->where('school_year_id', $sy->id))
                ->get();

            $paidCount    = $payments->where('status', 'paid')->count();
            $pendingCount = $payments->where('status', 'pending')->count();
            $totalPaid    = $payments->where('status', 'paid')->sum('amount');
            $totalBalance = $payments->where('status', 'pending')->sum('amount');

            $recentGrades = GradeEntry::with('subject')
                ->where('student_id', $child->id)
                ->where('status', 'published')
                ->when($sy, fn ($q) => $q->where('school_year_id', $sy->id))
                ->orderBy('quarter')
                ->take(6)
                ->get();
        }

        return view('parent.dashboard', compact(
            'parent', 'children', 'child', 'sy', 'latestEnrollment',
            'todayAttendance', 'recentAttendance', 'attendanceSummary',
            'paidCount', 'pendingCount', 'totalPaid', 'totalBalance', 'recentGrades'
        ));
    }

    public function attendance(Request $request)
    {
        [$parent, $children, $child] = $this->resolveChild($request);
        $sy    = SchoolYear::where('is_active', true)->first();
        $month = $request->query('month');

        $records = collect();
        $summary = ['Present' => 0, 'Late' => 0, 'Absent' => 0, 'Excused' => 0];

        if ($child) {
            $query = Attendance::where('student_id', $child->id)
                ->when($sy, fn ($q) => $q->where('school_year_id', $sy->id))
                ->orderByDesc('attendance_date');

            if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
                $query->whereYear('attendance_date', substr($month, 0, 4))
                      ->whereMonth('attendance_date', substr($month, 5, 2));
            }

            $records = $query->get();

            foreach ($records as $rec) {
                if (isset($summary[$rec->status])) {
                    $summary[$rec->status]++;
                }
            }
        }

        return view('parent.attendance', compact('parent', 'children', 'child', 'sy', 'records', 'summary', 'month'));
    }

    public function grades(Request $request)
    {
        [$parent, $children, $child] = $this->resolveChild($request);
        $sy = SchoolYear::where('is_active', true)->first();

        $gradesByQuarter = collect();
        $enrollment      = null;

        if ($child) {
            $enrollment = $child->enrollments()
                ->with(['schoolYear', 'section'])
                ->latest()
                ->first();

            $gradesByQuarter = GradeEntry::with('subject')
                ->where('student_id', $child->id)
                ->where('status', 'published')
                ->when($sy, fn ($q) => $q->where('school_year_id', $sy->id))
                ->orderBy('quarter')
                ->get()
                ->groupBy('quarter');
        }

        return view('parent.grades', compact('parent', 'children', 'child', 'sy', 'enrollment', 'gradesByQuarter'));
    }

    public function payments(Request $request)
    {
        [$parent, $children, $child] = $this->resolveChild($request);
        $sy = SchoolYear::where('is_active', true)->first();

        $payments     = collect();
        $totalPaid    = 0;
        $totalBalance = 0;

        if ($child) {
            $payments = QuarterlyPayment::with('feeSchedule')
                ->where('student_id', $child->id)
                ->when($sy, fn ($q) => $q->where('school_year_id', $sy->id))
                ->orderBy('quarter')
                ->get();

            $totalPaid    = $payments->where('status', 'paid')->sum('amount');
            $totalBalance = $payments->where('status', 'pending')->sum('amount');
        }

        return view('parent.payments', compact('parent', 'children', 'child', 'sy', 'payments', 'totalPaid', 'totalBalance'));
    }

    public function linkChild(Request $request)
    {
        $data = $request->validate([
            'lrn'          => ['required', 'string', 'max:30'],
            'last_name'    => ['required', 'string', 'max:100'],
            'relationship' => ['nullable', 'string', 'max:50'],
        ]);

        $student = Student::where('lrn', trim($data['lrn']))->first();

        if (!$student || strcasecmp(trim($student->last_name), trim($data['last_name'])) !== 0) {
            return back()->withErrors(['lrn' => 'No matching student found with that LRN and Last Name.']);
        }

        $parent = auth('parent')->user();
        if ($parent->students()->where('student_id', $student->id)->exists()) {
            return back()->with('info', "{$student->full_name} is already connected to your account.");
        }

        $parent->students()->attach($student->id, [
            'relationship' => $data['relationship'] ?: $parent->relationship,
        ]);

        session(['parent_selected_student_id' => $student->id]);

        return redirect()->route('parent.dashboard', ['student_id' => $student->id])
            ->with('success', "Successfully connected {$student->full_name} to your account.");
    }
}
