@extends('layouts.student')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Browse Hostels</h2>
            <p class="text-muted">Find your perfect accommodation</p>
        </div>
        <div class="col-md-6">
            <form action="{{ route('student.hostels.index') }}" method="GET" class="card shadow-sm border-0">
                <div class="card-body p-2">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control border-0 bg-light" 
                                   placeholder="Search by name..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="type" class="form-select border-0 bg-light">
                                <option value="">All Types</option>
                                <option value="male" {{ request('type') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ request('type') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="mixed" {{ request('type') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 h-100">Search</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hostels Grid -->
    <div class="row g-4">
        @forelse($hostels as $hostel)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="position-relative">
                        @if($hostel->image_path)
                            <img src="{{ asset('storage/' . $hostel->image_path) }}" class="card-img-top" alt="{{ $hostel->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-secondary d-flex align-items-center justify-content-center text-white" style="height: 200px;">
                                <i class="fas fa-building fa-3x"></i>
                            </div>
                        @endif
                        <span class="position-absolute top-0 end-0 m-3 badge bg-white text-dark shadow-sm">
                            {{ ucfirst($hostel->type) }}
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-1">{{ $hostel->name }}</h5>
                        <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt me-1"></i>{{ $hostel->address }}</p>
                        
                        <p class="card-text text-muted mb-4">
                            {{ Str::limit($hostel->description, 100) }}
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                            <div>
                                <span class="d-block fw-bold text-primary">{{ $hostel->rooms_count }} Rooms</span>
                                <small class="text-muted">Starting from ₦{{ number_format($hostel->min_price, 2) }}</small>
                            </div>
                            <a href="{{ route('student.hostels.show', $hostel) }}" class="btn btn-primary px-4">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-building fa-3x text-muted opacity-50"></i>
                </div>
                <h4 class="text-muted">No hostels found matching your criteria.</h4>
                <a href="{{ route('student.hostels.index') }}" class="btn btn-outline-primary mt-3">View All Hostels</a>
            </div>
        @endforelse
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>
@endsection
