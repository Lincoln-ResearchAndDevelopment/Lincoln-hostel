@extends('layouts.admin')

@section('page-title', 'Attendance Management')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Attendance</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Check-ins Today</p>
                <h4 class="fw-bold mb-0">{{ $stats['total_check_ins'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Check-outs Today</p>
                <h4 class="fw-bold mb-0">{{ $stats['total_check_outs'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #cc0000 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Students In Hostel</p>
                <h4 class="fw-bold mb-0">{{ $stats['students_in'] }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.attendance.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="all">All Types</option>
                    <option value="check_in" {{ request('type') == 'check_in' ? 'selected' : '' }}>Check In</option>
                    <option value="check_out" {{ request('type') == 'check_out' ? 'selected' : '' }}>Check Out</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Search Student</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name or Admission No" value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                <a href="{{ route('admin.attendance.create') }}" class="btn btn-sm btn-success"><i class="fas fa-plus"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Records -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="fas fa-clipboard-check me-2" style="color: #cc0000;"></i>Attendance Records - {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</h6>
        <a href="{{ route('admin.attendance.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Record Attendance
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Student</th>
                    <th>Room / Hostel</th>
                    <th>Type</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Recorded By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td class="ps-3">
                        <strong>{{ $record->student->full_name ?? 'N/A' }}</strong>
                        <br><small class="text-muted">{{ $record->student->admission_number ?? '' }}</small>
                    </td>
                    <td>
                        @if($record->student->room)
                            {{ $record->student->room->room_number }}
                            <br><small class="text-muted">{{ $record->student->room->hostel->name ?? '' }}</small>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($record->type == 'check_in')
                            <span class="badge bg-success"><i class="fas fa-sign-in-alt me-1"></i>Check In</span>
                        @else
                            <span class="badge bg-danger"><i class="fas fa-sign-out-alt me-1"></i>Check Out</span>
                        @endif
                    </td>
                    <td>{{ $record->recorded_at->format('h:i A') }}</td>
                    <td>{{ $record->location ?? 'Main Gate' }}</td>
                    <td>{{ $record->recorded_by ?? 'System' }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.student', $record->student) }}" class="btn btn-sm btn-outline-secondary" title="View History">
                            <i class="fas fa-history"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No attendance records found for this date.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())
    <div class="card-footer bg-white">
        {{ $records->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
