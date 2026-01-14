@extends('layouts.admin')

@section('page-title', 'Complaints Report')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Complaints</li>
            </ol>
        </nav>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2" style="color: #cc0000;"></i>Complaints Report</h5>
        <small class="text-muted">{{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</small>
    </div>
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Print
    </button>
</div>

<!-- Date Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.complaints') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary w-100">Generate Report</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #cc0000 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Total Complaints</p>
                <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Resolved</p>
                <h4 class="fw-bold mb-0">{{ $stats['resolved'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Pending</p>
                <h4 class="fw-bold mb-0">{{ $stats['pending'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #17a2b8 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Resolution Rate</p>
                <h4 class="fw-bold mb-0">{{ $stats['total'] > 0 ? round(($stats['resolved'] / $stats['total']) * 100) : 0 }}%</h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- By Status -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">By Status</h6>
            </div>
            <div class="card-body">
                @forelse($stats['by_status'] as $status => $count)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span>
                        <span class="badge bg-{{ $status == 'resolved' ? 'success' : ($status == 'submitted' ? 'warning' : ($status == 'in progress' ? 'info' : 'secondary')) }} me-2">
                            {{ ucfirst($status) }}
                        </span>
                    </span>
                    <span class="fw-semibold">{{ $count }}</span>
                </div>
                @empty
                <p class="text-muted text-center py-3">No complaints in this period</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Complaints -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Recent Complaints</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Student</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints->take(10) as $complaint)
                        <tr>
                            <td class="ps-3">{{ $complaint->student->full_name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($complaint->subject ?? $complaint->title ?? 'N/A', 30) }}</td>
                            <td>
                                <span class="badge bg-{{ $complaint->status == 'resolved' ? 'success' : ($complaint->status == 'submitted' ? 'warning' : 'info') }}">
                                    {{ ucfirst($complaint->status) }}
                                </span>
                            </td>
                            <td>{{ $complaint->created_at->format('M d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">No complaints found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- All Complaints List -->
@if($complaints->count() > 0)
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">All Complaints in Period</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Student</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($complaints as $complaint)
                <tr>
                    <td class="ps-3">#{{ $complaint->id }}</td>
                    <td>
                        <strong>{{ $complaint->student->full_name ?? 'N/A' }}</strong>
                        <br><small class="text-muted">{{ $complaint->student->admission_number ?? '' }}</small>
                    </td>
                    <td>{{ Str::limit($complaint->subject ?? $complaint->title ?? 'N/A', 40) }}</td>
                    <td>
                        <span class="badge bg-{{ $complaint->status == 'resolved' ? 'success' : ($complaint->status == 'submitted' ? 'warning' : 'info') }}">
                            {{ ucfirst($complaint->status) }}
                        </span>
                    </td>
                    <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    @media print {
        .sidebar, .top-header, .breadcrumb, button, form { display: none !important; }
        .main-content { margin-left: 0 !important; }
    }
</style>
@endpush
