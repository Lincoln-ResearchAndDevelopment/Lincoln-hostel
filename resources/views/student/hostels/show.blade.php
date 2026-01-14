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

    <!-- Available Rooms -->
    <h4 class="fw-bold mb-4">Available Rooms</h4>
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
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Floor</small>
                            <span class="fw-medium">{{ $room->floor_number }}</span>
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

                        <div class="d-flex justify-content-between align-items-end mb-4">
                            <div>
                                <small class="text-muted d-block">Price per Semester</small>
                                <span class="fs-4 fw-bold text-primary">₵{{ $room->formatted_price_per_semester }}</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Capacity</small>
                                <span class="d-block">{{ $room->occupied }} / {{ $room->capacity }}</span>
                            </div>
                        </div>

                        <form action="{{ route('student.rooms.book', $room) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Confirm booking for Room {{ $room->room_number }}? This will be assigned to your profile immediately.')">
                                Book This Room
                            </button>
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
</div>

<style>
.room-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    transition: box-shadow 0.3s ease;
}
</style>
@endsection
