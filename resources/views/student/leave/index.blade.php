@extends('layouts.student')

@section('page-title', 'My Leave Requests')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Leave Requests</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Leave History</h4>
            <a href="{{ route('student.leave.create') }}" class="btn" style="background-color: #cc0000; color: white;">
                <i class="fas fa-plus me-2"></i>Apply for Leave
            </a>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Type</th>
                                <th>Duration</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Applied On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaveRequests as $request)
                                <tr>
                                    <td class="ps-4">
                                        @if($request->type == 'medical')
                                            <span class="badge bg-info-subtle text-info"><i class="fas fa-notes-medical me-1"></i> Medical</span>
                                        @elseif($request->type == 'home')
                                            <span class="badge bg-success-subtle text-success"><i class="fas fa-home me-1"></i> Home Visit</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary"><i class="fas fa-star me-1"></i> Other</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $request->start_date->format('M d, Y') }}</span>
                                            <small class="text-muted">to {{ $request->end_date->format('M d, Y') }}</small>
                                            <small class="text-muted">({{ $request->duration }} days)</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $request->reason }}">
                                            {{ $request->reason }}
                                        </span>
                                        @if($request->destination)
                                            <br><small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $request->destination }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                            @if($request->approved_at)
                                                <br><small class="text-muted">{{ $request->approved_at->format('M d') }}</small>
                                            @endif
                                        @elseif($request->status == 'rejected')
                                            <span class="badge bg-danger" data-bs-toggle="tooltip" title="{{ $request->rejection_reason }}">Rejected</span>
                                            @if($request->rejection_reason)
                                                <br><small class="text-danger" title="{{ $request->rejection_reason }}"><i class="fas fa-info-circle"></i> {{ Str::limit($request->rejection_reason, 20) }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-paper-plane fa-3x mb-3 opacity-50"></i>
                                            <p class="h5">No leave requests found</p>
                                            <a href="{{ route('student.leave.create') }}" class="btn btn-sm mt-2" style="background-color: #cc0000; color: white;">
                                                Apply Now
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($leaveRequests->hasPages())
                <div class="card-footer bg-white">
                    {{ $leaveRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
