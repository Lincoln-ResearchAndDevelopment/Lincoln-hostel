@extends('layouts.app')

@section('page-title', 'Super Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div style="background:red;" class="text-white " >
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-crown me-2"></i>
                                Welcome back, {{ $super_admin->name }}!
                                @if($is_master_admin)
                                    <span class="badge bg-white text-black ms-2">Master Admin</span>
                                @else
                                    <span class="badge bg-info ms-2">System Admin</span>
                                @endif
                            </h2>
                            <p class="mb-0 opacity-75">
                                Super Administrator Dashboard - Full system control and monitoring
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="me-3">
                                    <small class="opacity-75">Last Login</small><br>
                                    <span class="fw-bold">{{ $super_admin ? $super_admin->formatted_last_login : 'Never' }}</span>
                                </div>
                                <div class="avatar-circle bg-white text-primary">
                                    <i style="color:black;" class="fas fa-user-tie fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Overview Cards -->
    <div class="row mb-4">
        <!-- Users Overview -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-black shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-black text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total_users) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Overview -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-black shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-black text-uppercase mb-1">
                                Active Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total_students) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rooms Overview -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-black shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-black text-uppercase mb-1">
                                Available Rooms</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $available_rooms }} / {{ $total_rooms }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Overview -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-black shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-black text-uppercase mb-1">
                                Monthly Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₦{{ number_format($monthly_revenue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Overview Row -->
    <div class="row mb-4">
        <!-- Complaints -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold " style="color:black;">
                        <i class="fas fa-exclamation-triangle me-2"></i>Complaints Overview
                    </h6>
                    <span class="badge  text-white" style="background:red;">{{ $pending_complaints }} pending</span>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 mb-0 text-black">{{ $total_complaints }}</div>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-black">{{ $pending_complaints }}</div>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitors -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold" style="color:black;">
                        <i class="fas fa-users me-2"></i>Visitor Management
                    </h6>
                    <span class="badge  text-white" style="background:red;">{{ $current_visitors }} active</span>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 mb-0 text-black">{{ $total_visitors }}</div>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-black">{{ $current_visitors }}</div>
                            <small class="text-muted">Current</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Tables -->
    <div class="row">
        <!-- Recent Payments -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-black">
                        <i class="fas fa-credit-card me-2"></i>Recent Payments
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($recent_payments as $payment)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <div class="fw-bold">{{ $payment->student->full_name }}</div>
                                <small class="text-muted">{{ $payment->payment_date->format('M d, Y') }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">₦{{ number_format($payment->amount, 2) }}</div>
                                <span class="badge bg-{{ $payment->status == 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted mb-0">No recent payments</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Complaints -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-black">
                        <i class="fas fa-exclamation-circle me-2"></i>Recent Complaints
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($recent_complaints as $complaint)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                @if($complaint->attachment_path)
                                    <a href="{{ asset('storage/' . $complaint->attachment_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $complaint->attachment_path) }}" alt="attachment" class="complaint-thumb" style="width:64px; height:48px;">
                                    </a>
                                @else
                                    <div style="width:64px;height:48px;border-radius:6px;background:#f1f1f1;display:inline-block;"></div>
                                @endif

                                <div>
                                    <div class="fw-bold">{{ $complaint->student->full_name }}</div>
                                    <small class="text-muted">{{ Str::limit($complaint->subject ?? $complaint->description, 30) }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span  class="badge bg-{{ $complaint->status == 'resolved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($complaint->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted mb-0">No recent complaints</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    @if($system_alerts->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell me-2"></i>System Alerts
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($system_alerts as $alert)
                        <div class="alert alert-{{ $alert->type }} alert-dismissible fade show" role="alert">
                            <strong>{{ $alert->title }}</strong><br>
                            {{ $alert->message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-black">
                        <i class="fas fa-cogs me-2"></i>System Management
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <a href="{{ route('superadmin.admin-management.index') }}" class="text-decoration-none">
                                <div class="card border-black h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users-cog fa-3x text-black mb-3"></i>
                                        <h5 class="card-title text-black">Admin Management</h5>
                                        <p class="card-text text-muted">Create, edit, and manage system administrators</p>
                                        <span style="background:red;" class="badge">{{ $total_users - ($total_students ?? 0) }} Admins</span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4 text-center mb-3">
                            <a href="{{ route('superadmin.profile.show') }}" class="text-decoration-none">
                                <div class="card border-black h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-shield fa-3x text-black mb-3"></i>
                                        <h5 class="card-title text-black">Profile Settings</h5>
                                        <p class="card-text text-black">Manage your account settings and preferences</p>
                                        <span style="background:red;" class="badge b">Account</span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4 text-center mb-3">
                            <div class="card border-black h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-black mb-3"></i>
                                    <h5 class="card-title text-black">System Reports</h5>
                                    <p class="card-text text-black">View detailed analytics and reports</p>
                                    <span style="background:red;" class="badge ">Coming Soon</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
