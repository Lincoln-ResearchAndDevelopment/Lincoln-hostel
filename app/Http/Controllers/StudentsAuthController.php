<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

        $throttleKey = Str::lower($credentials['admission_number']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'admission_number' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.',
            ])->withInput();
        }

        // Normalize the input contact number: strip spaces, dashes, and handle +234/234 prefix
        $inputContact = preg_replace('/[\s\-\(\)]+/', '', $credentials['contact_number']);
        
        // Convert +234/234 prefix to local 0 prefix for consistent matching
        if (preg_match('/^\+?234/', $inputContact)) {
            $inputContact = '0' . preg_replace('/^\+?234/', '', $inputContact);
        }
        
        // Strip to pure digits for comparison
        $inputDigits = preg_replace('/\D/', '', $inputContact);

        // Attempt to find the student - first try exact match, then normalized match
        $student = Student::where('admission_number', $credentials['admission_number'])
                          ->where('contact_number', $credentials['contact_number'])
                          ->first();

        // If exact match failed, try normalized digit comparison
        if (!$student) {
            $student = Student::where('admission_number', $credentials['admission_number'])
                ->get()
                ->first(function ($s) use ($inputDigits) {
                    $dbDigits = preg_replace('/\D/', '', $s->contact_number);
                    // Also handle +234 vs 0 prefix in DB value
                    if (preg_match('/^234/', $dbDigits) && strlen($dbDigits) > 10) {
                        $dbDigits = '0' . substr($dbDigits, 3);
                    }
                    return $dbDigits === $inputDigits;
                });
        }

        if ($student) {
            RateLimiter::clear($throttleKey);
            // Login the student using the student guard
            Auth::guard('student')->login($student);

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            // Redirect to student dashboard
            return redirect()->intended(route('student.dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

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
