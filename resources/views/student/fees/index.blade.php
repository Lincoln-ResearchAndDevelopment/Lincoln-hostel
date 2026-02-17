@extends('layouts.student')

@section('page-title', 'Fee Details & Payment Status')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Fee Details</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Fee Overview Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Total Fee</h6>
                    <div class="rounded px-2 py-1" style="background-color: rgba(204, 0, 0, 0.1);">
                        <i class="fas fa-money-bill-wave" style="color: #cc0000;"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold">₦{{ number_format($feeInfo['total_fee'], 2) }}</h3>
                <small class="text-muted">Hostel accommodation fee</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Amount Paid</h6>
                    <div class="icon-shape bg-success-subtle text-success rounded px-2 py-1">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold text-success">₦{{ number_format($feeInfo['paid'], 2) }}</h3>
                <small class="text-muted">Total payments made</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Outstanding</h6>
                    <div class="icon-shape bg-{{ $feeInfo['outstanding'] > 0 ? 'danger' : 'success' }}-subtle text-{{ $feeInfo['outstanding'] > 0 ? 'danger' : 'success' }} rounded px-2 py-1">
                        <i class="fas fa-{{ $feeInfo['outstanding'] > 0 ? 'exclamation-triangle' : 'check' }}"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold text-{{ $feeInfo['outstanding'] > 0 ? 'danger' : 'success' }}">₦{{ number_format($feeInfo['outstanding'], 2) }}</h3>
                <small class="text-muted">Balance remaining</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Status</h6>
                    <div class="icon-shape bg-info-subtle text-info rounded px-2 py-1">
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold">{!! $student->fee_status_badge !!}</h3>
                @if($feeInfo['due_date'])
                    <small class="text-{{ $feeInfo['is_overdue'] ? 'danger' : 'muted' }}">
                        Due: {{ $feeInfo['due_date']->format('M d, Y') }}
                        @if($feeInfo['is_overdue'])
                            <span class="badge bg-danger">Overdue</span>
                        @endif
                    </small>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Progress -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Payment Progress</h6>
                    <span class="fw-bold">{{ $feeInfo['payment_percentage'] }}%</span>
                </div>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-{{ $feeInfo['payment_percentage'] >= 100 ? 'success' : ($feeInfo['payment_percentage'] >= 50 ? 'warning' : 'danger') }}" 
                         role="progressbar" 
                         style="width: {{ $feeInfo['payment_percentage'] }}%"
                         aria-valuenow="{{ $feeInfo['payment_percentage'] }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        {{ $feeInfo['payment_percentage'] }}%
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted">₦0</small>
                    <small class="text-muted">₦{{ number_format($feeInfo['total_fee'], 2) }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

@if($feeInfo['is_overdue'])
<!-- Overdue Warning -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-danger d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <h6 class="mb-1">Payment Overdue!</h6>
                <p class="mb-0">
                    Your hostel fee payment is overdue. Please make payment as soon as possible to avoid any penalties or accommodation issues.
                    Contact the finance department if you need assistance.
                </p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Payment History -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2" style="color: #cc0000;"></i>Payment History</h5>
                <button class="btn btn-sm" style="background-color: #cc0000; color: white;" data-bs-toggle="modal" data-bs-target="#makePaymentModal">
                    <i class="fas fa-plus me-1"></i>Make Payment
                </button>
            </div>
            <div class="card-body">
                @if($payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td><strong>{{ $payment->receipt_number }}</strong></td>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td class="fw-bold">₦{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($payment->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($payment->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->receipt_path)
                                            <a href="{{ asset('storage/' . $payment->receipt_path) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Payment Records</h5>
                        <p class="text-muted mb-3">You haven't made any payments yet.</p>
                        <button class="btn" style="background-color: #cc0000; color: white;" data-bs-toggle="modal" data-bs-target="#makePaymentModal">
                            <i class="fas fa-plus me-1"></i>Make Your First Payment
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Information -->
<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-university me-2" style="color: #cc0000;"></i>Bank Account Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Bank Name:</td>
                            <td class="fw-bold">Zenith Bank</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Account Number:</td>
                            <td class="fw-bold"><code class="fs-5">1311150112</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Account Name:</td>
                            <td class="fw-bold">Lincoln ODL LTD (GOperation)</td>
                        </tr>
                    </table>
                </div>
                <div class="alert alert-info mt-3 mb-0">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        After making payment, please upload your receipt for verification.
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-question-circle me-2" style="color: #cc0000;"></i>Need Help?</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">If you have any questions about your fees or payments, please contact:</p>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-building me-2" style="color: #cc0000;"></i>
                        <strong>Finance Department</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2" style="color: #cc0000;"></i>
                        +234 (0) 123 456 7890
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2" style="color: #cc0000;"></i>
                        finance@linchostel.com
                    </li>
                    <li>
                        <i class="fas fa-clock me-2" style="color: #cc0000;"></i>
                        Mon - Fri: 8:00 AM - 5:00 PM
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Make Payment Modal -->
<div class="modal fade" id="makePaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i>Submit Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('student.payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (₦)</label>
                        <input type="number" class="form-control" id="amount" name="amount" 
                               value="{{ $feeInfo['outstanding'] }}" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit_card">Credit/Debit Card</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="receipt" class="form-label">Payment Receipt</label>
                        <input type="file" class="form-control" id="receipt" name="receipt" 
                               accept="image/*,application/pdf" required>
                        <small class="text-muted">Upload your payment receipt (JPG, PNG, PDF)</small>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                  placeholder="Any additional information..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection