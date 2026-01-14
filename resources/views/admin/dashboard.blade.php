@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 text-white shadow" style="background: linear-gradient(135deg, #cc0000 0%, #a30000 100%);">
            <div class="card-body p-4">
                @php
                    date_default_timezone_set('Africa/Lagos');
                    $hour = date('H');
                    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
                @endphp
                <h2 class="fw-bold mb-1">{{ $greeting }}, {{ Auth::user()->name }}!</h2>
                <p class="mb-0 opacity-75">Welcome to your admin dashboard. Today is {{ now()->format('l, M d, Y') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row 1 -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #cc0000 !important;">
            <div class="card-body">
                <p class="text-muted mb-1 small">Hostels</p>
                <h3 class="fw-bold mb-0">{{ $total_hostels }}</h3>
                <small class="text-success">{{ $active_hostels }} active</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #cc0000 !important;">
            <div class="card-body">
                <p class="text-muted mb-1 small">Rooms</p>
                <h3 class="fw-bold mb-0">{{ $total_rooms }}</h3>
                <small class="text-success">{{ $available_rooms }} available</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #cc0000 !important;">
            <div class="card-body">
                <p class="text-muted mb-1 small">Students</p>
                <h3 class="fw-bold mb-0">{{ $total_students }}</h3>
                <small class="text-muted">Active residents</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-body">
                <p class="text-muted mb-1 small">Complaints</p>
                <h3 class="fw-bold mb-0">{{ $pending_complaints }}</h3>
                <small class="text-warning">Need attention</small>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row 2 -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body">
                <p class="text-muted mb-1 small">Applications</p>
                <h3 class="fw-bold mb-0">{{ $total_applications ?? 0 }}</h3>
                <small class="text-warning">{{ $pending_applications ?? 0 }} pending</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.leave.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
                <div class="card-body">
                    <p class="text-muted mb-1 small">Leave Requests</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $pending_leave ?? 0 }}</h3>
                    <small class="text-info">Pending</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6c757d !important;">
            <div class="card-body">
                <p class="text-muted mb-1 small">Visitors</p>
                <h3 class="fw-bold mb-0">{{ $recent_visitors->count() }}</h3>
                <small class="text-muted">In premises</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body">
                <p class="text-muted mb-1 small">Approved</p>
                <h3 class="fw-bold mb-0">{{ $approved_applications ?? 0 }}</h3>
                <small class="text-success">Applications</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Applications -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2" style="color: #cc0000;"></i>Recent Applications</h6>
                <a href="{{ route('applications.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr><th class="ps-3">Applicant</th><th>Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recent_applications ?? [] as $app)
                        <tr>
                            <td class="ps-3"><strong>{{ $app->first_name }} {{ $app->last_name }}</strong></td>
                            <td>{{ $app->created_at->format('M d') }}</td>
                            <td>
                                <span class="badge bg-{{ $app->status == 'pending' ? 'warning' : ($app->status == 'approved' ? 'success' : 'danger') }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">No recent applications</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('students.create') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-user-plus d-block mb-1" style="color: #cc0000;"></i>Add Student
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('rooms.create') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-door-open d-block mb-1" style="color: #cc0000;"></i>Add Room
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.announcements.create') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-bullhorn d-block mb-1" style="color: #cc0000;"></i>Announcement
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.attendance.create') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-clipboard-check d-block mb-1" style="color: #cc0000;"></i>Attendance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hostels Overview -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-building me-2" style="color: #cc0000;"></i>Hostels</h6>
                <a href="{{ route('hostels.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($latest_hostels as $hostel)
                    <div class="col-md-3 col-6">
                        <div class="card h-100 border">
                            @if($hostel->image_path)
                                <img src="{{ asset('storage/' . $hostel->image_path) }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px;">
                                    <i class="fas fa-building fa-2x text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1 text-truncate small fw-bold">{{ $hostel->name }}</h6>
                                <small class="text-muted"><i class="fas fa-bed me-1"></i>{{ $hostel->rooms_count }} Rooms</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-4 text-muted">No hostels found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
