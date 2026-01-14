@extends('layouts.student')

@section('page-title', 'Room & Hostel Details')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Room Details</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Room & Hostel Overview -->
<div class="row g-4 mb-4">
    <!-- Hostel Information -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header text-white" style="background-color: #cc0000;">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Hostel Information</h5>
            </div>
            <div class="card-body">
                @if($hostel->image_path)
                    <img src="{{ asset('storage/' . $hostel->image_path) }}" 
                         class="img-fluid rounded mb-3" 
                         alt="{{ $hostel->name }}"
                         style="max-height: 200px; width: 100%; object-fit: cover;">
                @endif
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Hostel Name</small>
                            <span class="fw-bold">{{ $hostel->name }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Hostel Code</small>
                            <span class="fw-bold">{{ $hostel->code }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Type</small>
                            <span class="fw-bold">{!! $hostel->type_badge !!}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Status</small>
                            <span class="fw-bold">{!! $hostel->status_badge !!}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Address</small>
                            <span class="fw-bold">{{ $hostel->address }}</span>
                        </div>
                    </div>
                    @if($hostel->description)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Description</small>
                            <span>{{ $hostel->description }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Room Information -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header text-white" style="background-color: #cc0000;">
                <h5 class="mb-0"><i class="fas fa-door-open me-2"></i>Room Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Room Number</small>
                            <span class="fw-bold fs-4" style="color: #cc0000;">{{ $room->room_number }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Floor</small>
                            <span class="fw-bold">{{ $room->floor_number ?? 'Ground' }} Floor</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Room Type</small>
                            <span class="fw-bold">{{ $room->room_type_display }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Capacity</small>
                            <span class="fw-bold">{{ $room->occupied }}/{{ $room->capacity }} Occupied</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Your Bed Number</small>
                            <span class="fw-bold">Bed #{{ $student->bed_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Gender Type</small>
                            <span class="fw-bold">{{ ucfirst($room->gender_type ?? 'Mixed') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Check-in Date</small>
                            <span class="fw-bold">{{ $student->formatted_check_in_date }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Expected Check-out</small>
                            <span class="fw-bold">{{ $student->formatted_check_out_date }}</span>
                        </div>
                    </div>
                </div>

                @if($room->facilities && count($room->facilities) > 0)
                <div class="mt-3">
                    <small class="text-muted d-block mb-2">Room Facilities</small>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($room->facilities as $facility)
                            <span class="badge bg-info">{{ $facility }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Roommates Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-users me-2" style="color: #cc0000;"></i>My Roommates</h5>
            </div>
            <div class="card-body">
                @if($roommates->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Contact</th>
                                    <th>Check-in Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roommates as $roommate)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; background-color: #cc0000;">
                                                {{ strtoupper(substr($roommate->full_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $roommate->full_name }}</strong>
                                                <br><small class="text-muted">{{ $roommate->admission_number }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $roommate->department }}</td>
                                    <td>{{ $roommate->contact_number ?? 'N/A' }}</td>
                                    <td>{{ $roommate->formatted_check_in_date }}</td>
                                    <td>{!! $roommate->status_badge !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Roommates Yet</h6>
                        <p class="text-muted mb-0">You currently have the room to yourself.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Room Images -->
@if($room->images && count($room->images) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-images me-2" style="color: #cc0000;"></i>Room Images</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($room->images as $image)
                    <div class="col-md-4">
                        <img src="{{ asset('storage/' . $image) }}" 
                             class="img-fluid rounded" 
                             alt="Room Image"
                             style="height: 200px; width: 100%; object-fit: cover;">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('student.hostel.rules') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-book fa-2x mb-2 d-block" style="color: #cc0000;"></i>
                            View Hostel Rules
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('student.complaints.index') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-exclamation-circle fa-2x mb-2 d-block" style="color: #cc0000;"></i>
                            Report Issue
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('student.leave.create') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-calendar-alt fa-2x mb-2 d-block" style="color: #cc0000;"></i>
                            Request Leave
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('student.fees.index') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-money-bill fa-2x mb-2 d-block" style="color: #cc0000;"></i>
                            View Fees
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection