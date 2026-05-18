<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        return view('student.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'password'   => 'required|string',
        ]);

        $credentials = [
            'student_id' => $request->student_id,
            'password'   => $request->password,
        ];

        if (Auth::guard('student')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('student.dashboard');
        }

        return back()->withErrors(['student_id' => 'Invalid Student ID or password.'])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('student.login');
    }
}
