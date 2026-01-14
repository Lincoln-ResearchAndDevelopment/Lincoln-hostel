@extends('layouts.admin')

@section('page-title', 'Student Attendance History')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">Attendance</a></li>
                <li class="breadcrumb-item active">{{ $student->full_name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-user-graduate me-2" style="color: #cc0000;"></i>Student Info</h6>
            </div>
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <span class="fs-3 fw-bold" style="color: #cc0000;">{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                </div>
                <h5 class="fw-bold mb-1">{{ $student->full_name }}</h5>
                <p class="text-muted mb-2">{{ $student->admission_number }}</p>
                @if($student->room)
                <span class="badge bg-secondary">{{ $student->room->room_number }} - {{ $student->room->hostel->name ?? '' }}</span>
                @endif
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @php
                    $checkIns = $records->where('type', 'check_in')->count();
                    $checkOuts = $records->where('type', 'check_out')->count();
                @endphp
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="p-2 bg-success bg-opacity-10 rounded">
                            <h5 class="fw-bold text-success mb-0">{{ $checkIns }}</h5>
                            <small class="text-muted">Check Ins</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-danger bg-opacity-10 rounded">
                            <h5 class="fw-bold text-danger mb-0">{{ $checkOuts }}</h5>
                            <small class="text-muted">Check Outs</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('admin.attendance.student', $student) }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small">Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">End Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2" style="color: #cc0000;"></i>Attendance History</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Date</th>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                        <tr>
                            <td class="ps-3">{{ $record->recorded_at->format('M d, Y') }}</td>
                            <td>{{ $record->recorded_at->format('h:i A') }}</td>
                            <td>
                                @if($record->type == 'check_in')
                                    <span class="badge bg-success"><i class="fas fa-sign-in-alt me-1"></i>In</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-sign-out-alt me-1"></i>Out</span>
                                @endif
                            </td>
                            <td>{{ $record->location ?? 'Main Gate' }}</td>
                            <td>{{ $record->recorded_by ?? 'System' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No records found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($records->hasPages())
            <div class="card-footer bg-white">{{ $records->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
