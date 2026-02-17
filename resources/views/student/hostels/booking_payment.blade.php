@extends('layouts.student')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white p-4">
                    <h4 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Complete Your Booking</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 border-end">
                            <h5 class="fw-bold mb-3 text-primary">Booking Details</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-building text-muted me-2 w-20"></i><strong>Hostel:</strong> {{ $room->hostel->name }}</li>
                                <li class="mb-2"><i class="fas fa-door-open text-muted me-2 w-20"></i><strong>Room:</strong> {{ $room->room_number }}</li>
                                <li class="mb-2"><i class="fas fa-layer-group text-muted me-2 w-20"></i><strong>Floor:</strong> Floor {{ $room->floor_number }}</li>
                                <li class="mb-2"><i class="fas fa-calendar-alt text-muted me-2 w-20"></i><strong>Plan:</strong> {{ ucfirst($plan) }}</li>
                                <li class="mb-2"><i class="fas fa-money-bill-wave text-muted me-2 w-20"></i><strong>Total Fee:</strong> ₦{{ number_format($amount, 2) }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6 ps-md-4">
                            <h5 class="fw-bold mb-3 text-success">Bank Details for Payment</h5>
                            <div class="bg-light p-3 rounded border">
                                <p class="mb-2"><strong>Bank:</strong> Zenith Bank</p>
                                <p class="mb-2"><strong>Account Name:</strong> Lincoln ODL LTD (GOperation)</p>
                                <p class="mb-0"><strong>Account Number:</strong> <code>1311150112</code></p>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i> Please make the payment and upload the receipt below.
                            </small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <form action="{{ route('student.rooms.submit_booking_payment', $room) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="payment_plan" value="{{ $plan }}">
                        <input type="hidden" name="amount" value="{{ $amount }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Payment Method</label>
                                    <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Upload Payment Receipt</label>
                                    <input type="file" name="receipt" class="form-control @error('receipt') is-invalid @enderror" accept="image/*,.pdf" required>
                                    <div class="form-text">JPG, PNG or PDF (Max 5MB)</div>
                                    @error('receipt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Additional Notes (Optional)</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Any details for the admin..."></textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                <i class="fas fa-paper-plane me-2"></i>Submit for Verification
                            </button>
                            <a href="{{ route('student.hostels.show', $room->hostel) }}" class="btn btn-link text-muted">Go Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.w-20 { width: 20px; }
</style>
@endsection
