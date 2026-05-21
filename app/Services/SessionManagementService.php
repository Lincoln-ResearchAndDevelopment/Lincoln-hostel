<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * Enterprise-Grade Session Management Service
 * 
 * Provides isolated, secure session management for multiple user types
 * following enterprise standards used by banking systems and large-scale SaaS platforms.
 * 
 * Key Features:
 * - Guard-specific session isolation
 * - Secure session regeneration
 * - Session context separation
 * - Enterprise-grade logout handling
 * - Concurrent session support
 * - Session security auditing
 */
class SessionManagementService
{
    /**
     * Session context prefixes for different user types
     */
    const CONTEXT_ADMIN = 'admin';
    const CONTEXT_STUDENT = 'student';
    const CONTEXT_SUPERADMIN = 'superadmin';

    /**
     * Perform secure login for a specific guard with session isolation
     * 
     * @param string $guard Guard name (web, student, superadmin)
     * @param mixed $user User model instance
     * @param Request $request Current request
     * @param bool $remember Remember me option
     * @return bool Success status
     */
    public function secureLogin(string $guard, $user, Request $request, bool $remember = false): bool
    {
        try {
            // Step 1: Clear any existing authentication for this guard only
            if (Auth::guard($guard)->check()) {
                $this->secureLogout($guard, $request, false); // Don't invalidate entire session
            }

            // Step 2: Regenerate session ID for security (prevents session fixation)
            $request->session()->regenerate();

            // Step 3: Set session context for this user type
            $context = $this->getSessionContext($guard);
            $request->session()->put("auth_context_{$context}", [
                'guard' => $guard,
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'login_time' => now()->toISOString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_token' => Str::random(40),
            ]);

            // Step 4: Perform the actual authentication
            Auth::guard($guard)->login($user, $remember);

            // Step 4.5: Update database session columns for auditing (if database session driver is used)
            if (config('session.driver') === 'database') {
                try {
                    $request->session()->save(); // Force save to ensure the session row exists
                    DB::table('sessions')
                        ->where('id', $request->session()->getId())
                        ->update([
                            'guard_type' => $guard,
                            'auth_contexts' => json_encode([
                                'user_id' => $user->id,
                                'user_type' => get_class($user),
                                'login_time' => now()->toISOString(),
                            ]),
                            'expires_at' => now()->addMinutes(config('session.lifetime')),
                        ]);
                } catch (\Exception $e) {
                    Log::warning("Could not write to sessions table columns: " . $e->getMessage());
                }
            }

            // Step 5: Update user login tracking (if supported)
            if (method_exists($user, 'resetLoginAttempts')) {
                $user->resetLoginAttempts();
            }

            // Step 6: Log successful authentication
            Log::info("Secure login successful", [
                'guard' => $guard,
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'ip' => $request->ip(),
                'session_id' => $request->session()->getId(),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Secure login failed", [
                'guard' => $guard,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            
            return false;
        }
    }

    /**
     * Perform secure logout for a specific guard WITHOUT affecting other guards
     * 
     * @param string $guard Guard name to logout
     * @param Request $request Current request
     * @param bool $invalidateSession Whether to invalidate entire session (default: true)
     * @return bool Success status
     */
    public function secureLogout(string $guard, Request $request, bool $invalidateSession = true): bool
    {
        try {
            $context = $this->getSessionContext($guard);
            $sessionData = $request->session()->get("auth_context_{$context}");

            // Step 1: Log the logout attempt
            Log::info("Secure logout initiated", [
                'guard' => $guard,
                'session_data' => $sessionData,
                'ip' => $request->ip(),
                'session_id' => $request->session()->getId(),
            ]);

            // Step 2: Logout from the specific guard only
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }

            // Step 3: Clear session context for this guard only
            $request->session()->forget("auth_context_{$context}");

            // Step 3.5: Clear database session columns (if database session driver is used)
            if (config('session.driver') === 'database') {
                try {
                    $request->session()->save(); // Force save to ensure changes are written
                    DB::table('sessions')
                        ->where('id', $request->session()->getId())
                        ->update([
                            'guard_type' => null,
                            'auth_contexts' => null,
                        ]);
                } catch (\Exception $e) {
                    Log::warning("Could not clear sessions table columns: " . $e->getMessage());
                }
            }

            // Step 4: Handle session invalidation based on parameter
            if ($invalidateSession) {
                // Check if other guards are still authenticated
                $otherGuardsActive = $this->hasActiveGuards($request, $guard);
                
                if (!$otherGuardsActive) {
                    // Safe to invalidate entire session - no other users logged in
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    Log::info("Session fully invalidated - no other active guards", [
                        'guard' => $guard,
                        'ip' => $request->ip(),
                    ]);
                } else {
                    // Other guards active - only regenerate token for security
                    $request->session()->regenerateToken();
                    
                    Log::info("Session token regenerated - other guards still active", [
                        'guard' => $guard,
                        'active_guards' => $this->getActiveGuards($request),
                        'ip' => $request->ip(),
                    ]);
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Secure logout failed", [
                'guard' => $guard,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            
            return false;
        }
    }

    /**
     * Check if user is authenticated in a specific guard with session validation
     * 
     * @param string $guard Guard name
     * @param Request $request Current request
     * @return bool Authentication status
     */
    public function isAuthenticated(string $guard, Request $request): bool
    {
        // Check Laravel's auth guard
        if (!Auth::guard($guard)->check()) {
            return false;
        }

        // Validate session context
        $context = $this->getSessionContext($guard);
        $sessionData = $request->session()->get("auth_context_{$context}");

        if (!$sessionData) {
            // Session context missing - force logout for security
            Auth::guard($guard)->logout();
            return false;
        }

        // Validate session integrity
        $user = Auth::guard($guard)->user();
        if (!$user || $sessionData['user_id'] != $user->id) {
            // Session mismatch - force logout
            Auth::guard($guard)->logout();
            $request->session()->forget("auth_context_{$context}");
            return false;
        }

        return true;
    }

    /**
     * Get session context prefix for a guard
     * 
     * @param string $guard Guard name
     * @return string Context prefix
     */
    private function getSessionContext(string $guard): string
    {
        return match($guard) {
            'web' => self::CONTEXT_ADMIN,
            'student' => self::CONTEXT_STUDENT,
            'superadmin' => self::CONTEXT_SUPERADMIN,
            default => $guard,
        };
    }

    /**
     * Check if other guards are still active (excluding the specified guard)
     * 
     * @param Request $request Current request
     * @param string $excludeGuard Guard to exclude from check
     * @return bool Whether other guards are active
     */
    private function hasActiveGuards(Request $request, string $excludeGuard): bool
    {
        $guards = ['web', 'student', 'superadmin'];
        
        foreach ($guards as $guard) {
            if ($guard === $excludeGuard) {
                continue;
            }
            
            if (Auth::guard($guard)->check()) {
                $context = $this->getSessionContext($guard);
                $sessionData = $request->session()->get("auth_context_{$context}");
                
                if ($sessionData) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Get list of currently active guards
     * 
     * @param Request $request Current request
     * @return array Active guard names
     */
    private function getActiveGuards(Request $request): array
    {
        $activeGuards = [];
        $guards = ['web', 'student', 'superadmin'];
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $context = $this->getSessionContext($guard);
                $sessionData = $request->session()->get("auth_context_{$context}");
                
                if ($sessionData) {
                    $activeGuards[] = $guard;
                }
            }
        }
        
        return $activeGuards;
    }

    /**
     * Get session information for debugging/monitoring
     * 
     * @param Request $request Current request
     * @return array Session information
     */
    public function getSessionInfo(Request $request): array
    {
        return [
            'session_id' => $request->session()->getId(),
            'active_guards' => $this->getActiveGuards($request),
            'session_contexts' => [
                'admin' => $request->session()->get('auth_context_admin'),
                'student' => $request->session()->get('auth_context_student'),
                'superadmin' => $request->session()->get('auth_context_superadmin'),
            ],
            'csrf_token' => $request->session()->token(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
    }

    /**
     * Clean up expired or invalid sessions
     * 
     * @param Request $request Current request
     * @return int Number of contexts cleaned
     */
    public function cleanupSession(Request $request): int
    {
        $cleaned = 0;
        $contexts = [self::CONTEXT_ADMIN, self::CONTEXT_STUDENT, self::CONTEXT_SUPERADMIN];
        
        foreach ($contexts as $context) {
            $sessionData = $request->session()->get("auth_context_{$context}");
            
            if ($sessionData) {
                $guard = $sessionData['guard'];
                
                // Check if guard is still authenticated
                if (!Auth::guard($guard)->check()) {
                    $request->session()->forget("auth_context_{$context}");
                    $cleaned++;
                    
                    Log::info("Cleaned up orphaned session context", [
                        'context' => $context,
                        'guard' => $guard,
                    ]);
                }
            }
        }
        
        return $cleaned;
    }
}