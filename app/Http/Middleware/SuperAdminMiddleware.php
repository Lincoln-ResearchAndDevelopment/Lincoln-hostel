<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SessionManagementService;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request with enterprise session validation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        // Resolve SessionManagementService from container
        $sessionService = app(SessionManagementService::class);
        
        // Check if super admin is authenticated with session validation
        if (!$sessionService->isAuthenticated('superadmin', $request)) {
            // Clean up any invalid session contexts
            $sessionService->cleanupSession($request);
            
            return redirect('/superadmin/login')->with('error', 'Please login to access the super admin panel.');
        }

        $superAdmin = Auth::guard('superadmin')->user();

        // Check if super admin account is active
        if (!$superAdmin->is_active) {
            // Use secure logout instead of direct Laravel logout
            $sessionService->secureLogout('superadmin', $request, true);
            
            return redirect('/superadmin/login')->with('error', 'Your account has been deactivated.');
        }

        // Check if account is locked
        if ($superAdmin->isLocked()) {
            return redirect('/superadmin/login')->with('error', 'Your account is temporarily locked due to multiple failed login attempts.');
        }

        // Check specific permission if required
        if ($permission && method_exists($superAdmin, 'hasPermission') && !$superAdmin->hasPermission($permission)) {
            return redirect('/superadmin/dashboard')->with('error', 'You do not have permission to access this feature.');
        }

        // Update last activity
        $superAdmin->update(['last_login_at' => now()]);

        return $next($request);
    }
}
