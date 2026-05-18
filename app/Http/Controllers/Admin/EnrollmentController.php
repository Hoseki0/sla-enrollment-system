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
        $schoolYears = SchoolYear::orderByDesc('id')->get();
        $sy          = SchoolYear::active();
        return view('admin.enrollments.create', [
            'schoolYears' => $schoolYears,
            'sy'          => $sy,
            'shsTracks'   => $this->shsTracks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'last_name'       => 'required|string|max:100',
            'first_name'      => 'required|string|max:100',
            'birthdate'       => 'required|date',
            'gender'          => 'required|in:Male,Female',
            'address'         => 'required|string',
            'level_type'      => 'required|in:JHS,SHS',
            'grade_level'     => 'required|string',
            'enrollment_type' => 'required|in:New,Old,Transferee,Returnee',
            'school_year_id'  => 'required|exists:school_years,id',
            'lrn'             => 'nullable|digits:12|unique:students,lrn',
            'track'           => 'required_if:level_type,SHS',
            'strand'          => 'required_if:level_type,SHS',
            'semester'        => 'required_if:level_type,SHS',
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
                // Default portal password: LRN if provided, otherwise last name (lowercase)
                'password'             => Hash::make($request->lrn ?: strtolower($request->last_name)),
            ]);

            Enrollment::create([
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
                'status'          => 'Pending',
                'enrollment_date' => now()->toDateString(),
            ]);
        });

        $defaultPw = $request->lrn ?: strtolower($request->last_name);
        return redirect()->route('admin.enrollments.index')
            ->with('success', "Student enrolled. Portal login: Student ID = auto-generated | Password = {$defaultPw}");
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
