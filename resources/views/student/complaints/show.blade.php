@extends('layouts.student')

@section('page-title', 'Complaint Details')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.complaints.index') }}">Complaints</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2" style="color: #cc0000;"></i>{{ $complaint->subject }}</h5>
                @switch($complaint->status)
                    @case('submitted')
                        <span class="badge bg-warning">Submitted</span>
                        @break
                    @case('in progress')
                        <span class="badge bg-info">In Progress</span>
                        @break
                    @case('resolved')
                        <span class="badge bg-success">Resolved</span>
                        @break
                    @case('rejected')
                        <span class="badge bg-danger">Rejected</span>
                        @break
                    @default
                        <span class="badge bg-secondary">{{ ucfirst($complaint->status) }}</span>
                @endswitch
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Description</h6>
                    <p class="mb-0">{{ $complaint->description }}</p>
                </div>

                @if($complaint->attachment)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Attachment</h6>
                    <a href="{{ asset('storage/' . $complaint->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-paperclip me-1"></i>View Attachment
                    </a>
                </div>
                @endif

                @if($complaint->admin_response)
                <div class="alert alert-info">
                    <h6 class="mb-2"><i class="fas fa-reply me-2"></i>Admin Response</h6>
                    <p class="mb-0">{{ $complaint->admin_response }}</p>
                    @if($complaint->responded_at)
                        <small class="text-muted d-block mt-2">Responded on {{ $complaint->responded_at->format('M d, Y h:i A') }}</small>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2" style="color: #cc0000;"></i>Complaint Info</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Submitted On</small>
                    <strong>{{ $complaint->created_at->format('M d, Y h:i A') }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Last Updated</small>
                    <strong>{{ $complaint->updated_at->format('M d, Y h:i A') }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    @switch($complaint->status)
                        @case('submitted')
                            <span class="badge bg-warning">Submitted</span>
                            @break
                        @case('in progress')
                            <span class="badge bg-info">In Progress</span>
                            @break
                        @case('resolved')
                            <span class="badge bg-success">Resolved</span>
                            @break
                        @case('rejected')
                            <span class="badge bg-danger">Rejected</span>
                            @break
                        @default
                            <span class="badge bg-secondary">{{ ucfirst($complaint->status) }}</span>
                    @endswitch
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('student.complaints.index') }}" class="btn btn-outline-secondary w-100">
                <i class="fas fa-arrow-left me-2"></i>Back to Complaints
            </a>
        </div>
    </div>
</div>
@endsection
