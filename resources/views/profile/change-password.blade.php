@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-3">
                    <h2 class="h5 mb-0">Change Password</h2>
                </div>
                
                <div class="card-body bg-light">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="current_password" class="form-label text-dark fw-semibold">Current Password</label>
                            <input type="password" name="current_password" class="form-control border-gray" 
                                   required style="background-color: #fff; border-color: #dee2e6;">
                            @error('current_password')
                                <div class="text-danger mt-2 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password" class="form-label text-dark fw-semibold">New Password</label>
                            <input type="password" name="new_password" class="form-control border-gray" 
                                   required style="background-color: #fff; border-color: #dee2e6;">
                            @error('new_password')
                                <div class="text-danger mt-2 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label text-dark fw-semibold">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control border-gray" 
                                   required style="background-color: #fff; border-color: #dee2e6;">
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('profile.index') }}" class="btn btn-outline-dark px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-dark px-4">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
body {
    background-color: #f8f9fa;
    color: #212529;
}

.card {
    border-radius: 8px;
}

.card-header {
    border-radius: 8px 8px 0 0 !important;
}

.form-control {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 0.75rem;
    transition: all 0.3s ease;
    color: #212529;
}

.form-control:focus {
    border-color: #000;
    box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

.form-label {
    color: #000;
    margin-bottom: 0.5rem;
}

.btn-dark {
    background-color: #000;
    border-color: #000;
    transition: all 0.3s ease;
}

.btn-dark:hover {
    background-color: #333;
    border-color: #333;
    transform: translateY(-1px);
}

.btn-outline-dark {
    border-color: #000;
    color: #000;
    transition: all 0.3s ease;
}

.btn-outline-dark:hover {
    background-color: #000;
    border-color: #000;
    color: #fff;
    transform: translateY(-1px);
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1) !important;
}

.border-gray {
    border-color: #dee2e6 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.small {
    font-size: 0.875rem;
}
</style>
@endsection