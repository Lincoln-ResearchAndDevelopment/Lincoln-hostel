<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SessionManagementService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';
    protected $maxAttempts = 5;
    protected $decayMinutes = 1;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application using secure session management.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        $guardName = $this->guard();
        
        // First, attempt authentication with Laravel's built-in method
        if (Auth::guard($guardName)->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard($guardName)->user();
            
            // Resolve SessionManagementService from container
            $sessionService = app(SessionManagementService::class);
            
            // Use our secure session management service
            $loginSuccess = $sessionService->secureLogin(
                $guardName,
                $user,
                $request,
                $request->boolean('remember')
            );
            
            if ($loginSuccess) {
                Log::info('Admin login successful', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                ]);
                
                return true;
            } else {
                // If secure login failed, logout from Laravel auth
                Auth::guard($guardName)->logout();
                
                Log::warning('Admin secure login failed', [
                    'email' => $credentials['email'] ?? 'unknown',
                    'ip' => $request->ip(),
                ]);
            }
        }
        
        return false;
    }

    /**
     * Enterprise-grade logout that only affects the admin session
     */
    public function logout(Request $request)
    {
        // Resolve SessionManagementService from container
        $sessionService = app(SessionManagementService::class);
        
        // Only logout the web guard (admin) - DO NOT affect other guards
        $logoutSuccess = $sessionService->secureLogout('web', $request, true);
        
        if ($logoutSuccess) {
            Log::info('Admin logout successful', [
                'ip' => $request->ip(),
                'session_id' => $request->session()->getId(),
            ]);
        } else {
            Log::warning('Admin logout had issues', [
                'ip' => $request->ip(),
            ]);
        }

        return redirect('/home')->with('status', 'You have been logged out successfully.');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return string
     */
    protected function guard()
    {
        return 'web';
    }
}
