@extends('layouts.admin')

@section('page-title', 'Fee Structure')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Fee Structure</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #cc0000 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Expected Fees</p>
                <h4 class="fw-bold mb-0">₦{{ number_format($stats['total_expected']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Collected</p>
                <h4 class="fw-bold mb-0">₦{{ number_format($stats['total_collected']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Outstanding</p>
                <h4 class="fw-bold mb-0">₦{{ number_format($stats['total_outstanding']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #17a2b8 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Fully Paid</p>
                <h4 class="fw-bold mb-0">{{ $stats['fully_paid'] }} <small class="text-muted fw-normal">students</small></h4>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.fees.students') }}" class="btn btn-primary">
                <i class="fas fa-users me-2"></i>View Student Fees
            </a>
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-credit-card me-2"></i>Payment Records
            </a>
            <a href="{{ route('admin.reports.fees') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-bar me-2"></i>Fee Reports
            </a>
        </div>
    </div>
</div>

<!-- Room Prices by Hostel -->
@foreach($hostels as $hostel)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold"><i class="fas fa-building me-2" style="color: #cc0000;"></i>{{ $hostel->name }}</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Room Number</th>
                    <th>Room Type</th>
                    <th>Capacity</th>
                    <th>Price/Semester</th>
                    <th>Price/Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hostel->rooms as $room)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $room->room_number }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($room->room_type ?? 'Standard') }}</span></td>
                    <td>{{ $room->capacity ?? '-' }}</td>
                    <td>₦{{ number_format($room->price_per_semester ?? 0) }}</td>
                    <td>₦{{ number_format($room->price_per_year ?? 0) }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPriceModal{{ $room->id }}">
                            <i class="fas fa-edit me-1"></i>Edit Price
                        </button>
                    </td>
                </tr>

                <!-- Edit Price Modal -->
                <div class="modal fade" id="editPriceModal{{ $room->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.fees.room.update', $room) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Room Price</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted">Room: <strong>{{ $room->room_number }}</strong> ({{ $hostel->name }})</p>
                                    <div class="mb-3">
                                        <label class="form-label">Price Per Semester (₦)</label>
                                        <input type="number" name="price_per_semester" class="form-control" value="{{ $room->price_per_semester ?? 0 }}" min="0" step="100" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price Per Year (₦)</label>
                                        <input type="number" name="price_per_year" class="form-control" value="{{ $room->price_per_year ?? 0 }}" min="0" step="100">
                                        <small class="text-muted">Leave empty to auto-calculate (2x semester price)</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update Price</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-3 text-muted">No rooms in this hostel.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endforeach

@if($hostels->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="fas fa-building fa-3x text-muted mb-3"></i>
        <p class="text-muted">No hostels found. Create hostels and rooms first.</p>
        <a href="{{ route('hostels.create') }}" class="btn btn-primary">Create Hostel</a>
    </div>
</div>
@endif
@endsection
