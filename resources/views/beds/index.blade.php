@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Bed Management - {{ $room->room_number }}</h2>
                    <p class="text-muted mb-0">
                        {{ $room->hostel->name }} | {{ ucfirst($room->room_type) }} | 
                        Capacity: {{ $room->capacity }} | 
                        Occupied: {{ $room->occupied }} | 
                        Gender: {{ ucfirst($room->gender_type) }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Rooms
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBedModal">
                        <i class="fas fa-plus"></i> Add Bed
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Beds in this Room ({{ $beds->count() }} total)</h5>
                </div>
                <div class="card-body">
                    @if($beds->count() === 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No beds have been created for this room yet. Click "Add Bed" to create one.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Bed Number</th>
                                        <th>Status</th>
                                        <th>Occupied By</th>
                                        <th>Admission Number</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($beds as $index => $bed)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $bed->bed_number }}</strong>
                                            </td>
                                            <td>
                                                @if($bed->is_occupied)
                                                    <span class="badge bg-danger">Occupied</span>
                                                @else
                                                    <span class="badge bg-success">Available</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($bed->student)
                                                    <a href="{{ route('students.show', $bed->student) }}" class="text-decoration-none">
                                                        {{ $bed->student->full_name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($bed->student)
                                                    {{ $bed->student->admission_number }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editBedModal{{ $bed->id }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                
                                                @if(!$bed->is_occupied)
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteBedModal{{ $bed->id }}">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-secondary" disabled title="Cannot delete occupied bed">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Edit Bed Modal -->
                                        <div class="modal fade" id="editBedModal{{ $bed->id }}" tabindex="-1" aria-labelledby="editBedModalLabel{{ $bed->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('beds.update', [$room, $bed]) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editBedModalLabel{{ $bed->id }}">Edit Bed Number</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="bed_number_edit{{ $bed->id }}" class="form-label">Bed Number <span class="text-danger">*</span></label>
                                                                <input type="text" 
                                                                       class="form-control" 
                                                                       id="bed_number_edit{{ $bed->id }}" 
                                                                       name="bed_number" 
                                                                       value="{{ $bed->bed_number }}" 
                                                                       required>
                                                                <small class="form-text text-muted">e.g., "Bed 1", "A1", "Upper Bunk 1"</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Update Bed</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Bed Modal -->
                                        <div class="modal fade" id="deleteBedModal{{ $bed->id }}" tabindex="-1" aria-labelledby="deleteBedModalLabel{{ $bed->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('beds.destroy', [$room, $bed]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title" id="deleteBedModalLabel{{ $bed->id }}">Confirm Deletion</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to delete <strong>{{ $bed->bed_number }}</strong>?</p>
                                                            <p class="text-danger mb-0"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Delete Bed</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Bed Modal -->
<div class="modal fade" id="addBedModal" tabindex="-1" aria-labelledby="addBedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('beds.store', $room) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addBedModalLabel">Add New Bed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Current room capacity: <strong>{{ $room->capacity }}</strong> | 
                        Beds created: <strong>{{ $beds->count() }}</strong>
                    </div>
                    <div class="mb-3">
                        <label for="bed_number" class="form-label">Bed Number <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('bed_number') is-invalid @enderror" 
                               id="bed_number" 
                               name="bed_number" 
                               placeholder="e.g., Bed {{ $beds->count() + 1 }}" 
                               value="{{ old('bed_number') }}" 
                               required>
                        @error('bed_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Enter a unique bed identifier for this room.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Bed</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.6em;
    }
</style>
@endpush
