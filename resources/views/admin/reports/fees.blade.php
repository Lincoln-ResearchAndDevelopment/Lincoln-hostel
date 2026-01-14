@extends('layouts.admin')

@section('page-title', 'Fee Collection Report')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Fee Collection</li>
            </ol>
        </nav>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2" style="color: #cc0000;"></i>Fee Collection Report</h5>
        <small class="text-muted">{{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</small>
    </div>
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Print
    </button>
</div>

<!-- Date Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.fees') }}" method="GET" class="row g-3">
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
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Total Collected</p>
                <h4 class="fw-bold mb-0">₦{{ number_format($stats['total_collected']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #17a2b8 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Transactions</p>
                <h4 class="fw-bold mb-0">{{ $stats['total_transactions'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Outstanding</p>
                <h4 class="fw-bold mb-0">₦{{ number_format($totalOutstanding) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-body py-3">
                <p class="text-muted mb-1 small">Defaulters</p>
                <h4 class="fw-bold mb-0">{{ $outstandingStudents->count() }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Payment by Method -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">By Payment Method</h6>
            </div>
            <div class="card-body">
                @forelse($stats['by_method'] as $method => $amount)
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span>{{ ucfirst($method ?? 'Other') }}</span>
                    <span class="fw-semibold">₦{{ number_format($amount) }}</span>
                </div>
                @empty
                <p class="text-muted text-center py-3">No payments in this period</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Recent Payments</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Student</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments->take(10) as $payment)
                        <tr>
                            <td class="ps-3">{{ $payment->student->full_name ?? 'N/A' }}</td>
                            <td class="text-success">₦{{ number_format($payment->amount) }}</td>
                            <td>{{ ucfirst($payment->payment_method ?? '-') }}</td>
                            <td>{{ $payment->created_at->format('M d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">No payments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Outstanding Fees -->
@if($outstandingStudents->count() > 0)
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-exclamation-circle me-2"></i>Students with Outstanding Fees</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Student</th>
                    <th>Room / Hostel</th>
                    <th>Fee Amount</th>
                    <th>Paid</th>
                    <th>Outstanding</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outstandingStudents->take(20) as $student)
                <tr>
                    <td class="ps-3">
                        <strong>{{ $student->full_name }}</strong>
                        <br><small class="text-muted">{{ $student->admission_number }}</small>
                    </td>
                    <td>
                        @if($student->room)
                            {{ $student->room->room_number }} - {{ $student->room->hostel->name ?? '' }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>₦{{ number_format($student->hostel_fee_amount) }}</td>
                    <td class="text-success">₦{{ number_format($student->hostel_fee_paid) }}</td>
                    <td class="text-danger fw-semibold">₦{{ number_format($student->hostel_fee_amount - $student->hostel_fee_paid) }}</td>
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
