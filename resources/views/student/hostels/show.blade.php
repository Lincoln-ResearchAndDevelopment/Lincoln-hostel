@extends('layouts.student')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('student.hostels.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Hostels
        </a>
    </div>

    <!-- Hostel Info Banner -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="row g-0">
            <div class="col-md-5 position-relative">
                @if($hostel->image_path)
                    <img src="{{ asset('storage/' . $hostel->image_path) }}" class="img-fluid h-100 object-fit-cover" alt="{{ $hostel->name }}" style="min-height: 300px;">
                @else
                    <div class="bg-secondary h-100 d-flex align-items-center justify-content-center text-white" style="min-height: 300px;">
                        <i class="fas fa-building fa-4x"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-7">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h2 class="fw-bold mb-0">{{ $hostel->name }}</h2>
                        <span class="badge bg-primary fs-6">{{ ucfirst($hostel->type) }} Hostel</span>
                    </div>
                    
                    <p class="text-muted mb-4"><i class="fas fa-map-marker-alt me-2 text-danger"></i>{{ $hostel->address }}</p>
                    
                    <h5 class="fw-bold mb-3">About this Hostel</h5>
                    <p class="text-muted mb-4">{{ $hostel->description }}</p>
                    
                    <div class="row g-3">
                        <div class="col-auto">
                            <div class="d-flex align-items-center text-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <span class="fw-bold">{{ $rooms->count() }} Available Rooms</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex align-items-center text-info">
                                <i class="fas fa-shield-alt me-2"></i>
                                <span>24/7 Security</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex align-items-center text-info">
                                <i class="fas fa-wifi me-2"></i>
                                <span>Free WiFi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter & Section Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h4 class="fw-bold mb-0">Available Rooms</h4>
        
        <form action="{{ route('student.hostels.show', $hostel) }}" method="GET" class="d-flex gap-2">
            <select name="room_type" class="form-select border-0 shadow-sm" style="min-width: 180px;" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <option value="single" {{ request('room_type') == 'single' ? 'selected' : '' }}>Single Room</option>
                <option value="double" {{ request('room_type') == 'double' ? 'selected' : '' }}>Double Room</option>
                <option value="triple" {{ request('room_type') == 'triple' ? 'selected' : '' }}>Triple Room</option>
                <option value="quad" {{ request('room_type') == 'quad' ? 'selected' : '' }}>Quad Room</option>
                <option value="dormitory" {{ request('room_type') == 'dormitory' ? 'selected' : '' }}>Dormitory</option>
            </select>
            @if(request('room_type'))
                <a href="{{ route('student.hostels.show', $hostel) }}" class="btn btn-light border shadow-sm">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>
    <div class="row g-4">
        @forelse($rooms as $room)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm room-card">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Room {{ $room->room_number }}</h5>
                            <span class="badge bg-light text-dark border">{{ $room->room_type_display }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <small class="text-muted d-block mb-1">Floor</small>
                                <span class="fw-medium">Floor {{ $room->floor_number }}</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block mb-1">Occupancy</small>
                                @php
                                    $remaining = $room->capacity - $room->occupied;
                                    $percentage = $room->capacity > 0 ? ($room->occupied / $room->capacity) * 100 : 0;
                                @endphp
                                <div class="d-flex flex-column align-items-end">
                                    <span class="badge {{ $remaining > 0 ? 'bg-success' : 'bg-danger' }} mb-1">
                                        {{ $room->occupied }} occupied, {{ $remaining }} left
                                    </span>
                                    <div class="progress" style="width: 100px; height: 6px;">
                                        <div class="progress-bar {{ $percentage >= 100 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success') }}" 
                                             role="progressbar" 
                                             style="width: {{ min($percentage, 100) }}%" 
                                             aria-valuenow="{{ $room->occupied }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="{{ $room->capacity }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <small class="text-muted d-block mb-2">Facilities</small>
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($room->facilities as $facility)
                                    <span class="badge bg-light text-secondary border">{{ $facility }}</span>
                                @empty
                                    <small class="text-muted fst-italic">Standard details</small>
                                @endforelse
                            </div>
                        </div>

                        <form action="{{ route('student.rooms.book', $room) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="text-muted small d-block mb-2">Select Payment Plan</label>
                                <div class="btn-group w-100" role="group" aria-label="Payment Plan Selection">
                                    <input type="radio" class="btn-check" name="payment_plan" id="plan_semester_{{ $room->id }}" value="semester" checked>
                                    <label class="btn btn-outline-primary" for="plan_semester_{{ $room->id }}">
                                        <i class="fas fa-calendar-alt d-block mb-1"></i>
                                        Semester<br>
                                        <span class="fw-bold">₦{{ $room->formatted_price_per_semester }}</span>
                                    </label>

                                    <input type="radio" class="btn-check" name="payment_plan" id="plan_year_{{ $room->id }}" value="year">
                                    <label class="btn btn-outline-primary" for="plan_year_{{ $room->id }}">
                                        <i class="fas fa-history d-block mb-1"></i>
                                        Full Year<br>
                                        <span class="fw-bold">₦{{ $room->formatted_price_per_year }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-secondary py-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#roomModal{{ $room->id }}">
                                    <i class="fas fa-info-circle me-2"></i>View Room Details
                                </button>
                                <button type="submit" class="btn btn-primary py-3 fw-bold">
                                    <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-door-closed fa-3x text-muted opacity-50 mb-3"></i>
                <h5 class="text-muted">No rooms currently available in this hostel.</h5>
                <p class="text-muted">Please check back later or view other hostels.</p>
            </div>
        @endforelse
    </div>

    @foreach($rooms as $room)
    <!-- Room Details Modal -->
    <div class="modal fade" id="roomModal{{ $room->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Room {{ $room->room_number }} Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4 p-3 bg-light rounded-3">
                        <i class="fas fa-bed fa-3x text-primary mb-2"></i>
                        <h4 class="fw-bold mb-0">{{ $room->room_type_display }}</h4>
                        <span class="text-muted">Floor {{ $room->floor_number }}</span>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center">
                                <small class="text-muted d-block mb-1">Capacity</small>
                                <span class="fw-bold fs-5">{{ $room->capacity }} Person(s)</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded-3 text-center">
                                <small class="text-muted d-block mb-1">Available</small>
                                <span class="fw-bold fs-5 text-success">{{ $room->capacity - $room->occupied }} Slot(s)</span>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3"><i class="fas fa-star me-2 text-warning"></i>Room Amenities</h6>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @forelse($room->facilities as $facility)
                            <div class="px-3 py-2 bg-white border rounded-pill small d-flex align-items-center">
                                <i class="fas fa-check text-success me-2"></i>{{ $facility }}
                            </div>
                        @empty
                            <span class="text-muted fst-italic">Standard amenities provided</span>
                        @endforelse
                    </div>

                    @if($room->description)
                        <h6 class="fw-bold mb-2">Description</h6>
                        <p class="text-muted small mb-4">{{ $room->description }}</p>
                    @endif

                    <div class="alert alert-info border-0 bg-light p-3 mb-0">
                        <div class="d-flex">
                            <i class="fas fa-info-circle me-2 mt-1"></i>
                            <div class="small">
                                <strong>Note:</strong> Final room allocation is subject to administrative approval after payment.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<style>
.room-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    transition: box-shadow 0.3s ease;
}
</style>
@endsection
