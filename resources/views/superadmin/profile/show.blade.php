@extends('layouts.app')

@section('page-title', 'Super Admin Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header "style="background:red;color:white;"
                >
                    <h3 class="mb-0">
                        <i  class="fas fa-user-circle me-2 "></i>
                        Super Admin Profile
                    </h3>
                </div>

                    <div class="row">
                        <div class="col-md-4 text-center mb-4" >
                            <div class="avatar-large mx-auto mb-3 bg-white">
                                <i class="fas fa-user-tie fa-4x text-black "></i>
                            </div>
                            <h4>{{ $superAdmin->name }}</h4>
                            <p class="text-muted">{{ $superAdmin->email }}</p>
                            @if($superAdmin->is_master)
                                <span  style="background:red;" class="badge ">Master Admin</span>
                            @else
                                <span class="badge bg-info">System Admin</span>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <h5>Profile Information</h5>
                            <hr>

                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Name:</strong></div>
                                <div class="col-sm-9">{{ $superAdmin->name }}</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Email:</strong></div>
                                <div class="col-sm-9">{{ $superAdmin->email }}</div>
                            </div>

                            @if($superAdmin->phone)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Phone:</strong></div>
                                <div class="col-sm-9">{{ $superAdmin->phone }}</div>
                            </div>
                            @endif

                            @if($superAdmin->bio)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Bio:</strong></div>
                                <div class="col-sm-9">{{ $superAdmin->bio }}</div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Status:</strong></div>
                                <div class="col-sm-9">
                                    @if($superAdmin->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Last Login:</strong></div>
                                <div class="col-sm-9">{{ $superAdmin->formatted_last_login }}</div>
                            </div>

                            @if($superAdmin->last_login_ip)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Last IP:</strong></div>
                                <div class="col-sm-9">{{ $superAdmin->last_login_ip }}</div>
                            </div>
                            @endif

                            <div class="mt-4">
                                <a style="background:red;color:white;" href="{{ route('superadmin.profile.edit') }}" class="btn">
                                    <i class="fas fa-edit me-2 text-white"></i>Edit Profile
                                </a>
                                <a style="background:red; color:white;" href="{{ route('superadmin.profile.password.form') }}" class="btn">
                                    <i  class="fas fa-key me-2 text-white"></i>Change Password
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #dee2e6;
}

.badge {
    font-size: 0.8rem;
}
</style>
@endsection
