<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    /**
     * Display a listing of all admin users
     */
    public function index()
    {
        $superAdmin = Auth::guard('superadmin')->user();

        // Get all admin users (users with admin role)
        $adminUsers = User::where('role', 'admin')
                         ->orWhere('is_admin', true)
                         ->with(['created_by_user'])
                         ->latest()
                         ->paginate(15);

        // Get statistics
        $stats = [
            'total_admins' => User::where('role', 'admin')->orWhere('is_admin', true)->count(),
            'active_admins' => User::where('role', 'admin')->orWhere('is_admin', true)->where('is_active', true)->count(),
            'inactive_admins' => User::where('role', 'admin')->orWhere('is_admin', true)->where('is_active', false)->count(),
        ];

        return view('superadmin.admin-management.index', compact('adminUsers', 'stats', 'superAdmin'));
    }

    /**
     * Show the form for creating a new admin user
     */
    public function create()
    {
        $superAdmin = Auth::guard('superadmin')->user();

        // Only master admin can create other admins
        if (!$superAdmin || !$superAdmin->is_master) {
            return redirect()->route('superadmin.admin-management.index')
                           ->with('error', 'Only the Master Super Admin can create admin accounts.');
        }

        return view('superadmin.admin-management.create', compact('superAdmin'));
    }

    /**
     * Store a newly created admin user
     */
    public function store(Request $request)
    {
        $currentSuperAdmin = Auth::guard('superadmin')->user();

        // Only master admin can create other admins
        if (!$currentSuperAdmin || !$currentSuperAdmin->is_master) {
            return redirect()->route('superadmin.admin-management.index')
                           ->with('error', 'Only the Master Super Admin can create admin accounts.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,staff',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        // Create the admin user
        $adminUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_admin' => true,
            'is_active' => $request->is_active ?? true,
            'permissions' => $request->permissions ?? User::getDefaultAdminPermissions(),
            'created_by' => Auth::guard('superadmin')->id(),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('superadmin.admin-management.index')
                         ->with('success', 'Admin user created successfully!');
    }

    /**
     * Display the specified admin user
     */
    public function show(User $adminUser)
    {
        $superAdmin = Auth::guard('superadmin')->user();

        // Ensure the user is actually an admin
        if (!$adminUser->is_admin && $adminUser->role !== 'admin') {
            return redirect()->route('superadmin.admin-management.index')
                           ->with('error', 'User is not an admin.');
        }

        return view('superadmin.admin-management.show', compact('adminUser', 'superAdmin'));
    }

    /**
     * Show the form for editing the specified admin user
     */
    public function edit(User $adminUser)
    {
        $superAdmin = Auth::guard('superadmin')->user();

        // Ensure the user is actually an admin
        if (!$adminUser->is_admin && $adminUser->role !== 'admin') {
            return redirect()->route('superadmin.admin-management.index')
                           ->with('error', 'User is not an admin.');
        }

        return view('superadmin.admin-management.edit', compact('adminUser', 'superAdmin'));
    }

    /**
     * Update the specified admin user
     */
    public function update(Request $request, User $adminUser)
    {
        // Ensure the user is actually an admin
        if (!$adminUser->is_admin && $adminUser->role !== 'admin') {
            return redirect()->route('superadmin.admin-management.index')
                           ->with('error', 'User is not an admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($adminUser->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,manager,staff',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        // Update admin user
        $adminUser->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => $request->is_active ?? $adminUser->is_active,
            'permissions' => $request->permissions ?? $adminUser->permissions,
            'updated_by' => Auth::guard('superadmin')->id(),
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $adminUser->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('superadmin.admin-management.index')
                         ->with('success', 'Admin user updated successfully!');
    }

    /**
     * Remove/Deactivate the specified admin user
     */
    public function destroy(User $adminUser)
    {
        // Ensure the user is actually an admin
        if (!$adminUser->is_admin && $adminUser->role !== 'admin') {
            return redirect()->route('superadmin.admin-management.index')
                           ->with('error', 'User is not an admin.');
        }

        // Prevent deleting the current super admin (if somehow reached)
        $currentSuperAdmin = Auth::guard('superadmin')->user();
        if ($currentSuperAdmin && $currentSuperAdmin->is_master) {
            // Master admin can delete any admin
            $adminUser->update([
                'is_active' => false,
                'deactivated_by' => $currentSuperAdmin->id,
                'deactivated_at' => now(),
            ]);

            return redirect()->route('superadmin.admin-management.index')
                           ->with('success', 'Admin user deactivated successfully!');
        }

        return redirect()->route('superadmin.admin-management.index')
                         ->with('error', 'Unauthorized action.');
    }

    /**
     * Activate an admin user
     */
    public function activate(User $adminUser)
    {
        // Ensure the user is actually an admin
        if (!$adminUser->is_admin && $adminUser->role !== 'admin') {
            return redirect()->route('superadmin.admin-management.index')
                           ->with('error', 'User is not an admin.');
        }

        $adminUser->update([
            'is_active' => true,
            'activated_by' => Auth::guard('superadmin')->id(),
            'activated_at' => now(),
        ]);

        return redirect()->route('superadmin.admin-management.index')
                         ->with('success', 'Admin user activated successfully!');
    }

    /**
     * Get admin management statistics for AJAX
     */
    public function getStats()
    {
        $stats = [
            'total_admins' => User::where('role', 'admin')->orWhere('is_admin', true)->count(),
            'active_admins' => User::where('role', 'admin')->orWhere('is_admin', true)->where('is_active', true)->count(),
            'inactive_admins' => User::where('role', 'admin')->orWhere('is_admin', true)->where('is_active', false)->count(),
            'recent_admins' => User::where('role', 'admin')->orWhere('is_admin', true)->latest()->take(5)->get(),
        ];

        return response()->json($stats);
    }
}
