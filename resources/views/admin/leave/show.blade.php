@extends('layouts.admin')

@section('page-title', 'Leave Request Details')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.leave.index') }}">Leave Requests</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Leave Request Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2" style="color: #cc0000;"></i>Leave Request Details</h6>
                @if($leaveRequest->status == 'pending')
                    <span class="badge bg-warning text-dark fs-6">Pending Review</span>
                @elseif($leaveRequest->status == 'approved')
                    <span class="badge bg-success fs-6">Approved</span>
                @else
                    <span class="badge bg-danger fs-6">Rejected</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Leave Type</label>
                        <p class="fw-semibold mb-0">
                            <span class="badge bg-{{ $leaveRequest->type == 'medical' ? 'info' : ($leaveRequest->type == 'home' ? 'success' : 'secondary') }} me-2">
                                {{ ucfirst($leaveRequest->type) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Duration</label>
                        <p class="fw-semibold mb-0">{{ $leaveRequest->start_date->diffInDays($leaveRequest->end_date) + 1 }} days</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Start Date</label>
                        <p class="fw-semibold mb-0">{{ $leaveRequest->start_date->format('l, M d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">End Date</label>
                        <p class="fw-semibold mb-0">{{ $leaveRequest->end_date->format('l, M d, Y') }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small">Reason</label>
                        <p class="mb-0">{{ $leaveRequest->reason }}</p>
                    </div>
                    @if($leaveRequest->emergency_contact)
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Emergency Contact</label>
                        <p class="fw-semibold mb-0">{{ $leaveRequest->emergency_contact }}</p>
                    </div>
                    @endif
                    @if($leaveRequest->destination)
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Destination</label>
                        <p class="fw-semibold mb-0">{{ $leaveRequest->destination }}</p>
                    </div>
                    @endif
                    <div class="col-12">
                        <label class="form-label text-muted small">Submitted On</label>
                        <p class="mb-0">{{ $leaveRequest->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>

                @if($leaveRequest->status == 'rejected' && $leaveRequest->rejection_reason)
                <div class="alert alert-danger mt-4 mb-0">
                    <strong><i class="fas fa-times-circle me-2"></i>Rejection Reason:</strong>
                    <p class="mb-0 mt-2">{{ $leaveRequest->rejection_reason }}</p>
                </div>
                @endif

                @if($leaveRequest->status == 'approved' && $leaveRequest->approved_at)
                <div class="alert alert-success mt-4 mb-0">
                    <strong><i class="fas fa-check-circle me-2"></i>Approved</strong>
                    <p class="mb-0 mt-2">Approved on {{ $leaveRequest->approved_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        @if($leaveRequest->status == 'pending')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-tasks me-2" style="color: #cc0000;"></i>Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <form action="{{ route('admin.leave.approve', $leaveRequest) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 py-2">
                                <i class="fas fa-check me-2"></i>Approve Request
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-danger w-100 py-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times me-2"></i>Reject Request
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.leave.reject', $leaveRequest) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Leave Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Please provide a reason for rejecting this request..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Student Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-user-graduate me-2" style="color: #cc0000;"></i>Student Information</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="rounded-circle bg-primary-subtle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <span class="fs-2 fw-bold" style="color: #cc0000;">{{ strtoupper(substr($leaveRequest->student->first_name ?? 'S', 0, 1)) }}</span>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">{{ $leaveRequest->student->full_name ?? 'N/A' }}</h5>
                <p class="text-muted mb-3">{{ $leaveRequest->student->admission_number ?? '' }}</p>
                
                <div class="text-start">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Email</span>
                        <span class="fw-semibold">{{ $leaveRequest->student->email ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Phone</span>
                        <span class="fw-semibold">{{ $leaveRequest->student->phone ?? 'N/A' }}</span>
                    </div>
                    @if($leaveRequest->student->room)
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Room</span>
                        <span class="fw-semibold">{{ $leaveRequest->student->room->room_number }}</span>
                    </div>
                    @endif
                    @if($leaveRequest->student->room && $leaveRequest->student->room->hostel)
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Hostel</span>
                        <span class="fw-semibold">{{ $leaveRequest->student->room->hostel->name }}</span>
                    </div>
                    @endif
                </div>

                <a href="{{ route('students.show', $leaveRequest->student) }}" class="btn btn-outline-primary w-100 mt-3">
                    <i class="fas fa-eye me-2"></i>View Full Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
