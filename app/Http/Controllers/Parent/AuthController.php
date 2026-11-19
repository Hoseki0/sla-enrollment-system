<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentUser;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('parent')->check()) {
            return redirect()->route('parent.dashboard');
        }
        return view('parent.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('parent')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('parent.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::guard('parent')->check()) {
            return redirect()->route('parent.dashboard');
        }
        return view('parent.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:120'],
            'email'             => ['required', 'email', 'max:150', 'unique:parents,email'],
            'contact_number'    => ['nullable', 'string', 'max:20'],
            'relationship'      => ['required', 'string', 'max:50'],
            'student_lrn'       => ['required', 'string', 'max:30'],
            'student_last_name' => ['required', 'string', 'max:100'],
            'password'          => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $student = Student::where('lrn', trim($data['student_lrn']))->first();

        if (!$student || strcasecmp(trim($student->last_name), trim($data['student_last_name'])) !== 0) {
            return back()
                ->withErrors(['student_lrn' => 'No matching student found with that LRN and Last Name.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $parent = ParentUser::create([
            'name'           => trim($data['name']),
            'email'          => strtolower(trim($data['email'])),
            'contact_number' => $data['contact_number'] ?? null,
            'relationship'   => $data['relationship'],
            'password'       => Hash::make($data['password']),
        ]);

        $parent->students()->attach($student->id, ['relationship' => $data['relationship']]);

        Auth::guard('parent')->login($parent);
        $request->session()->regenerate();

        return redirect()->route('parent.dashboard')
            ->with('success', "Welcome! Your account is now connected to {$student->full_name}.");
    }

    public function logout(Request $request)
    {
        Auth::guard('parent')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('parent.login')->with('success', 'You have been logged out.');
    }
}
