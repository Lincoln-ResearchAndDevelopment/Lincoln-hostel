<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SessionManagementService;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request with enterprise session validation.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Resolve SessionManagementService from container
        $sessionService = app(SessionManagementService::class);
        
        // Check if user is authenticated in web guard (regular admin) with session validation
        if ($sessionService->isAuthenticated('web', $request)) {
            $user = Auth::guard('web')->user();
            if ($user && $user->isAdmin()) {
                return $next($request);
            }
        }

        // Check if super admin is trying to access admin routes (prevent collision)
        if ($sessionService->isAuthenticated('superadmin', $request)) {
            $superAdmin = Auth::guard('superadmin')->user();
            if ($superAdmin && $superAdmin->is_master) {
                // Master super admin can access admin routes if needed
                return $next($request);
            }
        }

        // Clean up any invalid session contexts
        $sessionService->cleanupSession($request);

        // If neither condition is met, redirect to appropriate login
        if ($request->is('superadmin*')) {
            return redirect()->route('superadmin.login')->with('error', 'Super Admin access required.');
        }

        return redirect()->route('login')->with('error', 'Access denied. Admin privileges required.');
    }
}
