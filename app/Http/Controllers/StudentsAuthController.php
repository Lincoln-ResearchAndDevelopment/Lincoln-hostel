<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentsAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('student.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'admission_number' => 'required|string',
            'contact_number' => 'required|string',
        ]);

        // Custom authentication logic
        $student = \App\Models\Student::where('admission_number', $credentials['admission_number'])
                                      ->where('contact_number', $credentials['contact_number'])
                                      ->first();

        if ($student) {
            Auth::guard('student')->login($student);
            $request->session()->regenerate();
            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors([
            'admission_number' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('student.login');
    }

}