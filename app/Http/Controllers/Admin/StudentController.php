<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::withCount('enrollments')->latest()->paginate(25);
        return view('admin.students.index', compact('students'));
    }

    public function show(Student $student)
    {
        $student->load(['enrollments.schoolYear', 'enrollments.section']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'last_name'  => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'birthdate'  => 'required|date',
            'gender'     => 'required|in:Male,Female',
            'address'    => 'required|string',
            'lrn'        => 'nullable|digits:12|unique:students,lrn,' . $student->id,
        ]);

        $student->update($request->except(['_token', '_method', 'student_id']));
        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student record updated.');
    }
}
