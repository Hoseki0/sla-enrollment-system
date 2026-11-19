<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $sy   = SchoolYear::where('is_active', true)->first();
        $date = $request->query('date', now()->toDateString());
        $grade = $request->query('grade_level');

        $enrollments = Enrollment::with(['student', 'section'])
            ->when($sy, fn ($q) => $q->where('school_year_id', $sy->id))
            ->where('status', 'Enrolled')
            ->when($grade, fn ($q) => $q->where('grade_level', $grade))
            ->orderBy('grade_level')
            ->get();

        $existing = Attendance::whereDate('attendance_date', $date)
            ->whereIn('student_id', $enrollments->pluck('student_id'))
            ->get()
            ->keyBy('student_id');

        $grades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

        return view('admin.attendance.index', compact('sy', 'date', 'grade', 'grades', 'enrollments', 'existing'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'attendance_date' => ['required', 'date'],
            'records'         => ['required', 'array'],
            'records.*.student_id'    => ['required', 'exists:students,id'],
            'records.*.enrollment_id' => ['nullable', 'exists:enrollments,id'],
            'records.*.status'        => ['required', 'in:Present,Absent,Late,Excused'],
            'records.*.time_in'       => ['nullable', 'string', 'max:10'],
            'records.*.remarks'       => ['nullable', 'string', 'max:255'],
        ]);

        $sy = SchoolYear::where('is_active', true)->firstOrFail();
        $adminId = auth('admin')->id();

        foreach ($data['records'] as $rec) {
            Attendance::updateOrCreate(
                [
                    'student_id'      => $rec['student_id'],
                    'attendance_date' => $data['attendance_date'],
                ],
                [
                    'enrollment_id'  => $rec['enrollment_id'] ?? null,
                    'school_year_id' => $sy->id,
                    'status'         => $rec['status'],
                    'time_in'        => $rec['time_in'] ?: null,
                    'remarks'        => $rec['remarks'] ?: null,
                    'recorded_by'    => $adminId,
                ]
            );
        }

        return back()->with('success', 'Attendance sheet saved for ' . $data['attendance_date'] . '.');
    }
}
