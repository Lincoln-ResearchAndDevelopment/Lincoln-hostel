@extends('layouts.admin')

@section('page-title', 'Hostel Applications')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Applications</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2" style="color: #cc0000;"></i>Hostel Applications</h6>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('applications.index') }}">All</a></li>
                <li><a class="dropdown-item" href="{{ route('applications.index', ['status' => 'pending']) }}">Pending</a></li>
                <li><a class="dropdown-item" href="{{ route('applications.index', ['status' => 'approved']) }}">Approved</a></li>
                <li><a class="dropdown-item" href="{{ route('applications.index', ['status' => 'rejected']) }}">Rejected</a></li>
            </ul>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Application #</th>
                    <th>Student</th>
                    <th>Contact</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                <tr>
                    <td class="ps-3"><strong class="text-primary">{{ $application->application_number }}</strong></td>
                    <td>
                        <strong>{{ $application->full_name }}</strong><br>
                        <small class="text-muted">{{ $application->reg_number }}</small>
                    </td>
                    <td>
                        <small>{{ $application->email }}</small><br>
                        <small class="text-muted">{{ $application->phone_number }}</small>
                    </td>
                    <td>
                        <small>{{ $application->department }}</small><br>
                        <small class="text-muted">{{ $application->program }}</small>
                    </td>
                    <td>{!! $application->status_badge !!}</td>
                    <td>{{ $application->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('applications.show', $application) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                        @if($application->status === 'pending')
                        <button class="btn btn-sm btn-outline-success" onclick="updateStatus({{ $application->id }}, 'approved')"><i class="fas fa-check"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="updateStatus({{ $application->id }}, 'rejected')"><i class="fas fa-times"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No applications found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
    <div class="card-footer bg-white">{{ $applications->links() }}</div>
    @endif
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Application Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="under_review">Under Review</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(applicationId, status) {
    const modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    document.getElementById('statusUpdateForm').action = `/applications/${applicationId}/status`;
    document.getElementById('status').value = status;
    modal.show();
}
</script>
@endpush
