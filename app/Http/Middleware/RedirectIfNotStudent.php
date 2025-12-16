<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotStudent
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('student')->check()) {
            // Make sure this route exists exactly as named
            return redirect()->route('student.login');
        }

        return $next($request);
    }
}
