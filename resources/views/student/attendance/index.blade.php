@extends('layouts.student')

@section('page-title', 'Attendance Records')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Attendance Records</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Header & Current Status -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">In/Out Records</h4>
                        <p class="text-muted mb-0">Track your hostel check-in and check-out history</p>
                    </div>
                    <div class="text-end">
                        <span class="badge {{ $student->isInHostel() ? 'bg-success' : 'bg-warning' }} fs-6 px-3 py-2">
                            <i class="fas {{ $student->isInHostel() ? 'fa-home' : 'fa-walking' }} me-2"></i>
                            {{ $student->isInHostel() ? 'Currently In Hostel' : 'Currently Out' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="icon-shape bg-success-subtle text-success rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h4 class="mb-0">{{ $checkIns }}</h4>
                        <small class="text-muted">Check-ins</small>
                    </div>
                    <div class="col-6">
                        <div class="icon-shape bg-warning-subtle text-warning rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <h4 class="mb-0">{{ $checkOuts }}</h4>
                        <small class="text-muted">Check-outs</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Date Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('student.attendance.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filter Records
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Last Activity -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted mb-2"><i class="fas fa-sign-in-alt text-success me-2"></i>Last Check-in</h6>
                @if($student->last_check_in)
                    <h5 class="mb-1">{{ $student->last_check_in->recorded_at->format('M d, Y h:i A') }}</h5>
                    <small class="text-muted">
                        {{ $student->last_check_in->recorded_at->diffForHumans() }}
                        @if($student->last_check_in->location)
                            • {{ $student->last_check_in->location }}
                        @endif
                    </small>
                @else
                    <p class="text-muted mb-0">No check-in records found</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm border-start border-warning border-4">
            <div class="card-body">
                <h6 class="text-muted mb-2"><i class="fas fa-sign-out-alt text-warning me-2"></i>Last Check-out</h6>
                @if($student->last_check_out)
                    <h5 class="mb-1">{{ $student->last_check_out->recorded_at->format('M d, Y h:i A') }}</h5>
                    <small class="text-muted">
                        {{ $student->last_check_out->recorded_at->diffForHumans() }}
                        @if($student->last_check_out->location)
                            • {{ $student->last_check_out->location }}
                        @endif
                    </small>
                @else
                    <p class="text-muted mb-0">No check-out records found</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Attendance Records Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-history me-2" style="color: #cc0000;"></i>Attendance History</h5>
            </div>
            <div class="card-body">
                @if($attendanceRecords->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Recorded By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendanceRecords as $record)
                                <tr>
                                    <td>
                                        <strong>{{ $record->recorded_at->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $record->recorded_at->format('h:i A') }}</small>
                                    </td>
                                    <td>{!! $record->type_badge !!}</td>
                                    <td>{{ $record->location ?? 'N/A' }}</td>
                                    <td>{{ $record->recorded_by ?? 'System' }}</td>
                                    <td>
                                        @if($record->notes)
                                            <span class="text-muted">{{ Str::limit($record->notes, 50) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $attendanceRecords->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Attendance Records</h5>
                        <p class="text-muted mb-0">No attendance records found for the selected date range.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Information Card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info d-flex align-items-start">
            <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
            <div>
                <h6 class="mb-1">About Attendance Records</h6>
                <p class="mb-0">
                    Your attendance is recorded when you check in or out at the hostel gate. 
                    These records help us ensure your safety and track hostel occupancy. 
                    If you notice any discrepancies in your records, please contact the hostel porter or warden.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection