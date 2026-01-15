@extends('layouts.admin')

@section('page-title')
    @if(request('type') == 'male')
        Boys Hostels
    @elseif(request('type') == 'female')
        Girls Hostels
    @else
        Hostels Management
    @endif
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Hostels</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #cc0000 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Total Hostels</p>
                <h4 class="fw-bold mb-0">{{ $stats['total_hostels'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Active</p>
                <h4 class="fw-bold mb-0">{{ $stats['active_hostels'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #17a2b8 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Total Rooms</p>
                <h4 class="fw-bold mb-0">{{ $stats['total_rooms'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Total Capacity</p>
                <h4 class="fw-bold mb-0">{{ $stats['total_capacity'] }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Actions Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search hostels...">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="male" {{ request('type') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('type') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="mixed" {{ request('type') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                </select>
            </div>
            <div class="col-md-5 text-end">
                <a href="{{ route('hostels.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Hostel
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Hostels Grid -->
<div class="row g-4">
    @forelse($hostels as $hostel)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            @if($hostel->image_path)
                <img src="{{ asset('storage/' . $hostel->image_path) }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="{{ $hostel->name }}">
            @else
                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 150px; background: linear-gradient(135deg, #cc0000 0%, #a30000 100%);">
                    <i class="fas fa-building fa-3x text-white"></i>
                </div>
            @endif
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title fw-bold mb-0">{{ $hostel->name }}</h6>
                    <span class="badge bg-secondary">{{ $hostel->code }}</span>
                </div>
                <div class="mb-2">{!! $hostel->type_badge !!} {!! $hostel->status_badge !!}</div>
                @if($hostel->address)
                <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($hostel->address, 40) }}</p>
                @endif
                <div class="row text-center g-2 mt-3 pt-3 border-top">
                    <div class="col-4">
                        <div class="fw-bold" style="color: #cc0000;">{{ $hostel->total_rooms }}</div>
                        <small class="text-muted">Rooms</small>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-success">{{ $hostel->total_capacity }}</div>
                        <small class="text-muted">Capacity</small>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-warning">{{ $hostel->occupancy_rate }}%</div>
                        <small class="text-muted">Occupied</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top">
                <div class="btn-group w-100">
                    <a href="{{ route('hostels.show', $hostel) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('hostels.rooms', $hostel) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-door-open"></i></a>
                    <a href="{{ route('hostels.edit', $hostel) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteHostel({{ $hostel->id }})"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5>No Hostels Found</h5>
                <p class="text-muted">Get started by creating your first hostel.</p>
                <a href="{{ route('hostels.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Hostel</a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($hostels->hasPages())
<div class="mt-4">{{ $hostels->links() }}</div>
@endif

<form id="deleteForm" method="POST" style="display: none;">@csrf @method('DELETE')</form>
@endsection

@push('scripts')
<script>
function deleteHostel(id) {
    if (confirm('Are you sure you want to delete this hostel?')) {
        document.getElementById('deleteForm').action = `/hostels/${id}`;
        document.getElementById('deleteForm').submit();
    }
}

document.getElementById('typeFilter').addEventListener('change', function() {
    const type = this.value;
    const url = new URL(window.location.href);
    if (type) {
        url.searchParams.set('type', type);
    } else {
        url.searchParams.delete('type');
    }
    window.location.href = url.toString();
});

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const search = this.value;
        const url = new URL(window.location.href);
        if (search) {
            url.searchParams.set('search', search);
        } else {
            url.searchParams.delete('search');
        }
        window.location.href = url.toString();
    }
});
</script>
@endpush
