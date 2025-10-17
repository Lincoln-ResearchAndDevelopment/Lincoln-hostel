@extends('layouts.app')

@section('page-title', 'Super Admin Login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="my-2">
                        <i class="fas fa-user-shield"></i>
                        Super Admin Login
                    </h3>
                    <p class="mb-0">Access the system management panel</p>
                </div>

                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('superadmin.login.post') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   placeholder="name@example.com"
                                   value="{{ old('email') }}"
                                   required>
                            <label for="email">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Password"
                                   required>
                            <label for="password">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-center bg-light">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Secure access to system administration
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

[data-theme="dark"] .card {
    background: rgba(33, 37, 41, 0.95);
    color: white;
}

.form-floating > .form-control {
    height: calc(3.5rem + 2px);
    padding: 1rem 0.75rem;
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    padding: 12px 24px;
    font-weight: 600;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #0056b3, #004085);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus on email field when page loads
    document.getElementById('email').focus();

    // Add enter key support for form submission
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
            const form = document.querySelector('form');
            if (form.checkValidity()) {
                form.submit();
            }
        }
    });
});
</script>
@endsection
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="my-2">
                        <i class="fas fa-user-shield"></i>
                        Super Admin Login
                    </h3>
                    <p class="mb-0">Access the system management panel</p>
                </div>

                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('superadmin.login.post') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   placeholder="name@example.com"
                                   value="{{ old('email') }}"
                                   required>
                            <label for="email">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Password"
                                   required>
                            <label for="password">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-center bg-light">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Secure access to system administration
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

[data-theme="dark"] .card {
    background: rgba(33, 37, 41, 0.95);
    color: white;
}

.form-floating > .form-control {
    height: calc(3.5rem + 2px);
    padding: 1rem 0.75rem;
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    padding: 12px 24px;
    font-weight: 600;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #0056b3, #004085);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus on email field when page loads
    document.getElementById('email').focus();

    // Add enter key support for form submission
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
            const form = document.querySelector('form');
            if (form.checkValidity()) {
                form.submit();
            }
        }
    });
});
</script>
@endsection
