<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\Student;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Complaint;
use App\Models\Visitor;
use App\Models\Announcement;
use App\Models\SystemAlert;
use App\Services\SessionManagementService;

class SuperAdminController extends Controller
{
    /**
     * Show the super admin login form
     */
    public function showLoginForm()
    {
        return view('superadmin.auth.login');
    }

    /**
     * Handle super admin login with enterprise session management
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.',
            ])->withInput();
        }

        $superAdmin = SuperAdmin::where('email', $credentials['email'])->first();

        if (!$superAdmin) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        // Check if account is active
        if (!$superAdmin->is_active) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['email' => 'Your account has been deactivated'])->withInput();
        }

        // Check if account is locked
        if ($superAdmin->isLocked()) {
            return back()->withErrors(['email' => 'Account is temporarily locked due to multiple failed attempts'])->withInput();
        }

        if (Auth::guard('superadmin')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->remember)) {
            RateLimiter::clear($throttleKey);
            
            // Use enterprise session management for secure login
            $sessionService = app(SessionManagementService::class);
            $loginSuccess = $sessionService->secureLogin('superadmin', $superAdmin, $request, $request->boolean('remember'));
            
            if ($loginSuccess) {
                $superAdmin->resetLoginAttempts();
                
                Log::info('SuperAdmin login successful', [
                    'superadmin_id' => $superAdmin->id,
                    'email' => $superAdmin->email,
                    'ip' => $request->ip(),
                ]);
                
                return redirect()->route('superadmin.dashboard');
            } else {
                // If secure login failed, logout from Laravel auth
                Auth::guard('superadmin')->logout();
                
                Log::error('SuperAdmin secure login failed', [
                    'superadmin_id' => $superAdmin->id,
                    'email' => $superAdmin->email,
                    'ip' => $request->ip(),
                ]);
                
                return back()->withErrors(['email' => 'Login failed due to a system error. Please try again.'])->withInput();
            }
        }

        // Increment failed attempts
        RateLimiter::hit($throttleKey, 60);
        $superAdmin->incrementLoginAttempts();

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    /**
     * Enterprise-grade logout that only affects the superadmin session
     */
    public function logout(Request $request)
    {
        // Only logout the superadmin guard - DO NOT affect other guards
        $sessionService = app(SessionManagementService::class);
        $logoutSuccess = $sessionService->secureLogout('superadmin', $request, true);
        
        if ($logoutSuccess) {
            Log::info('SuperAdmin logout successful', [
                'ip' => $request->ip(),
                'session_id' => $request->session()->getId(),
            ]);
        } else {
            Log::warning('SuperAdmin logout had issues', [
                'ip' => $request->ip(),
            ]);
        }

        return redirect()->route('superadmin.login')->with('status', 'You have been logged out successfully.');
    }

    /**
     * Show super admin dashboard
     */
    public function dashboard()
    {
        $superAdmin = Auth::guard('superadmin')->user();

        // Check if super admin is authenticated
        if (!$superAdmin) {
            return redirect('/superadmin/login')->with('error', 'Please login to access the dashboard.');
        }

        $data = [
            // System Overview
            'total_users' => User::count(),
            'total_students' => Student::where('status', 'active')->count(),
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->whereRaw('occupied < capacity')->count(),

            // Financial Overview
            'total_payments' => Payment::count(),
            'monthly_revenue' => Payment::whereMonth('created_at', now()->month)->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),

            // Activity Overview
            'total_complaints' => Complaint::count(),
            'pending_complaints' => Complaint::whereIn('status', ['submitted', 'in progress'])->count(),
            'total_visitors' => Visitor::count(),
            'current_visitors' => Visitor::whereNull('check_out_time')->count(),

            // Recent Activity
            'recent_payments' => Payment::with('student')->latest()->take(5)->get(),
            'recent_complaints' => Complaint::with('student')->latest()->take(5)->get(),
            'recent_announcements' => Announcement::latest()->take(5)->get(),
            'system_alerts' => SystemAlert::latest()->take(5)->get(),

            // Super Admin Info
            'super_admin' => $superAdmin,
            'is_master_admin' => $superAdmin->is_master,
        ];

        return view('superadmin.dashboard', $data);
    }

    /**
     * Show super admin profile
     */
    public function profile()
    {
        $superAdmin = Auth::guard('superadmin')->user();
        return view('superadmin.profile.show', compact('superAdmin'));
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $superAdmin = Auth::guard('superadmin')->user();
        return view('superadmin.profile.edit', compact('superAdmin'));
    }

    /**
     * Update super admin profile
     */
    public function updateProfile(Request $request)
    {
        $superAdmin = Auth::guard('superadmin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:super_admins,email,' . $superAdmin->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
        ]);

        $superAdmin->update($request->only(['name', 'email', 'phone', 'bio']));

        return redirect()->route('superadmin.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Show change password form
     */
    public function changePasswordForm()
    {
        return view('superadmin.profile.change-password');
    }

    /**
     * Update super admin password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $superAdmin = Auth::guard('superadmin')->user();

        if (!Hash::check($request->current_password, $superAdmin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $superAdmin->update(['password' => Hash::make($request->password)]);

        return redirect()->route('superadmin.profile')->with('success', 'Password changed successfully');
    }
}
