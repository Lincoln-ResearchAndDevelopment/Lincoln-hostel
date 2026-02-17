<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        (function() {
        // Get saved theme from localStorage or detect system preference
        const savedTheme = localStorage.getItem('theme');
        let theme = 'light'; // default
        
        if (savedTheme) {
            theme = savedTheme;
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            theme = 'dark';
        }
        
        // Apply theme immediately to prevent flash
        document.documentElement.setAttribute('data-theme', theme);
        document.body = document.body || document.createElement('body');
        document.body.setAttribute('data-theme', theme);
        
        // Also set a class on html for immediate CSS targeting
        document.documentElement.className = theme + '-theme';
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Admin Login CSS File -->
    <link href="{{ asset('assets/css/adminlogin.css') }}" rel="stylesheet">
</head>
<body>

<div class="container-fluid login-container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-xl-8 col-lg-10 col-md-12">
            <div class="card shadow-lg login-card">
                <div class="row g-0">
                    <!-- Left Side - Shield Icon Section -->
                    <div class="col-md-6 d-none d-md-flex shield-section">
                        <div class="shield-container">
                            <div class="shield-icon-wrapper">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="shield-content">
                                <h2>Admin Portal</h2>
                                <p>Secure access to manage hostel operations</p>
                                <div class="security-features">
                                    <div class="feature-item">
                                        <i class="fas fa-shield-alt"></i>
                                        <span>Enhanced Security</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-lock"></i>
                                        <span>Encrypted Connection</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-user-check"></i>
                                        <span>Role-based Access</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side - Login Form -->
                    <div class="col-md-6 form-section">
                        <div class="card-body p-5">
                            <!-- Mobile Logo -->
                            <div class="d-md-none text-center mb-4">
                                <a href="/">
                                    <img src="{{ asset('assets/img/lincoln-logo.png') }}" alt="Lincoln Logo" class="mobile-logo">
                                </a>
                            </div>

                            <!-- Desktop Logo -->
                            <div class="d-none d-md-block text-center mb-4">
                                <a href="/">
                                    <img src="{{ asset('assets/img/lincoln-logo.png') }}" alt="Lincoln Logo" class="desktop-logo">
                                </a>
                            </div>

                            <div class="text-center mb-4">
                                <h3 class="login-title">
                                    <i class="fas fa-user-shield me-2 d-md-none"></i>
                                    {{ __('Admin Login') }}
                                </h3>
                                <p class="text-muted">Enter your credentials to access the admin panel</p>
                            </div>

                            <form method="POST" action="{{ route('login') }}" id="loginForm">
                                @csrf

                                <div class="mb-4">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>{{ __('Email Address') }}
                                    </label>
                                    <input id="email" type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email') }}" required autofocus
                                           placeholder="admin@lincoln.edu.ng">

                                    @error('email')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>{{ __('Password') }}
                                    </label>
                                    <input id="password" type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" required
                                           placeholder="Enter your password">

                                    @error('password')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2 mb-3">
                                    <button type="submit" class="btn btn-lg btn-danger login-btn" id="loginBtn">
                                        <span class="btn-content">
                                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('Login') }}
                                        </span>
                                        <span class="btn-loading d-none">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Logging in...
                                        </span>
                                    </button>
                                </div>

                                @if (Route::has('password.request'))
                                    <div class="text-center">
                                        <a href="{{ route('password.request') }}" class="forgot-password">
                                            <i class="fas fa-key me-1"></i>{{ __('Forgot Your Password?') }}
                                        </a>
                                    </div>
                                @endif
                            </form>

                            <div class="mt-4 pt-3 border-top text-center">
                                <p class="mb-0">Are you a student? 
                                    <a href="{{ route('student.login') }}" class="student-login-link">
                                        <i class="fas fa-graduation-cap me-1"></i>Login here
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading animation to login button
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const btnContent = loginBtn.querySelector('.btn-content');
        const btnLoading = loginBtn.querySelector('.btn-loading');
        
        loginForm.addEventListener('submit', function(e) {
            // Show loading state
            btnContent.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            loginBtn.disabled = true;
        });

        // Theme transition handling
        document.body.classList.add('theme-loaded');
    });
</script>

</body>
</html>