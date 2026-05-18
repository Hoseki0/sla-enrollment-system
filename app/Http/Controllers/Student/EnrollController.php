<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EnrollController extends Controller
{
    private array $shsTracks = [
        'Academic'      => ['STEM', 'ABM', 'HUMSS', 'GAS', 'TVL Integration'],
        'TVL'           => ['ICT', 'HE', 'Agri-Fishery', 'Industrial Arts'],
        'Sports'        => ['Sports Track'],
        'Arts & Design' => ['Arts & Design Track'],
    ];

    // ── Self Enrollment (New Students) ─────────────────────────
    public function showEnroll()
    {
        $sy          = SchoolYear::active();
        $schoolYears = SchoolYear::orderByDesc('id')->get();
        $shsTracks   = $this->shsTracks;
        return view('student.enroll', compact('sy', 'schoolYears', 'shsTracks'));
    }

    public function enroll(Request $request)
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
            'email'           => 'nullable|email',
            'password'        => 'required|string|min:6|confirmed',
            'track'           => 'required_if:level_type,SHS',
            'strand'          => 'required_if:level_type,SHS',
            'semester'        => 'required_if:level_type,SHS',
        ]);

        $student = Student::create([
            'student_id'            => Student::generateStudentId(),
            'lrn'                   => $request->lrn ?: null,
            'last_name'             => $request->last_name,
            'first_name'            => $request->first_name,
            'middle_name'           => $request->middle_name ?: null,
            'suffix'                => $request->suffix ?: null,
            'birthdate'             => $request->birthdate,
            'gender'                => $request->gender,
            'civil_status'          => $request->civil_status ?? 'Single',
            'religion'              => $request->religion ?: null,
            'nationality'           => $request->nationality ?? 'Filipino',
            'email'                 => $request->email ?: null,
            'contact_number'        => $request->contact_number ?: null,
            'address'               => $request->address,
            'barangay'              => $request->barangay ?: null,
            'municipality'          => $request->municipality ?: null,
            'province'              => $request->province ?: null,
            'zip_code'              => $request->zip_code ?: null,
            'father_name'           => $request->father_name ?: null,
            'father_occupation'     => $request->father_occupation ?: null,
            'father_contact'        => $request->father_contact ?: null,
            'mother_name'           => $request->mother_name ?: null,
            'mother_occupation'     => $request->mother_occupation ?: null,
            'mother_contact'        => $request->mother_contact ?: null,
            'guardian_name'         => $request->guardian_name ?: null,
            'guardian_relationship' => $request->guardian_relationship ?: null,
            'guardian_contact'      => $request->guardian_contact ?: null,
            'last_school_attended'  => $request->last_school_attended ?: null,
            'last_school_address'   => $request->last_school_address ?: null,
            'school_type'           => $request->school_type ?? 'Public',
            'password'              => Hash::make($request->password),
        ]);

        Enrollment::create([
            'enrollment_no'   => Enrollment::generateEnrollmentNo($request->school_year_id),
            'student_id'      => $student->id,
            'school_year_id'  => $request->school_year_id,
            'section_id'      => $request->section_id ?: null,
            'level_type'      => $request->level_type,
            'grade_level'     => $request->grade_level,
            'track'           => $request->track ?: null,
            'strand'          => $request->strand ?: null,
            'semester'        => $request->semester ?: null,
            'enrollment_type' => $request->enrollment_type,
            'status'          => 'Pending',
            'enrollment_date' => now()->toDateString(),
        ]);

        return redirect()->route('student.login')
            ->with('enroll_success', "Enrollment submitted! Your Student ID is: {$student->student_id}. Your application is pending review. You may log in once approved.");
    }

    // ── Create Account (Existing Students) ─────────────────────
    public function showRegister()
    {
        return view('student.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|exists:students,student_id',
            'last_name'  => 'required|string',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        $student = Student::where('student_id', $request->student_id)
            ->whereRaw('LOWER(last_name) = ?', [strtolower($request->last_name)])
            ->first();

        if (!$student) {
            return back()->withErrors(['student_id' => 'Student ID and Last Name do not match our records.'])->withInput($request->except('password', 'password_confirmation'));
        }

        $student->update(['password' => Hash::make($request->password)]);

        return redirect()->route('student.login')
            ->with('register_success', 'Account created! You can now log in with your Student ID and new password.');
    }

    // ── AJAX: sections ──────────────────────────────────────────
    public function sections(Request $request)
    {
        $sections = Section::where('school_year_id', $request->sy)
            ->where('grade_level', $request->grade)
            ->when($request->strand, fn ($q) => $q->where('strand', $request->strand))
            ->get(['id', 'section_name', 'strand']);
        return response()->json($sections);
    }
}
