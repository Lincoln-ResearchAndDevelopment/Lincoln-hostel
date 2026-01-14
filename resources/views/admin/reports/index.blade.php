@extends('layouts.admin')

@section('page-title', 'Reports')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Reports</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-0"><i class="fas fa-chart-bar me-2" style="color: #cc0000;"></i>Hostel Reports</h5>
        <p class="text-muted mb-0">Generate and view various hostel reports</p>
    </div>
</div>

<div class="row g-4">
    <!-- Occupancy Report -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-primary-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-bed fa-2x" style="color: #cc0000;"></i>
                </div>
                <h5 class="fw-bold">Occupancy Report</h5>
                <p class="text-muted small mb-4">View room occupancy rates, available beds, and capacity utilization across all hostels.</p>
                <a href="{{ route('admin.reports.occupancy') }}" class="btn btn-primary w-100">
                    <i class="fas fa-eye me-2"></i>View Report
                </a>
            </div>
        </div>
    </div>

    <!-- Fee Collection Report -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                </div>
                <h5 class="fw-bold">Fee Collection Report</h5>
                <p class="text-muted small mb-4">Track fee collections, outstanding payments, and payment trends over time.</p>
                <a href="{{ route('admin.reports.fees') }}" class="btn btn-success w-100">
                    <i class="fas fa-eye me-2"></i>View Report
                </a>
            </div>
        </div>
    </div>

    <!-- Complaints Report -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                </div>
                <h5 class="fw-bold">Complaints Report</h5>
                <p class="text-muted small mb-4">Analyze complaint patterns, resolution times, and common issues reported.</p>
                <a href="{{ route('admin.reports.complaints') }}" class="btn btn-warning w-100">
                    <i class="fas fa-eye me-2"></i>View Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mt-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-chart-pie me-2" style="color: #cc0000;"></i>Quick Overview</h6>
            </div>
            <div class="card-body">
                <div class="row g-4 text-center">
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded">
                            <h3 class="fw-bold mb-1" style="color: #cc0000;">{{ \App\Models\Hostel::count() }}</h3>
                            <small class="text-muted">Total Hostels</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded">
                            <h3 class="fw-bold mb-1 text-primary">{{ \App\Models\Room::count() }}</h3>
                            <small class="text-muted">Total Rooms</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded">
                            <h3 class="fw-bold mb-1 text-success">{{ \App\Models\Student::where('status', 'active')->count() }}</h3>
                            <small class="text-muted">Active Students</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded">
                            <h3 class="fw-bold mb-1 text-info">{{ \App\Models\Payment::whereMonth('created_at', now()->month)->count() }}</h3>
                            <small class="text-muted">Payments This Month</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
