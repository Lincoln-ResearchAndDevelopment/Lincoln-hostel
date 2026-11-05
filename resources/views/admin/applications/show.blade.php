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
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#approveAssignModal">
                    <i class="fas fa-user-check me-1"></i> Approve & Assign Room
                </button>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times me-1"></i> Reject
                </button>
                @elseif($application->status === 'approved')
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-menu-item px-3 text-danger small" href="#" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject (Reverse Approval)</a></li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @if($application->status === 'rejected')
    <div class="col-12">
        <div class="alert alert-danger border-0 shadow-sm mb-0">
            <h6 class="alert-heading fw-bold"><i class="fas fa-exclamation-circle me-2"></i>Application Rejected</h6>
            <p class="mb-0"><strong>Reason:</strong> {{ $application->rejection_reason }}</p>
        </div>
    </div>
    @endif

    <!-- Student Information -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-user-graduate me-2" style="color: #cc0000;"></i>Student Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><label class="text-muted small">Full Name</label><p class="mb-0 fw-semibold">{{ $application->full_name }}</p></div>
                    <div class="col-6"><label class="text-muted small">Admission/Reg ID</label><p class="mb-0 fw-semibold">{{ $application->student_id }}</p></div>
                    <div class="col-6"><label class="text-muted small">Campus</label><p class="mb-0 fw-semibold">{{ optional($application->campus)->name }}</p></div>
                    <div class="col-6"><label class="text-muted small">Gender</label><p class="mb-0"><span class="badge bg-light text-dark border">{{ ucfirst($application->gender) }}</span></p></div>
                    <div class="col-6"><label class="text-muted small">Date of Birth</label><p class="mb-0">{{ $application->date_of_birth->format('M d, Y') }}</p></div>
                    <div class="col-6"><label class="text-muted small">Phone</label><p class="mb-0">{{ $application->phone_number }}</p></div>
                    <div class="col-6"><label class="text-muted small">Email</label><p class="mb-0">{{ $application->email }}</p></div>
                    <div class="col-12"><label class="text-muted small">Home Address</label><p class="mb-0">{{ $application->home_address }}</p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic & Preferences -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-graduation-cap me-2" style="color: #cc0000;"></i>Academic & Preferences</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><label class="text-muted small">Program</label><p class="mb-0">{{ $application->program }}</p></div>
                    <div class="col-6"><label class="text-muted small">Department</label><p class="mb-0">{{ $application->department }}</p></div>
                    <div class="col-6"><label class="text-muted small">Intake</label><p class="mb-0 fw-semibold">{{ $application->intake }}</p></div>
                    <div class="col-6"><label class="text-muted small">Academic Year</label><p class="mb-0">{{ $application->academic_year }}</p></div>
                    <div class="col-12">
                        @php
                            $paidHostel = $application->preferredHostel;
                            $expected = $application->expectedFee();
                            $fmt = fn ($n) => '₦' . number_format($n, 2);
                        @endphp
                        <label class="text-muted small">Hostel &amp; room size the applicant paid for</label>
                        @if($paidHostel)
                            <p class="mb-0 fw-semibold">
                                {{ $paidHostel->name }} <span class="badge bg-info text-dark">{{ ucfirst($paidHostel->type) }}</span>
                                @if($application->preferred_room_capacity)
                                    · {{ $application->preferred_room_capacity == 1 ? '1 person per room' : $application->preferred_room_capacity . ' persons per room' }}
                                @endif
                            </p>
                            @if($paidHostel->address)<p class="small text-muted mb-0"><i class="fas fa-map-marker-alt me-1"></i>{{ $paidHostel->address }}</p>@endif
                            @if($expected)
                                <p class="small mb-0 text-success">
                                    Expected hostel fee ({{ $expected['plan'] === 'full_year' ? 'full year' : 'per semester' }}):
                                    <strong>{{ $expected['min'] == $expected['max'] ? $fmt($expected['min']) : $fmt($expected['min']) . ' – ' . $fmt($expected['max']) }}</strong>
                                    — compare with the hostel fee receipt.
                                </p>
                            @endif
                        @else
                            <p class="mb-0 text-muted">Not recorded (application made before the hostel choice was added).</p>
                        @endif
                    </div>
                    <div class="col-6">
                        <label class="text-muted small">Room Preference</label>
                        <p class="mb-0">
                            <span class="badge bg-secondary-subtle text-secondary border">
                                {{ ucfirst($application->preferred_room_type ?: 'No Preference') }}
                            </span>
                        </p>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small">Payment Proof</label>
                        <p class="mb-0 fw-bold text-success">Verified: {{ $application->amount_paid_display }}</p>
                    </div>
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
                @php $payCampus = $application->campus; @endphp
                @if($payCampus)
                <div class="alert alert-light border small mb-3">
                    <strong>Check the receipts against {{ $payCampus->name }}'s accounts:</strong>
                    @php
                        $scParts = array_filter([
                            $payCampus->service_charge_amount !== null ? '₦' . number_format($payCampus->service_charge_amount, 2) . ' per semester' : null,
                            $payCampus->service_charge_amount_year !== null ? '₦' . number_format($payCampus->service_charge_amount_year, 2) . ' per year' : null,
                        ]);
                    @endphp
                    <div>Service charge{{ $scParts ? ' (' . implode(' / ', $scParts) . ')' : '' }}:
                        {{ $payCampus->service_charge_bank_name }}, <span class="font-monospace">{{ $payCampus->service_charge_account_number }}</span> ({{ $payCampus->service_charge_account_name }})</div>
                    <div>Hostel fee: {{ $payCampus->bank_name }}, <span class="font-monospace">{{ $payCampus->account_number }}</span> ({{ $payCampus->account_name }})</div>
                </div>
                @endif
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
                            <p class="small mb-2 fw-semibold">Service Charge Receipt</p>
                            <a href="{{ asset($application->applicationform_receipt) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                    @endif
                    @if($application->hostelfee_receipt)
                    <div class="col-md-2 col-6">
                        <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-between">
                            <i class="fas fa-receipt fa-2x text-success mb-2"></i>
                            <p class="small mb-2 fw-semibold">Hostel Fee Receipt</p>
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

