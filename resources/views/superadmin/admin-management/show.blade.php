@extends('layouts.app')

@section('page-title', 'Admin Details - ' . $adminUser->name)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            {{ $adminUser->name }}
                        </h3>
                        <div>
                            <a href="{{ route('superadmin.admin-management.edit', $adminUser) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('superadmin.admin-management.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="avatar-large mx-auto mb-3">
                                <i class="fas fa-user-tie fa-4x text-primary"></i>
                            </div>
                            <h4>{{ $adminUser->name }}</h4>
                            <p class="text-muted">{{ $adminUser->email }}</p>
                            @if($adminUser->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <h5>Account Information</h5>
                            <hr>

                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Name:</strong></div>
                                <div class="col-sm-9">{{ $adminUser->name }}</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Email:</strong></div>
                                <div class="col-sm-9">{{ $adminUser->email }}</div>
                            </div>

                            @if($adminUser->phone)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Phone:</strong></div>
                                <div class="col-sm-9">{{ $adminUser->phone }}</div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Role:</strong></div>
                                <div class="col-sm-9">
                                    <span class="badge bg-{{ $adminUser->role === 'admin' ? 'primary' : ($adminUser->role === 'manager' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($adminUser->role) }}
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Status:</strong></div>
                                <div class="col-sm-9">
                                    @if($adminUser->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Created:</strong></div>
                                <div class="col-sm-9">{{ $adminUser->created_at->format('M d, Y \a\t h:i A') }}</div>
                            </div>

                            @if($adminUser->created_by_user)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Created by:</strong></div>
                                <div class="col-sm-9">{{ $adminUser->created_by_user->name }}</div>
                            </div>
                            @endif

                            @if($adminUser->last_login_at)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Last Login:</strong></div>
                                <div class="col-sm-9">{{ $adminUser->last_login_at->format('M d, Y \a\t h:i A') }}</div>
                            </div>
                            @endif

                            @if($adminUser->last_login_ip)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Last IP:</strong></div>
                                <div class="col-sm-9">{{ $adminUser->last_login_ip }}</div>
                            </div>
                            @endif

                            @if($adminUser->activated_at)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Activated:</strong></div>
                                <div class="col-sm-9">{{ $adminUser->activated_at->format('M d, Y \a\t h:i A') }}</div>
                            </div>
                            @endif

                            @if($adminUser->deactivated_at)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Deactivated:</strong></div>
                                <div class="col-sm-9">{{ $adminUser->deactivated_at->format('M d, Y \a\t h:i A') }}</div>
                            </div>
                            @endif

                            @if($adminUser->permissions && count($adminUser->permissions) > 0)
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Permissions:</strong></div>
                                <div class="col-sm-9">
                                    @foreach($adminUser->permissions as $permission)
                                        <span class="badge bg-light text-dark me-1">{{ $permission }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="mt-4">
                                <a href="{{ route('superadmin.admin-management.edit', $adminUser) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>Edit Admin
                                </a>

                                @if($adminUser->is_active)
                                    <form action="{{ route('superadmin.admin-management.destroy', $adminUser) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to deactivate this admin?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-ban me-2"></i>Deactivate
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('superadmin.admin-management.activate', $adminUser) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>Activate
                                        </button>
                                    </form>
                                @endif
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
