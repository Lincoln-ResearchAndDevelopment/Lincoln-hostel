@extends('layouts.student')

@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 text-white shadow overflow-hidden position-relative" style="background: linear-gradient(135deg, #cc0000 0%, #a30000 100%);">
            <div class="card-body p-4 position-relative z-1">
                @php
                    date_default_timezone_set('Africa/Lagos');
                    $hour = date('H');
                    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
                @endphp
                <h2 class="fw-bold">{{ $greeting }}, {{ $student->first_name }}! 👋</h2>
                <p class="mb-0 opacity-75">Welcome to your student portal. Current Session: 2025/2026</p>
                
                @if(!$student->room_id)
                    <div class="mt-3">
                        <a href="{{ route('student.hostels.index') }}" class="btn btn-light fw-bold" style="color: #cc0000;">
                            <i class="fas fa-search me-2"></i>Find & Book a Room
                        </a>
                    </div>
                @else
                    <div class="mt-3">
                        <span class="badge bg-white px-3 py-2 rounded-pill" style="color: #cc0000;">
                            <i class="fas fa-home me-2"></i>Room {{ $student->room->room_number }} ({{ $student->room->hostel->name }})
                        </span>
                    </div>
                @endif
            </div>
            <div class="position-absolute top-0 end-0 p-5 bg-white opacity-10 rounded-circle" style="margin-right: -3rem; margin-top: -3rem; width: 200px; height: 200px;"></div>
        </div>
    </div>
</div>
<!-- Pending Booking Alert -->
@if(isset($pendingBooking) && $pendingBooking)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-0" role="alert" style="background-color: #fff3cd; border-left: 5px solid #ffc107 !important;">
            <div class="p-2 bg-warning rounded-circle me-3 text-white">
                <i class="fas fa-clock fa-fw"></i>
            </div>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Room Booking Awaiting Approval</h6>
                <p class="mb-0">Your request for <strong>Room {{ $pendingBooking->room->room_number }}</strong> in <strong>{{ $pendingBooking->room->hostel->name }}</strong> is currently being reviewed. We will notify you once it's approved.</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Notifications Alert -->
@if(isset($unreadNotifications) && $unreadNotifications->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        @foreach($unreadNotifications->take(3) as $notification)
        <div class="alert alert-{{ $notification->type == 'leave_approved' ? 'success' : ($notification->type == 'leave_rejected' ? 'danger' : 'info') }} alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="{{ $notification->icon }} me-3 fa-lg"></i>
            <div class="flex-grow-1">
                <strong>{{ $notification->title }}</strong>
                <p class="mb-0 small">{{ $notification->message }}</p>
            </div>
            <a href="{{ route('student.notifications.mark-read', $notification->id) }}" class="btn btn-sm btn-outline-{{ $notification->type == 'leave_approved' ? 'success' : ($notification->type == 'leave_rejected' ? 'danger' : 'info') }} ms-3">
                Dismiss
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Quick Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Total Paid</h6>
                    <div class="rounded px-2 py-1" style="background-color: rgba(40, 167, 69, 0.1);">
                        <i class="fas fa-wallet text-success"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold">₦{{ number_format($total_paid, 2) }}</h3>
                <small class="text-muted">{{ $total_payments }} transactions</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Pending Payments</h6>
                    <div class="rounded px-2 py-1" style="background-color: rgba(255, 193, 7, 0.1);">
                        <i class="fas fa-hourglass-half text-warning"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold">{{ $pending_payments }}</h3>
                <small class="text-muted">Awaiting verification</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Complaints</h6>
                    <div class="rounded px-2 py-1" style="background-color: rgba(204, 0, 0, 0.1);">
                        <i class="fas fa-exclamation-circle" style="color: #cc0000;"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold">{{ $total_complaints }}</h3>
                <small class="text-muted">{{ $pending_complaints }} active</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Announcements</h6>
                    <div class="rounded px-2 py-1" style="background-color: rgba(23, 162, 184, 0.1);">
                        <i class="fas fa-bullhorn text-info"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold">{{ $unreadAnnouncements }}</h3>
                <small class="text-muted">New updates</small>
            </div>
        </div>
    </div>
</div>

<!-- Second Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('student.leave.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">Leave Requests</h6>
                        <div class="rounded px-2 py-1" style="background-color: rgba(204, 0, 0, 0.1);">
                            <i class="fas fa-paper-plane" style="color: #cc0000;"></i>
                        </div>
                    </div>
                    <h3 class="mb-0 fw-bold text-dark">{{ $student->leaveRequests()->where('status', 'pending')->count() }}</h3>
                    <small class="text-muted">Pending approvals</small>
                </div>
            </div>
        </a>
    </div>
    @if($student->room_id)
    <div class="col-6 col-md-3">
        <a href="{{ route('student.room.details') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">My Room</h6>
                        <div class="rounded px-2 py-1" style="background-color: rgba(204, 0, 0, 0.1);">
                            <i class="fas fa-door-open" style="color: #cc0000;"></i>
                        </div>
                    </div>
                    <h3 class="mb-0 fw-bold text-dark">{{ $student->room->room_number }}</h3>
                    <small class="text-muted">{{ $student->room->hostel->name }}</small>
                </div>
            </div>
        </a>
    </div>
    @endif
</div>

<div class="row g-4">
    <!-- Student Info -->
    <div class="col-lg-8" id="student-info">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-id-card me-2" style="color: #cc0000;"></i>My Profile</h5>
                <a href="{{ route('student.profile.index') }}" class="btn btn-sm btn-outline-primary">View Full Profile</a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Full Name</small>
                            <span class="fw-bold">{{ $student->full_name }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Student ID / Admission No</small>
                            <span class="fw-bold">{{ $student->admission_number }}</span>
                        </div>
                    </div>
                    @if($student->application)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Application Number</small>
                            <span class="fw-bold">{{ $student->application->application_number }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Department</small>
                            <span class="fw-bold">{{ $student->department }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Status</small>
                            <span class="badge {{ $student->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roommates Section -->
        @if($student->room)
        <div class="card border-0 shadow-sm mb-4" id="roommates">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-users me-2" style="color: #cc0000;"></i>My Roommates</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Contact</th>
                            <th>Check-in</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->room->students->where('id', '!=', $student->id) as $roommate)
                        <tr>
                            <td class="ps-4 fw-medium">{{ $roommate->full_name }}</td>
                            <td>{{ $roommate->email }}</td>
                            <td>{{ $roommate->check_in_date ? $roommate->check_in_date->format('M d, Y') : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No roommates yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <a href="{{ route('student.hostel.rules') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-book fa-2x mb-2 d-block" style="color: #cc0000;"></i>
                            Hostel Rules
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('student.complaints.index') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-exclamation-circle fa-2x mb-2 d-block" style="color: #cc0000;"></i>
                            Report Issue
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('student.leave.create') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-calendar-alt fa-2x mb-2 d-block" style="color: #cc0000;"></i>
                            Request Leave
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('student.fees.index') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-money-bill fa-2x mb-2 d-block" style="color: #cc0000;"></i>
                            View Fees
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-bell me-2" style="color: #cc0000;"></i>Latest Updates</h5>
                <a href="{{ route('student.announcements.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($latestAnnouncements as $announcement)
                    <div class="list-group-item p-3 border-bottom">
                        <div class="d-flex w-100 justify-content-between mb-1">
                            <h6 class="mb-0 fw-bold text-truncate">{{ $announcement->title }}</h6>
                            <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1 small text-muted text-truncate">{{ $announcement->description }}</p>
                        @if($announcement->hasAttachment())
                            <small style="color: #cc0000;"><i class="fas fa-paperclip me-1"></i>Attachment</small>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">No announcements.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
