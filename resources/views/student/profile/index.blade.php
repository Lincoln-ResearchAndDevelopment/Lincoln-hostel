@extends('layouts.app')

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
                            <span class="text-muted float-end">{{ $student->full_name }}</span>
                        </div>
                        <div class="info-item p-3 border-bottom">
                            <strong class="text-dark">Admission #:</strong>
                            <span class="text-muted float-end">{{ $student->admission_number }}</span>
                        </div>
                        <div class="info-item p-3 border-bottom">
                            <strong class="text-dark">Contact:</strong>
                            <span class="text-muted float-end">{{ $student->contact_number }}</span>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ url('/student/profile/edit') }}" class="btn btn-dark px-4">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                        <a href="{{ url('/student/profile/change-password') }}" class="btn btn-dark px-4"><i class="fas fa-shield me-2"></i>Change Password</a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
