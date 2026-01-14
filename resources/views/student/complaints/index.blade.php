@extends('layouts.student')

@section('page-title', 'My Complaints')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Complaints</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">My Complaints & Maintenance Requests</h4>
                        <p class="text-muted mb-0">Track the status of your submitted complaints</p>
                    </div>
                    <button class="btn" style="background-color: #cc0000; color: white;" data-bs-toggle="modal" data-bs-target="#newComplaintModal">
                        <i class="fas fa-plus me-2"></i>New Complaint
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #cc0000 !important;">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Complaints</h6>
                <h3 class="mb-0">{{ $complaints->total() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
            <div class="card-body">
                <h6 class="text-muted mb-2">Pending</h6>
                <h3 class="mb-0">{{ $student->complaints()->where('status', 'submitted')->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-start border-info border-4">
            <div class="card-body">
                <h6 class="text-muted mb-2">In Progress</h6>
                <h3 class="mb-0">{{ $student->complaints()->where('status', 'in progress')->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted mb-2">Resolved</h6>
                <h3 class="mb-0">{{ $student->complaints()->where('status', 'resolved')->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Complaints List -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-list me-2" style="color: #cc0000;"></i>Complaint History</h5>
            </div>
            <div class="card-body">
                @if($complaints->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Subject</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($complaints as $index => $complaint)
                                <tr>
                                    <td>{{ $complaints->firstItem() + $index }}</td>
                                    <td><strong>{{ $complaint->subject }}</strong></td>
                                    <td>{{ Str::limit($complaint->description, 50) }}</td>
                                    <td>
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
                                    </td>
                                    <td>
                                        {{ $complaint->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $complaint->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('student.complaints.show', $complaint->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $complaints->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-check fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Complaints</h5>
                        <p class="text-muted mb-3">You haven't submitted any complaints yet.</p>
                        <button class="btn" style="background-color: #cc0000; color: white;" data-bs-toggle="modal" data-bs-target="#newComplaintModal">
                            <i class="fas fa-plus me-1"></i>Submit Your First Complaint
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- New Complaint Modal -->
<div class="modal fade" id="newComplaintModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exclamation-circle me-2"></i>Submit New Complaint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('student.complaints.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" 
                               placeholder="Brief description of the issue" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" 
                                  placeholder="Provide detailed information about your complaint..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="attachment" class="form-label">Attachment (Optional)</label>
                        <input type="file" class="form-control" id="attachment" name="attachment" 
                               accept="image/*">
                        <small class="text-muted">Upload an image if relevant (JPG, PNG - Max 5MB)</small>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Your complaint will be reviewed by the hostel management. You will be notified when there's an update.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Complaint</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection