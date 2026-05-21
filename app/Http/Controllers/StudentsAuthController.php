<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Services\SessionManagementService;

class StudentsAuthController extends Controller
{
    /**
     * Show the student login form
     */
    public function showLoginForm()
    {
        return view('student.auth.login');
    }

    /**
     * Handle student login with enterprise-grade session management
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
            
            // Resolve SessionManagementService from container
            $sessionService = app(SessionManagementService::class);
            
            // Use enterprise session management for secure login
            $loginSuccess = $sessionService->secureLogin('student', $student, $request);
            
            if ($loginSuccess) {
                Log::info('Student login successful', [
                    'student_id' => $student->id,
                    'admission_number' => $student->admission_number,
                    'ip' => $request->ip(),
                ]);
                
                // Redirect to student dashboard
                return redirect()->intended(route('student.dashboard'));
            } else {
                Log::error('Student secure login failed', [
                    'student_id' => $student->id,
                    'admission_number' => $student->admission_number,
                    'ip' => $request->ip(),
                ]);
                
                return back()->withErrors([
                    'admission_number' => 'Login failed due to a system error. Please try again.',
                ])->withInput();
            }
        }

        RateLimiter::hit($throttleKey, 60);

        // Invalid credentials
        return back()->withErrors([
            'admission_number' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Enterprise-grade logout that only affects the student session
     */
    public function logout(Request $request)
    {
        // Resolve SessionManagementService from container
        $sessionService = app(SessionManagementService::class);
        
        // Only logout the student guard - DO NOT affect other guards
        $logoutSuccess = $sessionService->secureLogout('student', $request, true);
        
        if ($logoutSuccess) {
            Log::info('Student logout successful', [
                'ip' => $request->ip(),
                'session_id' => $request->session()->getId(),
            ]);
        } else {
            Log::warning('Student logout had issues', [
                'ip' => $request->ip(),
            ]);
        }

        return redirect()->route('student.login')->with('status', 'You have been logged out successfully.');
    }
}
