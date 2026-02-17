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
    <title>Student Login</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Student Login CSS File -->
    <link href="{{ asset('assets/css/studentlogin.css') }}" rel="stylesheet">
</head>
<body>

<div class="container-fluid login-container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-xl-8 col-lg-10 col-md-12">
            <div class="card shadow-lg login-card">
                <div class="row g-0">
                    <!-- Left Side - Student Icon Section -->
                    <div class="col-md-6 d-none d-md-flex student-section">
                        <div class="student-container">
                            <div class="student-icon-wrapper">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="student-content">
                                <h2>Student Portal</h2>
                                <p>Access your hostel accommodation details</p>
                                <div class="student-features">
                                    <div class="feature-item">
                                        <i class="fas fa-home"></i>
                                        <span>Room Allocation</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>Booking Management</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-file-invoice"></i>
                                        <span>Payment Status</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side - Login Form -->
                    <div class="col-md-6 form-section">
                        <div class="card-body p-4">
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
                                    <i class="fas fa-user-graduate me-2 d-md-none"></i>
                                    {{ __('Student Login') }}
                                </h3>
                                <p class="text-muted">Enter your credentials to access the student portal</p>
                            </div>

                            <form method="POST" action="{{ route('student.login.post') }}" id="loginForm">
                                @csrf

                                <div class="mb-3">
                                    <label for="admission_number" class="form-label">
                                        <i class="fas fa-id-card me-2"></i>{{ __('Admission Number') }}
                                    </label>
                                    <input id="admission_number" type="text" 
                                           class="form-control @error('admission_number') is-invalid @enderror" 
                                           name="admission_number" value="{{ old('admission_number') }}" required autofocus
                                           placeholder="Enter your admission number">

                                    @error('admission_number')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="contact_number" class="form-label">
                                        <i class="fas fa-phone me-2"></i>{{ __('Contact Number') }}
                                    </label>
                                    <input id="contact_number" type="text" 
                                           class="form-control @error('contact_number') is-invalid @enderror" 
                                           name="contact_number" required
                                           placeholder="Enter your contact number">

                                    @error('contact_number')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2 mb-3">
                                    <button type="submit" class="btn btn-danger login-btn" id="loginBtn">
                                        <span class="btn-content">
                                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('Login') }}
                                        </span>
                                        <span class="btn-loading d-none">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Logging in...
                                        </span>
                                    </button>
                                </div>
                            </form>

                            <div class="mt-4 pt-3 border-top text-center">
                                <p class="mb-0">Are you an admin? 
                                    <a href="{{ route('login') }}" class="admin-login-link">
                                        <i class="fas fa-user-shield me-1"></i>Login here
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