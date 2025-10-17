@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Rooms') }}
                    <a href="{{ route('rooms.create') }}" class="btn" style="background-color: #2c3e50; color: white">Add New Room</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Search Bar -->
                    <div class="mb-4">
                        <form action="{{ route('rooms.index') }}" method="GET" class="row g-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Search rooms by gender type, room number, capacity or status" 
                                        value="{{ request('search') }}"
                                        aria-label="Search rooms">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn w-100" style="background-color: #2c3e50; color: white">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Gender Type</th>
                                <th>Room Number</th>
                                <th>Capacity</th>
                                <th>Occupied</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rooms as $room)
                                <tr>
                                    <td>{{ ucfirst($room->gender_type) ?? '-' }}</td>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ $room->capacity }}</td>
                                    <td>{{ $room->occupied }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($room->current_status == 'available') bg-success 
                                            @elseif($room->current_status == 'full') bg-danger
                                            @else bg-warning @endif">
                                            {{ ucfirst($room->current_status) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                                            <!-- View Button -->
                                            <a href="{{ route('rooms.show', $room) }}" 
                                            class="btn btn-info btn-sm"
                                            title="View"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Edit Button -->
                                            <a href="{{ route('rooms.edit', $room) }}" 
                                            class="btn btn-warning btn-sm"
                                            title="Edit"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Delete Button -->
                                            @if($room->occupied == 0)
                                                <form action="{{ route('rooms.destroy', $room) }}" 
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this room?')"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm"
                                                            title="Delete"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No rooms found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Enhanced Pagination -->
                    @if($rooms instanceof \Illuminate\Pagination\AbstractPaginator && $rooms->total() > $rooms->perPage())
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                            <!-- Pagination Info -->
                            <div class="pagination-info mb-2 mb-md-0">
                                Showing {{ $rooms->firstItem() }} to {{ $rooms->lastItem() }} of {{ $rooms->total() }} results
                            </div>
                            
                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $rooms->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif

            </div>
        </div>
    </div>
</div>
@endsection
