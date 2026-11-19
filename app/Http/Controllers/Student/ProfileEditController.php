<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileEditController extends Controller
{
    public function edit()
    {
        $student = Auth::guard('student')->user();
        return view('student.profile-edit', compact('student'));
    }

    public function update(Request $request)
    {
        $student = Auth::guard('student')->user();

        $data = $request->validate([
            'email'                 => 'nullable|email|max:150|unique:students,email,' . $student->id,
            'contact_number'        => 'nullable|string|max:20',
            'address'               => 'nullable|string|max:500',
            'barangay'              => 'nullable|string|max:100',
            'municipality'          => 'nullable|string|max:100',
            'province'              => 'nullable|string|max:100',
            'zip_code'              => 'nullable|string|max:10',
            'guardian_name'         => 'nullable|string|max:150',
            'guardian_relationship' => 'nullable|string|max:100',
            'guardian_contact'      => 'nullable|string|max:20',
        ]);

        $student->update($data);

        ActivityLog::recordStudent('student.profile_updated', 'Updated personal contact information.');

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $student = Auth::guard('student')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $student->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $student->update(['password' => Hash::make($request->password)]);

        ActivityLog::recordStudent('student.password_changed', 'Changed account password.');

        return back()->with('success', 'Password changed successfully.');
    }

    public function certificate()
    {
        $student    = Auth::guard('student')->user();
        $enrollment = $student->latestEnrollment?->load(['schoolYear', 'section']);

        if (!$enrollment || $enrollment->status !== 'Enrolled') {
            return back()->with('error', 'You must be officially enrolled to download your certificate.');
        }

        return view('student.certificate', compact('student', 'enrollment'));
    }
}
