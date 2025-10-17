<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        // Check if super admin is authenticated
        if (!Auth::guard('superadmin')->check()) {
            return redirect('/superadmin/login')->with('error', 'Please login to access the super admin panel.');
        }

        $superAdmin = Auth::guard('superadmin')->user();

        // Check if super admin account is active
        if (!$superAdmin->is_active) {
            Auth::guard('superadmin')->logout();
            return redirect('/superadmin/login')->with('error', 'Your account has been deactivated.');
        }

        // Check if account is locked
        if ($superAdmin->isLocked()) {
            return redirect('/superadmin/login')->with('error', 'Your account is temporarily locked due to multiple failed login attempts.');
        }

        // Check specific permission if required
        if ($permission && !$superAdmin->hasPermission($permission)) {
            return redirect('/superadmin/dashboard')->with('error', 'You do not have permission to access this feature.');
        }

        // Update last activity
        $superAdmin->update(['last_login_at' => now()]);

        return $next($request);
    }
}
