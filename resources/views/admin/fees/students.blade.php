@extends('layouts.admin')

@section('page-title', 'Student Fees')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.fees.index') }}">Fee Structure</a></li>
                <li class="breadcrumb-item active">Student Fees</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.fees.students') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Payment Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="all">All Status</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial Payment</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Not Paid</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small">Search Student</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name or Admission No" value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Students Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold"><i class="fas fa-users me-2" style="color: #cc0000;"></i>Student Fee Records</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Student</th>
                    <th>Room / Hostel</th>
                    <th>Fee Amount</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                @php
                    $balance = ($student->hostel_fee_amount ?? 0) - ($student->hostel_fee_paid ?? 0);
                    $status = $balance <= 0 ? 'paid' : ($student->hostel_fee_paid > 0 ? 'partial' : 'unpaid');
                @endphp
                <tr>
                    <td class="ps-3">
                        <strong>{{ $student->full_name }}</strong>
                        <br><small class="text-muted">{{ $student->admission_number }}</small>
                    </td>
                    <td>
                        @if($student->room)
                            {{ $student->room->room_number }}
                            <br><small class="text-muted">{{ $student->room->hostel->name ?? '' }}</small>
                        @else
                            <span class="text-muted">Not Assigned</span>
                        @endif
                    </td>
                    <td>₦{{ number_format($student->hostel_fee_amount ?? 0) }}</td>
                    <td class="text-success">₦{{ number_format($student->hostel_fee_paid ?? 0) }}</td>
                    <td class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">₦{{ number_format($balance) }}</td>
                    <td>
                        @if($status == 'paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif($status == 'partial')
                            <span class="badge bg-warning text-dark">Partial</span>
                        @else
                            <span class="badge bg-danger">Unpaid</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editFeeModal{{ $student->id }}" title="Edit Fee">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-secondary" title="View Student">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>

                <!-- Edit Fee Modal -->
                <div class="modal fade" id="editFeeModal{{ $student->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.fees.student.update', $student) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Student Fee</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted mb-3">Student: <strong>{{ $student->full_name }}</strong></p>
                                    <div class="mb-3">
                                        <label class="form-label">Fee Amount (₦)</label>
                                        <input type="number" name="hostel_fee_amount" class="form-control" value="{{ $student->hostel_fee_amount ?? 0 }}" min="0" step="100" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Payment Due Date</label>
                                        <input type="date" name="payment_due_date" class="form-control" value="{{ $student->payment_due_date ? $student->payment_due_date->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="alert alert-info small mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Current paid amount: ₦{{ number_format($student->hostel_fee_paid ?? 0) }}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update Fee</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No students found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($students->hasPages())
    <div class="card-footer bg-white">
        {{ $students->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
