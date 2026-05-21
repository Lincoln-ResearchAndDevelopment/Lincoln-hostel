<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SessionManagementService;

class RedirectIfNotStudent
{
    public function handle(Request $request, Closure $next)
    {
        // Resolve SessionManagementService from container
        $sessionService = app(SessionManagementService::class);
        
        // Use enterprise session validation for student guard
        if (!$sessionService->isAuthenticated('student', $request)) {
            // Clean up any invalid session contexts
            $sessionService->cleanupSession($request);
            
            return redirect()->route('student.login')->with('error', 'Please log in to access the student portal.');
        }

        return $next($request);
    }
}
