@extends('layouts.admin')

@section('page-title', 'Application Details')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('applications.index') }}">Applications</a></li>
                <li class="breadcrumb-item active">{{ $application->application_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Header Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1">Application #{{ $application->application_number }}</h5>
                <p class="text-muted mb-0">Submitted: {{ $application->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                {!! $application->status_badge !!}
                @if($application->status === 'pending' || $application->status === 'under_review')
                <button class="btn btn-success btn-sm" onclick="updateStatus('approved')"><i class="fas fa-check me-1"></i>Approve</button>
                <button class="btn btn-danger btn-sm" onclick="updateStatus('rejected')"><i class="fas fa-times me-1"></i>Reject</button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Student Information -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-user-graduate me-2" style="color: #cc0000;"></i>Student Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><label class="text-muted small">Full Name</label><p class="mb-0 fw-semibold">{{ $application->full_name }}</p></div>
                    <div class="col-6"><label class="text-muted small">Reg Number</label><p class="mb-0 fw-semibold">{{ $application->reg_number }}</p></div>
                    <div class="col-6"><label class="text-muted small">Gender</label><p class="mb-0">{{ ucfirst($application->gender) }}</p></div>
                    <div class="col-6"><label class="text-muted small">Date of Birth</label><p class="mb-0">{{ $application->date_of_birth->format('M d, Y') }}</p></div>
                    <div class="col-6"><label class="text-muted small">Phone</label><p class="mb-0">{{ $application->phone_number }}</p></div>
                    <div class="col-6"><label class="text-muted small">Email</label><p class="mb-0">{{ $application->email }}</p></div>
                    <div class="col-12"><label class="text-muted small">Home Address</label><p class="mb-0">{{ $application->home_address }}</p></div>
                    <div class="col-4"><label class="text-muted small">Nationality</label><p class="mb-0">{{ $application->nationality }}</p></div>
                    <div class="col-4"><label class="text-muted small">State</label><p class="mb-0">{{ $application->state_of_origin }}</p></div>
                    <div class="col-4"><label class="text-muted small">LGA</label><p class="mb-0">{{ $application->local_government }}</p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Information -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-graduation-cap me-2" style="color: #cc0000;"></i>Academic Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><label class="text-muted small">Academic Year</label><p class="mb-0 fw-semibold">{{ $application->academic_year }}</p></div>
                    <div class="col-6"><label class="text-muted small">Intake</label><p class="mb-0">{{ $application->intake }}</p></div>
                    <div class="col-6"><label class="text-muted small">Program</label><p class="mb-0">{{ $application->program }}</p></div>
                    <div class="col-6"><label class="text-muted small">Department</label><p class="mb-0">{{ $application->department }}</p></div>
                    <div class="col-12"><label class="text-muted small">Amount Paid</label><p class="mb-0 fw-bold text-success">₦{{ number_format($application->amount_paid) }}</p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent/Guardian Information -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-users me-2" style="color: #cc0000;"></i>Parent/Guardian</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><label class="text-muted small">Full Name</label><p class="mb-0 fw-semibold">{{ $application->parent_full_name }}</p></div>
                    <div class="col-6"><label class="text-muted small">Relationship</label><p class="mb-0">{{ ucfirst($application->parent_relationship) }}</p></div>
                    <div class="col-6"><label class="text-muted small">Phone</label><p class="mb-0">{{ $application->parent_phone }}</p></div>
                    <div class="col-6"><label class="text-muted small">Email</label><p class="mb-0">{{ $application->parent_email ?: 'N/A' }}</p></div>
                    <div class="col-6"><label class="text-muted small">Occupation</label><p class="mb-0">{{ $application->parent_occupation }}</p></div>
                    <div class="col-6"><label class="text-muted small">Workplace</label><p class="mb-0">{{ $application->parent_workplace ?: 'N/A' }}</p></div>
                    <div class="col-12"><label class="text-muted small">Address</label><p class="mb-0">{{ $application->parent_address }}</p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency Contact -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-phone-alt me-2" style="color: #cc0000;"></i>Emergency Contact</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><label class="text-muted small">Name</label><p class="mb-0 fw-semibold">{{ $application->emergency_contact_name }}</p></div>
                    <div class="col-6"><label class="text-muted small">Phone</label><p class="mb-0">{{ $application->emergency_contact_phone }}</p></div>
                    <div class="col-6"><label class="text-muted small">Relationship</label><p class="mb-0">{{ $application->emergency_contact_relationship }}</p></div>
                    <div class="col-12"><label class="text-muted small">Address</label><p class="mb-0">{{ $application->emergency_contact_address }}</p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical & Lifestyle Information -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-heartbeat me-2" style="color: #cc0000;"></i>Health & Lifestyle</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><label class="text-muted small">Blood Group</label><p class="mb-0">{{ $application->blood_group ?: 'N/A' }}</p></div>
                    <div class="col-6"><label class="text-muted small">Genotype</label><p class="mb-0">{{ $application->genotype ?: 'N/A' }}</p></div>
                    <div class="col-6"><label class="text-muted small">Smoking Status</label><p class="mb-0"><span class="badge {{ $application->smoking_status == 'smoker' ? 'bg-danger' : 'bg-success' }}">{{ ucfirst($application->smoking_status) }}</span></p></div>
                    <div class="col-6"><label class="text-muted small">Vaccination Status</label><p class="mb-0">{{ $application->vaccination_status ?: 'N/A' }}</p></div>
                    <div class="col-6"><label class="text-muted small">Insurance Info</label><p class="mb-0">{{ $application->insurance_info ?: 'N/A' }}</p></div>
                    <div class="col-6"><label class="text-muted small">Preferred Hospital</label><p class="mb-0">{{ $application->preferred_hospital ?: 'N/A' }}</p></div>
                    @if($application->medical_conditions)<div class="col-12"><label class="text-muted small">Medical Conditions</label><p class="mb-0">{{ $application->medical_conditions }}</p></div>@endif
                    @if($application->allergies)<div class="col-12"><label class="text-muted small">Allergies</label><p class="mb-0">{{ $application->allergies }}</p></div>@endif
                    @if($application->physical_restrictions)<div class="col-12"><label class="text-muted small">Physical Restrictions</label><p class="mb-0 text-danger small">{{ $application->physical_restrictions }}</p></div>@endif
                    @if($application->has_disability)<div class="col-12"><div class="alert alert-warning mb-0 p-2 small"><strong>Disability:</strong> {{ $application->disability_details ?? 'Yes' }}</div></div>@endif
                </div>
            </div>
        </div>
    </div>

    <!-- Documents -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2" style="color: #cc0000;"></i>Submitted Documents</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($application->passport_photo)
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-between">
                            <i class="fas fa-user-circle fa-2x text-primary mb-2"></i>
                            <p class="small mb-2 fw-semibold">Passport Photo</p>
                            <a href="{{ asset($application->passport_photo) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                    @endif
                    @if($application->applicationform_receipt)
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-between">
                            <i class="fas fa-file-invoice-dollar fa-2x text-success mb-2"></i>
                            <p class="small mb-2 fw-semibold">App. Receipt</p>
                            <a href="{{ asset($application->applicationform_receipt) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                    @endif
                    @if($application->hostelfee_receipt)
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-between">
                            <i class="fas fa-receipt fa-2x text-success mb-2"></i>
                            <p class="small mb-2 fw-semibold">Hostel Receipt</p>
                            <a href="{{ asset($application->hostelfee_receipt) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                    @endif
                    @if($application->medical_report)
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-between">
                            <i class="fas fa-file-medical fa-2x text-info mb-2"></i>
                            <p class="small mb-2 fw-semibold">Medical Report</p>
                            <a href="{{ asset($application->medical_report) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                    @endif
                    @if($application->birth_certificate)
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-between">
                            <i class="fas fa-child fa-2x text-warning mb-2"></i>
                            <p class="small mb-2 fw-semibold">Birth Cert.</p>
                            <a href="{{ asset($application->birth_certificate) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                    @endif
                    @if($application->admission_letter)
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-between">
                            <i class="fas fa-university fa-2x text-dark mb-2"></i>
                            <p class="small mb-2 fw-semibold">Admission Letter</p>
                            <a href="{{ asset($application->admission_letter) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Notes -->
    @if($application->admin_notes)
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-sticky-note me-2" style="color: #cc0000;"></i>Admin Notes</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">{{ $application->admin_notes }}</div>
            </div>
        </div>
    </div>
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
            <form method="POST" action="{{ route('applications.update-status', $application) }}">
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
                        <textarea name="admin_notes" class="form-control" rows="3">{{ $application->admin_notes }}</textarea>
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
function updateStatus(status) {
    document.getElementById('status').value = status;
    new bootstrap.Modal(document.getElementById('statusUpdateModal')).show();
}
</script>
@endpush