<!-- MODALS -->

<!-- Approve & Assign Modal -->
<div class="modal fade" id="approveAssignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-user-check me-2"></i>Approve & Assign Bed Space
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('applications.approve-assign', $application) }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info small mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Select a hostel, room, and specific bed to complete the allocation.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">1. Select Hostel</label>
                            <select id="hostel_select" name="hostel_id" class="form-select select2" required>
                                <option value="">-- Choose Hostel --</option>
                                @foreach($hostels as $hostel)
                                    @if(in_array(strtolower($hostel->type), [strtolower($application->gender), 'mixed'], true))
                                        <option value="{{ $hostel->id }}" {{ (int) $application->preferred_hostel_id === (int) $hostel->id ? 'selected' : '' }}>{{ $hostel->name }} ({{ ucfirst($hostel->type) }}){{ $hostel->address ? ' – ' . $hostel->address : '' }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="text-muted">Hostels for {{ $application->gender }} students, and mixed hostels, are shown.
                                @if($application->preferredHostel) The applicant paid for <strong>{{ $application->preferredHostel->name }}</strong>@if($application->preferred_room_capacity), {{ $application->preferred_room_capacity }} per room @endif.@endif</small>
                            @if($application->preferredHostel)
                                <div id="hostel_mismatch" class="alert alert-warning small mt-2 mb-0 d-none">
                                    The applicant paid for <strong>{{ $application->preferredHostel->name }}</strong>. A different hostel can have a different fee, so check the price before allocating.
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">2. Select Room</label>
                            <select id="room_select" name="room_id" class="form-select" disabled required>
                                <option value="">-- Choose Hostel First --</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold small text-uppercase mb-3">3. Choose Available Bed Space</label>
                            <div id="bed_selection_container" class="row g-2">
                                <div class="col-12 text-center py-4 bg-light rounded border border-dashed">
                                    <p class="text-muted mb-0">Select a room to see available beds</p>
                                </div>
                            </div>
                            <input type="hidden" name="bed_id" id="selected_bed_id" required>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold small text-uppercase">Admin Notes (Optional)</label>
                            <textarea name="admin_notes" class="form-control" rows="3" placeholder="Any specific notes for this allocation..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="submit_assign_btn" disabled>
                        Confirm Allocation & Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold">Reject Application</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('applications.reject', $application) }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required 
                                  placeholder="Provide a clear reason (e.g., Invalid payment proof, Incomplete documents, etc.)"></textarea>
                        <small class="text-muted">This reason will be visible to the student on their dashboard.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hostelSelect = document.getElementById('hostel_select');
    const roomSelect = document.getElementById('room_select');
    const bedContainer = document.getElementById('bed_selection_container');
    const selectedBedInput = document.getElementById('selected_bed_id');
    const submitBtn = document.getElementById('submit_assign_btn');

    // Load Rooms when Hostel changes
    hostelSelect.addEventListener('change', function() {
        const hostelId = this.value;
        roomSelect.disabled = true;
        roomSelect.innerHTML = '<option value="">Loading rooms...</option>';
        bedContainer.innerHTML = '<div class="col-12 text-center py-4 bg-light rounded border border-dashed"><p class="text-muted mb-0">Select a room to see available beds</p></div>';
        selectedBedInput.value = '';
        submitBtn.disabled = true;

        if (!hostelId) {
            roomSelect.innerHTML = '<option value="">-- Choose Hostel First --</option>';
            return;
        }

        fetch(`/applications/available-rooms/${hostelId}`)
            .then(response => response.json())
            .then(rooms => {
                roomSelect.disabled = false;
                roomSelect.innerHTML = '<option value="">-- Choose Room --</option>';
                rooms.forEach(room => {
                    roomSelect.innerHTML += `<option value="${room.id}">${room.room_number} (${room.room_type}, sleeps ${room.capacity} - ${room.occupied}/${room.capacity} taken - ₦${Number(room.price_per_semester).toLocaleString()}/sem, ₦${Number(room.price_per_year).toLocaleString()}/yr)</option>`;
                });
            });
    });

    // Preselected hostel (the one the applicant paid for): load its rooms straight away,
    // and warn if the admin picks a different hostel.
    const preferredHostelId = @json($application->preferred_hostel_id ? (string) $application->preferred_hostel_id : null);
    const mismatch = document.getElementById('hostel_mismatch');
    hostelSelect.addEventListener('change', function () {
        if (mismatch) mismatch.classList.toggle('d-none', !preferredHostelId || !this.value || this.value === preferredHostelId);
    });
    if (hostelSelect.value) hostelSelect.dispatchEvent(new Event('change'));

    // Load Beds when Room changes
    roomSelect.addEventListener('change', function() {
        const roomId = this.value;
        bedContainer.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Loading beds...</div>';
        selectedBedInput.value = '';
        submitBtn.disabled = true;

        if (!roomId) {
            bedContainer.innerHTML = '<div class="col-12 text-center py-4 bg-light rounded border border-dashed"><p class="text-muted mb-0">Select a room to see available beds</p></div>';
            return;
        }

        fetch(`/applications/available-beds/${roomId}`)
            .then(response => response.json())
            .then(beds => {
                bedContainer.innerHTML = '';
                if (beds.length === 0) {
                    bedContainer.innerHTML = '<div class="col-12 alert alert-warning small">No available beds in this room.</div>';
                    return;
                }

                beds.forEach(bed => {
                    bedContainer.innerHTML += `
                        <div class="col-md-3 col-6 mb-2">
                            <div class="bed-item p-3 border rounded text-center cursor-pointer hover-shadow transition-all h-100" 
                                 data-bed-id="${bed.id}" onclick="selectBed(this, ${bed.id})">
                                <i class="fas fa-bed fa-2x mb-2 text-secondary"></i>
                                <div class="fw-bold small">${bed.bed_number}</div>
                                <div class="text-success extra-small" style="font-size: 0.65rem;">AVAILABLE</div>
                            </div>
                        </div>
                    `;
                });
            });
    });
});

function selectBed(element, bedId) {
    // Deselect others
    document.querySelectorAll('.bed-item').forEach(el => {
        el.classList.remove('border-primary', 'bg-primary-subtle', 'shadow-sm');
        el.querySelector('i').classList.replace('text-primary', 'text-secondary');
    });

    // Select this one
    element.classList.add('border-primary', 'bg-primary-subtle', 'shadow-sm');
    element.querySelector('i').classList.replace('text-secondary', 'text-primary');
    
    document.getElementById('selected_bed_id').value = bedId;
    document.getElementById('submit_assign_btn').disabled = false;
}
</script>

<style>
.cursor-pointer { cursor: pointer; }
.hover-shadow:hover { shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
.transition-all { transition: all 0.2s ease; }
.extra-small { font-size: 0.7rem; }
.bed-item { background: #fff; }
.bed-item.border-primary { border-width: 2px !important; }
</style>
@endpush
