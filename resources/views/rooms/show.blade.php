@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Rooms
        </a>
    </div>

    <!-- Room Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-primary mb-2">Room {{ $room->room_number }}</span>
                            @if($room->hostel)
                                <h2 class="mb-1">{{ $room->hostel->name }}</h2>
                                <p class="text-muted"><i class="fas fa-map-pin me-1"></i>{{ $room->hostel->code }} • Floor {{ $room->floor_number }}</p>
                            @else
                                <h2 class="mb-1">Room {{ $room->room_number }}</h2>
                                <span class="badge bg-secondary">Unassigned Hostel</span>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="badge @if($room->is_available) bg-success @elseif($room->status == 'maintenance') bg-warning @else bg-danger @endif fs-6 px-3 py-2">
                                {{ ucfirst($room->status) }}
                            </span>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="text-muted small text-uppercase fw-bold">Room Type</div>
                            <div class="fs-5">{{ $room->room_type_display }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small text-uppercase fw-bold">Gender</div>
                            <div class="fs-5">{{ ucfirst($room->gender_type) }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small text-uppercase fw-bold">Capacity</div>
                            <div class="fs-5">{{ $room->capacity }} Students</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small text-uppercase fw-bold">Occupied</div>
                            <div class="fs-5 text-primary">{{ $room->occupied }} Students</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body p-4">
                    <h5 class="card-title text-muted mb-4"><i class="fas fa-tag me-2"></i>Pricing Details</h5>
                    
                    <div class="mb-3">
                        <label class="small text-muted text-uppercase fw-bold">Per Semester</label>
                        <div class="d-flex align-items-center text-success">
                            <span class="h2 mb-0">₦{{ $room->formatted_price_per_semester }}</span>
                        </div>
                    </div>

                    <div class="mb-4 border-top pt-3">
                        <label class="small text-muted text-uppercase fw-bold">Per Year</label>
                        <div class="d-flex align-items-center">
                            <span class="h4 mb-0 text-dark">₦{{ $room->formatted_price_per_year }}</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Room Description & Facilities -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Description & Facilities</h5>
                </div>
                <div class="card-body">
                    @if($room->description)
                        <div class="mb-4">
                            <p class="text-muted">{{ $room->description }}</p>
                        </div>
                    @else
                        <p class="text-muted fst-italic">No additional description provided.</p>
                    @endif

                    <h6 class="text-uppercase small fw-bold text-muted mb-3">Amenities</h6>
                    @if($room->facilities && count($room->facilities) > 0)
                        <div class="row g-2">
                            @foreach($room->facilities as $facility)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-2 rounded bg-light">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>{{ $facility }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light border">No specific facilities listed.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assigned Students -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Assigned Students</h5>
                    <span class="badge bg-secondary">{{ $room->occupied }} / {{ $room->capacity }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($room->students as $student)
                            <div class="list-group-item px-4 py-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                            {{ substr($student->first_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $student->first_name }} {{ $student->last_name }}</div>
                                            <small class="text-muted">{{ $student->email }}</small>
                                        </div>
                                    </div>
                                    <a href="{{ route('students.profile', $student) }}" class="btn btn-sm btn-outline-primary">Profile</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-user-slash text-muted fa-3x mb-3"></i>
                                <p class="text-muted mb-0">This room is currently empty.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
