@extends('layouts.admin')

@section('page-title', 'Leave Requests')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Leave Requests</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #cc0000 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Total Requests</p>
                <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Pending</p>
                <h4 class="fw-bold mb-0">{{ $stats['pending'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Approved</p>
                <h4 class="fw-bold mb-0">{{ $stats['approved'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Rejected</p>
                <h4 class="fw-bold mb-0">{{ $stats['rejected'] }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.leave.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="all">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Search Student</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name or Admission No" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">From Date</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">To Date</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Leave Requests Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2" style="color: #cc0000;"></i>Leave Requests</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Student</th>
                    <th>Type</th>
                    <th>Duration</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaveRequests as $request)
                <tr>
                    <td class="ps-3">
                        <strong>{{ $request->student->full_name ?? 'N/A' }}</strong>
                        <br><small class="text-muted">{{ $request->student->admission_number ?? '' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-{{ $request->type == 'medical' ? 'info' : ($request->type == 'home' ? 'success' : 'secondary') }}">
                            {{ ucfirst($request->type) }}
                        </span>
                    </td>
                    <td>
                        {{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d, Y') }}
                        <br><small class="text-muted">{{ $request->start_date->diffInDays($request->end_date) + 1 }} days</small>
                    </td>
                    <td>{{ Str::limit($request->reason, 40) }}</td>
                    <td>
                        @if($request->status == 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($request->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td>
                        @if($request->status == 'pending')
                            <form action="{{ route('admin.leave.approve', $request) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                        <a href="{{ route('admin.leave.show', $request) }}" class="btn btn-sm btn-outline-secondary" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.leave.reject', $request) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Reject Leave Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Rejecting leave request for <strong>{{ $request->student->full_name ?? 'N/A' }}</strong></p>
                                    <div class="mb-3">
                                        <label class="form-label">Reason for Rejection</label>
                                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Please provide a reason..."></textarea>
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
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No leave requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($leaveRequests->hasPages())
    <div class="card-footer bg-white">
        {{ $leaveRequests->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
