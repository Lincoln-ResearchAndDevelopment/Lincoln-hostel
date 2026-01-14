<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class StudentsAuthController extends Controller
{
    /**
     * Show the student login form
     */
    public function showLoginForm()
    {
        return view('student.auth.login'); // Make sure this blade exists
    }

    /**
     * Handle student login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'admission_number' => 'required|string',
            'contact_number' => 'required|string',
        ]);

        // Attempt to find the student with provided credentials
        $student = Student::where('admission_number', $credentials['admission_number'])
                          ->where('contact_number', $credentials['contact_number'])
                          ->first();

        if ($student) {
            // Login the student using the student guard
            Auth::guard('student')->login($student);

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            // Redirect to student dashboard
            return redirect()->intended(route('student.dashboard'));
        }

        // Invalid credentials
        return back()->withErrors([
            'admission_number' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Logout the student
     */
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();

        // Invalidate the session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
