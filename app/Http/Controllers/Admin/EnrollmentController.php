<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EnrollmentController extends Controller
{
    private array $shsTracks = [
        'Academic' => ['STEM', 'ABM', 'HUMSS', 'GAS', 'TVL Integration'],
        'TVL'      => ['ICT', 'HE', 'Agri-Fishery', 'Industrial Arts'],
        'Sports'   => ['Sports Track'],
        'Arts & Design' => ['Arts & Design Track'],
    ];

    public function index(Request $request)
    {
        $sy   = SchoolYear::active();
        $syId = $request->get('sy', $sy?->id ?? 0);

        $query = Enrollment::with(['student', 'section', 'schoolYear'])
            ->where('school_year_id', $syId);

        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('level'))   $query->where('level_type', $request->level);
        if ($request->filled('grade'))   $query->where('grade_level', $request->grade);

        $enrollments = $query->latest()->paginate(25)->withQueryString();
        $schoolYears = SchoolYear::orderByDesc('id')->get();

        return view('admin.enrollments.index', compact('enrollments', 'schoolYears', 'sy', 'syId'));
    }

    public function create()
    {
        $schoolYears      = SchoolYear::orderByDesc('id')->get();
        $sy               = SchoolYear::active();
        $existingStudents = Student::orderBy('last_name')->orderBy('first_name')->get();
        return view('admin.enrollments.create', [
            'schoolYears'      => $schoolYears,
            'sy'               => $sy,
            'shsTracks'        => $this->shsTracks,
            'existingStudents' => $existingStudents,
        ]);
    }

    public function store(Request $request)
    {
        // ── Case 1: Enrolling an Existing Regular / Continuing Student ──
        if ($request->filled('existing_student_id')) {
            $request->validate([
                'existing_student_id' => 'required|exists:students,id',
                'school_year_id'      => 'required|exists:school_years,id',
                'level_type'          => 'required|in:JHS,SHS',
                'grade_level'         => 'required|string',
                'enrollment_type'     => 'required|in:New,Old,Transferee,Returnee',
                'section_id'          => 'nullable|exists:sections,id',
                'track'               => 'required_if:level_type,SHS|nullable|string',
                'strand'              => 'required_if:level_type,SHS|nullable|string',
                'semester'            => 'required_if:level_type,SHS|nullable|string',
            ]);

            $student = Student::findOrFail($request->existing_student_id);

            // Check if already enrolled in this school year
            $exists = Enrollment::where('student_id', $student->id)
                ->where('school_year_id', $request->school_year_id)
                ->first();

            if ($exists) {
                return back()->withInput()->with('error', "{$student->full_name} already has an enrollment record ({$exists->enrollment_no}) for this school year.");
            }

            $enrollment = Enrollment::create([
                'enrollment_no'   => Enrollment::generateEnrollmentNo($request->school_year_id),
                'student_id'      => $student->id,
                'school_year_id'  => $request->school_year_id,
                'section_id'      => $request->section_id ?: null,
                'encoded_by'      => Auth::guard('admin')->id(),
                'level_type'      => $request->level_type,
                'grade_level'     => $request->grade_level,
                'track'           => $request->track ?: null,
                'strand'          => $request->strand ?: null,
                'semester'        => $request->semester ?: null,
                'enrollment_type' => $request->enrollment_type,
                'status'          => 'Enrolled',
                'enrollment_date' => now()->toDateString(),
            ]);

            \App\Models\ActivityLog::recordAdmin('enrollment.created', "Enrolled regular student: {$student->full_name} ({$enrollment->grade_level})", $enrollment);

            return redirect()->route('admin.enrollments.index')
                ->with('success', "Regular student {$student->full_name} successfully enrolled in {$enrollment->grade_level} ({$enrollment->enrollment_no}).");
        }

        // ── Case 2: Enrolling a New Student or Transfer Student ──
        $request->validate([
            'last_name'            => 'required|string|max:100',
            'first_name'           => 'required|string|max:100',
            'birthdate'            => 'required|date',
            'gender'               => 'required|in:Male,Female',
            'address'              => 'required|string',
            'level_type'           => 'required|in:JHS,SHS',
            'grade_level'          => 'required|string',
            'enrollment_type'      => 'required|in:New,Old,Transferee,Returnee',
            'school_year_id'       => 'required|exists:school_years,id',
            'lrn'                  => 'nullable|digits:12|unique:students,lrn',
            'track'                => 'required_if:level_type,SHS|nullable|string',
            'strand'               => 'required_if:level_type,SHS|nullable|string',
            'semester'             => 'required_if:level_type,SHS|nullable|string',
            'last_school_attended' => 'required_if:enrollment_type,Transferee|nullable|string|max:200',
            'last_school_address'  => 'nullable|string|max:300',
            'school_type'          => 'nullable|in:Public,Private',
        ]);

        DB::transaction(function () use ($request) {
            $student = Student::create([
                'student_id'           => Student::generateStudentId(),
                'lrn'                  => $request->lrn ?: null,
                'last_name'            => $request->last_name,
                'first_name'           => $request->first_name,
                'middle_name'          => $request->middle_name ?: null,
                'suffix'               => $request->suffix ?: null,
                'birthdate'            => $request->birthdate,
                'gender'               => $request->gender,
                'civil_status'         => $request->civil_status ?? 'Single',
                'religion'             => $request->religion ?: null,
                'nationality'          => $request->nationality ?? 'Filipino',
                'email'                => $request->email ?: null,
                'contact_number'       => $request->contact_number ?: null,
                'address'              => $request->address,
                'barangay'             => $request->barangay ?: null,
                'municipality'         => $request->municipality ?: null,
                'province'             => $request->province ?: null,
                'zip_code'             => $request->zip_code ?: null,
                'father_name'          => $request->father_name ?: null,
                'father_occupation'    => $request->father_occupation ?: null,
                'father_contact'       => $request->father_contact ?: null,
                'mother_name'          => $request->mother_name ?: null,
                'mother_occupation'    => $request->mother_occupation ?: null,
                'mother_contact'       => $request->mother_contact ?: null,
                'guardian_name'        => $request->guardian_name ?: null,
                'guardian_relationship'=> $request->guardian_relationship ?: null,
                'guardian_contact'     => $request->guardian_contact ?: null,
                'last_school_attended' => $request->last_school_attended ?: null,
                'last_school_address'  => $request->last_school_address ?: null,
                'school_type'          => $request->school_type ?? 'Public',
                'password'             => Hash::make($request->lrn ?: strtolower($request->last_name)),
            ]);

            $enrollment = Enrollment::create([
                'enrollment_no'   => Enrollment::generateEnrollmentNo($request->school_year_id),
                'student_id'      => $student->id,
                'school_year_id'  => $request->school_year_id,
                'section_id'      => $request->section_id ?: null,
                'encoded_by'      => Auth::guard('admin')->id(),
                'level_type'      => $request->level_type,
                'grade_level'     => $request->grade_level,
                'track'           => $request->track ?: null,
                'strand'          => $request->strand ?: null,
                'semester'        => $request->semester ?: null,
                'enrollment_type' => $request->enrollment_type,
                'status'          => 'Enrolled',
                'enrollment_date' => now()->toDateString(),
            ]);

            \App\Models\ActivityLog::recordAdmin('enrollment.created', "Enrolled student: {$student->full_name} ({$enrollment->enrollment_type} - {$enrollment->grade_level})", $enrollment);
        });

        $typeLabel = $request->enrollment_type === 'Transferee' ? 'Transfer Student' : 'New Student';
        $defaultPw = $request->lrn ?: strtolower($request->last_name);
        return redirect()->route('admin.enrollments.index')
            ->with('success', "{$typeLabel} enrolled successfully! Portal password: {$defaultPw}");
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'section', 'schoolYear', 'encodedBy']);
        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function updateStatus(Request $request, Enrollment $enrollment)
    {
        $request->validate(['status' => 'required|in:Pending,Enrolled,Cancelled,Dropped']);
        $enrollment->update(['status' => $request->status]);
        return back()->with('success', 'Status updated to ' . $request->status . '.');
    }

    public function sections(Request $request)
    {
        $sections = Section::where('school_year_id', $request->sy)
            ->where('grade_level', $request->grade)
            ->when($request->strand, fn ($q) => $q->where('strand', $request->strand))
            ->get(['id', 'section_name', 'strand']);

        return response()->json($sections);
    }
}
