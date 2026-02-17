@extends('layouts.student')

@section('page-title', 'My Profile')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">My Profile</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Profile Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                             style="width: 100px; height: 100px; font-size: 2.5rem; background: linear-gradient(135deg, #cc0000 0%, #a30000 100%);">
                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="col">
                        <h3 class="mb-1">{{ $student->full_name }}</h3>
                        <p class="text-muted mb-2">{{ $student->admission_number }}</p>
                        <span class="badge {{ $student->status == 'active' ? 'bg-success' : 'bg-danger' }} me-2">
                            {{ ucfirst($student->status) }}
                        </span>
                        @if($student->room_id)
                            <span class="badge" style="background-color: #cc0000;">
                                Room {{ $student->room->room_number }} - {{ $student->room->hostel->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Personal Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-user me-2" style="color: #cc0000;"></i>Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">First Name</small>
                            <span class="fw-bold">{{ $student->first_name }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Last Name</small>
                            <span class="fw-bold">{{ $student->last_name }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Other Names</small>
                            <span class="fw-bold">{{ $student->other_names ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Email</small>
                            <span class="fw-bold">{{ $student->email }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Phone Number</small>
                            <span class="fw-bold">{{ $student->contact_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Gender</small>
                            <span class="fw-bold">{{ ucfirst($student->gender ?? 'N/A') }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Date of Birth</small>
                            <span class="fw-bold">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Nationality</small>
                            <span class="fw-bold">{{ $student->nationality ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">State of Origin</small>
                            <span class="fw-bold">{{ $student->state_of_origin ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Home Address</small>
                            <span class="fw-bold">{{ $student->address ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Academic Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2" style="color: #cc0000;"></i>Academic Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Student ID / Admission No</small>
                            <span class="fw-bold">{{ $student->admission_number }}</span>
                        </div>
                    </div>
                    @if($student->application)
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Application Number</small>
                            <span class="fw-bold">{{ $student->application->application_number }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Department</small>
                            <span class="fw-bold">{{ $student->department }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Faculty</small>
                            <span class="fw-bold">{{ $student->faculty ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Level</small>
                            <span class="fw-bold">{{ $student->level ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Session</small>
                            <span class="fw-bold">{{ $student->session ?? '2025/2026' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Parent/Guardian Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-users me-2" style="color: #cc0000;"></i>Parent/Guardian Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Guardian Name</small>
                            <span class="fw-bold">{{ $student->parent_name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Relationship</small>
                            <span class="fw-bold">{{ ucfirst($student->parent_relationship ?? 'N/A') }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Guardian Phone</small>
                            <span class="fw-bold">{{ $student->parent_phone ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Guardian Email</small>
                            <span class="fw-bold">{{ $student->parent_email ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Guardian Occupation</small>
                            <span class="fw-bold">{{ $student->parent_occupation ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Guardian Address</small>
                            <span class="fw-bold">{{ $student->parent_address ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Emergency Contact -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-phone-alt me-2" style="color: #cc0000;"></i>Emergency Contact</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Contact Name</small>
                            <span class="fw-bold">{{ $student->emergency_contact ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Contact Phone</small>
                            <span class="fw-bold">{{ $student->emergency_contact ?? 'N/A' }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Medical Information -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-heartbeat me-2" style="color: #cc0000;"></i>Medical Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Blood Group</small>
                            <span class="fw-bold">{{ $student->blood_group ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Genotype</small>
                            <span class="fw-bold">{{ $student->genotype ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Medical Conditions</small>
                            <span class="fw-bold">{{ $student->medical_conditions ?? 'None' }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Allergies</small>
                            <span class="fw-bold">{{ $student->allergies ?? 'None' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hostel Information -->
@if($student->room_id)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-bed me-2" style="color: #cc0000;"></i>Hostel Allocation</h5>
                <a href="{{ route('student.room.details') }}" class="btn btn-sm btn-outline-primary">View Details</a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Hostel</small>
                            <span class="fw-bold">{{ $student->room->hostel->name }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Room Number</small>
                            <span class="fw-bold">{{ $student->room->room_number }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Bed Number</small>
                            <span class="fw-bold">{{ $student->bed_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block text-uppercase">Check-in Date</small>
                            <span class="fw-bold">{{ $student->check_in_date ? $student->check_in_date->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

