@extends('layouts.admin')

@section('page-title', 'Occupancy Report')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Occupancy</li>
            </ol>
        </nav>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0"><i class="fas fa-bed me-2" style="color: #cc0000;"></i>Occupancy Report</h5>
        <small class="text-muted">Room and bed occupancy across all hostels</small>
    </div>
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Print Report
    </button>
</div>

<!-- Overall Summary -->
<div class="row g-3 mb-4">
    @php
        $totalCapacity = collect($stats)->sum('total_capacity');
        $totalOccupied = collect($stats)->sum('total_occupied');
        $overallRate = $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 1) : 0;
    @endphp
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #cc0000 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Total Capacity</p>
                <h4 class="fw-bold mb-0">{{ $totalCapacity }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Occupied</p>
                <h4 class="fw-bold mb-0">{{ $totalOccupied }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #17a2b8 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Available</p>
                <h4 class="fw-bold mb-0">{{ $totalCapacity - $totalOccupied }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Occupancy Rate</p>
                <h4 class="fw-bold mb-0">{{ $overallRate }}%</h4>
            </div>
        </div>
    </div>
</div>

<!-- Hostel-wise Breakdown -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold"><i class="fas fa-building me-2" style="color: #cc0000;"></i>Hostel-wise Occupancy</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Hostel</th>
                    <th>Total Rooms</th>
                    <th>Capacity</th>
                    <th>Occupied</th>
                    <th>Available</th>
                    <th>Occupancy Rate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats as $hostelId => $data)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $data['name'] }}</td>
                    <td>{{ $data['total_rooms'] }}</td>
                    <td>{{ $data['total_capacity'] }}</td>
                    <td class="text-success">{{ $data['total_occupied'] }}</td>
                    <td class="text-info">{{ $data['available'] }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar {{ $data['occupancy_rate'] > 80 ? 'bg-success' : ($data['occupancy_rate'] > 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $data['occupancy_rate'] }}%"></div>
                            </div>
                            <span class="small fw-semibold">{{ $data['occupancy_rate'] }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No hostels found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Room Details by Hostel -->
@foreach($hostels as $hostel)
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">{{ $hostel->name }} - Room Details</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Room</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Occupied</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hostel->rooms as $room)
                <tr>
                    <td class="ps-3">{{ $room->room_number }}</td>
                    <td>{{ ucfirst($room->room_type ?? 'Standard') }}</td>
                    <td>{{ $room->capacity }}</td>
                    <td>{{ $room->students_count }}</td>
                    <td>
                        @if($room->students_count >= $room->capacity)
                            <span class="badge bg-danger">Full</span>
                        @elseif($room->students_count > 0)
                            <span class="badge bg-warning text-dark">Partial</span>
                        @else
                            <span class="badge bg-success">Available</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<style>
    @media print {
        .sidebar, .top-header, .breadcrumb, button { display: none !important; }
        .main-content { margin-left: 0 !important; }
        .card { break-inside: avoid; }
    }
</style>
@endpush
