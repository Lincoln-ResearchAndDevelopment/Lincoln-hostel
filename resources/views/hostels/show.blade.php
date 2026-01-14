@extends('layouts.admin')

@section('page-title', $hostel->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hostels.index') }}">Hostels</a></li>
                <li class="breadcrumb-item active">{{ $hostel->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4">
    <!-- Hostel Details Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
                @if($hostel->image_path)
                    <img src="{{ asset('storage/' . $hostel->image_path) }}" class="card-img-top" alt="{{ $hostel->name }}" style="height: 250px; object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="fas fa-building fa-4x text-muted"></i>
                    </div>
                @endif
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">{{ $hostel->name }}</h4>
                        {!! $hostel->status_badge !!}
                    </div>
                    
                    <div class="mb-3">
                        <span class="badge bg-secondary">{{ $hostel->code }}</span>
                        {!! $hostel->type_badge !!}
                    </div>

                    @if($hostel->address)
                        <div class="mb-3">
                            <h6 class="text-muted text-uppercase small fw-bold">Address</h6>
                            <p class="mb-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $hostel->address }}</p>
                        </div>
                    @endif

                    @if($hostel->description)
                        <div class="mb-3">
                            <h6 class="text-muted text-uppercase small fw-bold">Description</h6>
                            <p class="text-muted mb-0">{{ $hostel->description }}</p>
                        </div>
                    @endif

                    <hr>

                <div class="d-grid gap-2">
                    <a href="{{ route('hostels.edit', $hostel) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Hostel
                    </a>
                    <a href="{{ route('hostels.rooms', $hostel) }}" class="btn btn-outline-primary">
                        <i class="fas fa-door-open me-2"></i>Manage Rooms
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Column -->
    <div class="col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card text-center h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #cc0000 0%, #a30000 100%);">
                    <div class="card-body text-white">
                        <div class="display-5 fw-bold mb-1">{{ $stats['total_rooms'] }}</div>
                        <div class="small text-white-50">Total Rooms</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100 border-0 shadow-sm bg-success">
                    <div class="card-body text-white">
                        <div class="display-5 fw-bold mb-1">{{ $stats['total_capacity'] }}</div>
                        <div class="small text-white-50">Total Capacity</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100 border-0 shadow-sm bg-info">
                    <div class="card-body text-white">
                        <div class="display-5 fw-bold mb-1">{{ $hostel->occupancy_rate }}%</div>
                        <div class="small text-white-50">Occupancy Rate</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Stats -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-chart-bar me-2" style="color: #cc0000;"></i>Detailed Statistics</h6>
            </div>
                <div class="card-body">
                    <div class="row g-4 text-center">
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-success mb-1">{{ $stats['available_rooms'] }}</h3>
                                <small class="text-muted fw-bold">Available Rooms</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-danger mb-1">{{ $stats['occupied_rooms'] }}</h3>
                                <small class="text-muted fw-bold">Full Rooms</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-warning mb-1">{{ $stats['maintenance_rooms'] }}</h3>
                                <small class="text-muted fw-bold">Maintenance</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-primary mb-1">{{ $stats['occupied_spaces'] }}</h3>
                                <small class="text-muted fw-bold">Active Students</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Students -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-users me-2" style="color: #cc0000;"></i>Recent Students</h6>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Name</th>
                                    <th>Room</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hostel->students()->latest()->take(5)->get() as $student)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-2 bg-primary text-white">
                                                    {{ substr($student->first_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                    <small class="text-muted">{{ $student->admission_number }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($student->room)
                                                <span class="badge bg-light text-dark border">{{ $student->room->room_number }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($student->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $student->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                            No students currently assigned to this hostel.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>
@endsection
