@extends('layouts.admin')

@section('page-title', request('type') == 'booking' ? (request('status') == 'pending' ? 'Pending Room Bookings' : 'All Room Bookings') : 'Payments History')

@section('content')

<style>
.receipt-preview {
    width: 50px;
    height: 50px;
    border-radius: 4px;
    cursor: pointer;
    object-fit: cover;
    border: 1px solid #dee2e6;
}
.pdf-preview {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}
</style>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas {{ request('type') == 'booking' ? 'fa-home' : 'fa-credit-card' }} me-2 text-primary"></i>
                    {{ request('type') == 'booking' ? (request('status') == 'pending' ? 'Pending Room Bookings' : 'All Room Bookings') : 'Payments History' }}
                </h5>
                <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New Payment
                </a>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Search & Filters -->
                <div class="mb-4 bg-light p-3 rounded shadow-sm">
                    <form action="{{ route('payments.index') }}" method="GET" class="row g-3">
                        <input type="hidden" name="type" value="{{ request('type') }}">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by student, receipt #..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                            <a href="{{ route('payments.index', ['type' => request('type')]) }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-muted small uppercase">
                            <tr>
                                <th>Receipt #</th>
                                <th>Student</th>
                                <th>Payment For</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td><span class="fw-bold">{{ $payment->receipt_number }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ strtoupper(substr($payment->student->full_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $payment->student->full_name }}</div>
                                                <small class="text-muted">{{ $payment->student->admission_number }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($payment->room_id)
                                            <div class="fw-bold text-dark">Room Booking: {{ $payment->room->room_number }}</div>
                                            <div class="small text-muted">{{ $payment->room->hostel->name }} ({{ $payment->room->room_type_display }})</div>
                                            <div class="badge bg-primary-subtle text-primary border-0 small mt-1">
                                                {{ ucfirst($payment->payment_plan) }} Duration
                                            </div>
                                        @else
                                            <div class="fw-bold">General Payment</div>
                                            <div class="small text-muted">Hostel Fees / Others</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">₦{{ number_format($payment->amount, 2) }}</div>
                                        <div class="small text-muted">{{ ucfirst($payment->payment_method) }}</div>
                                    </td>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge rounded-pill 
                                            @if($payment->status == 'completed') bg-success-subtle text-success border border-success-subtle 
                                            @elseif($payment->status == 'pending') bg-warning-subtle text-warning border border-warning-subtle
                                            @else bg-danger-subtle text-danger border border-danger-subtle @endif px-3">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-light border" title="View Details">
                                                <i class="fas fa-eye text-primary"></i>
                                            </a>
                                            @if($payment->status == 'pending')
                                                <form action="{{ route('payments.approve', $payment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-light border text-success" onclick="return confirm('Approve this payment?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-sm btn-light border" title="Edit">
                                                <i class="fas fa-edit text-warning"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request('type') == 'booking' ? 7 : 6 }}" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-receipt fa-3x mb-3 opacity-25"></i>
                                            <p>No payments found matching your criteria</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $payments->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
