@extends('layouts.admin')

@section('page-title', 'Rooms Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="fas fa-door-open me-2"></i>Rooms Management</h5>
                    </div>
                    <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New Room
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filters -->
                    <div class="mb-4 p-3 bg-light rounded border">
                        <form action="{{ route('rooms.index') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label small text-muted">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                    <input type="text" id="search" name="search" class="form-control" 
                                           placeholder="Room number, description..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="hostel_id" class="form-label small text-muted">Filter by Hostel</label>
                                <select name="hostel_id" id="hostel_id" class="form-select">
                                    <option value="">All Hostels</option>
                                    @foreach($hostels as $hostel)
                                        <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>
                                            {{ $hostel->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 pt-2">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-filter me-1"></i> Apply Filters
                                </button>
                            </div>
                            <div class="col-md-2 pt-2">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Room Details</th>
                                    <th>Hostel</th>
                                    <th>Type & Price</th>
                                    <th>Occupancy</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rooms as $room)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="room-icon bg-light rounded p-2 me-3 text-center" style="width: 45px;">
                                                    <i class="fas fa-door-closed text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">Room {{ $room->room_number }}</div>
                                                    <small class="text-muted">Floor {{ $room->floor_number }} • {{ ucfirst($room->gender_type) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($room->hostel)
                                                <a href="{{ route('hostels.show', $room->hostel) }}" class="text-decoration-none">
                                                    {{ $room->hostel->name }}
                                                </a>
                                                <div class="small text-muted">{{ $room->hostel->code }}</div>
                                            @else
                                                <span class="text-muted fst-italic">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="badge bg-info text-dark mb-1">{{ $room->room_type_display }}</div>
                                            <div class="small text-success fw-bold">
                                                ₦{{ $room->formatted_price_per_semester }}/Sem
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px; width: 60px;">
                                                    @php
                                                        $percentage = $room->capacity > 0 ? ($room->occupied / $room->capacity) * 100 : 0;
                                                        $color = $percentage >= 100 ? 'bg-danger' : ($percentage >= 80 ? 'bg-warning' : 'bg-success');
                                                    @endphp
                                                    <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <span class="small text-muted">{{ $room->occupied }}/{{ $room->capacity }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($room->current_status == 'available') 
                                                <span class="badge bg-success">Available</span>
                                            @elseif($room->current_status == 'full') 
                                                <span class="badge bg-danger">Full</span>
                                            @else 
                                                <span class="badge bg-warning text-dark">Maintenance</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-sm btn-outline-info" title="View details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-outline-warning" title="Edit room">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($room->occupied == 0)
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteRoom({{ $room->id }})" title="Delete room">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                                            <h5>No Rooms Found</h5>
                                            <p class="text-muted">Try adjusting your search or add a new room.</p>
                                            <a href="{{ route('rooms.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus me-1"></i>Add New Room
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $rooms->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteRoomForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteRoom(id) {
    if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
        const form = document.getElementById('deleteRoomForm');
        form.action = `/rooms/${id}`;
        form.submit();
    }
}
</script>
@endsection
