@extends('layouts.admin')

@section('page-title', 'Manage Rooms - ' . $hostel->name)

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('hostels.show', $hostel) }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Hostel
            </a>
            <h4 class="d-inline-block align-middle mb-0 text-muted">| {{ $hostel->name }} Rooms</h4>
        </div>
        <a href="{{ route('rooms.create', ['hostel_id' => $hostel->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Room
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Room Number</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Price (Sem/Year)</th>
                            <th>Status</th>
                            <th>Occupancy</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            <tr>
                                <td class="ps-4 fw-bold">
                                    {{ $room->room_number }}
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ ucfirst($room->room_type) }}</span>
                                </td>
                                <td>
                                    {{ $room->capacity }} Students
                                </td>
                                <td>
                                    <div><small class="text-muted">Sem:</small> ₦{{ number_format($room->price_per_semester, 2) }}</div>
                                    <div><small class="text-muted">Year:</small> ₦{{ number_format($room->price_per_year, 2) }}</div>
                                </td>
                                <td>
                                    @if($room->status == 'available')
                                        <span class="badge bg-success">Available</span>
                                    @elseif($room->status == 'full')
                                        <span class="badge bg-danger">Full</span>
                                    @elseif($room->status == 'maintenance')
                                        <span class="badge bg-warning text-dark">Maintenance</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($room->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 6px; width: 100px;">
                                            @php
                                                $percentage = ($room->occupied / $room->capacity) * 100;
                                                $color = $percentage >= 100 ? 'bg-danger' : ($percentage >= 50 ? 'bg-warning' : 'bg-success');
                                            @endphp
                                            <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="ms-2 small text-muted">{{ $room->occupied }}/{{ $room->capacity }}</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('rooms.show', $room) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-outline-warning" title="Edit Room">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Room">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-door-open fa-3x mb-3 opacity-50"></i>
                                        <p class="h5">No rooms found in this hostel</p>
                                        <p class="small">Get started by adding a new room.</p>
                                        <a href="{{ route('rooms.create', ['hostel_id' => $hostel->id]) }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus me-2"></i>Add Room
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($rooms->hasPages())
                <div class="card-footer bg-white">
                    {{ $rooms->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
