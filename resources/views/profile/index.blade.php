@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-3">
                    <h2 class="h5 mb-0">My Profile</h2>
                </div>
                
                <div class="card-body bg-light">
                    <div class="profile-info mb-4">
                        <div class="info-item p-3 border-bottom">
                            <strong class="text-dark">Name:</strong>
                            <span class="text-muted float-end">{{ Auth::user()->name }}</span>
                        </div>
                        <div class="info-item p-3 border-bottom">
                            <strong class="text-dark">Email:</strong>
                            <span class="text-muted float-end">{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('profile.edit') }}" class="btn btn-dark px-4">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                            <a href="{{ route('profile.password.form') }}"class="btn btn-dark px-4"><i class="fas fa-shield me-2"></i>Change Password</a>

                    </div>
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

.info-item {
    background-color: #ffffff;
    transition: background-color 0.3s ease;
}

.info-item:hover {
    background-color: #f8f9fa;
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

.border-bottom {
    border-color: #dee2e6 !important;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1) !important;
}
</style>
@endsection