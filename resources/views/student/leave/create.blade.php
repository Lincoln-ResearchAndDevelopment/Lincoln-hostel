@extends('layouts.student')

@section('page-title', 'Apply for Leave')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.leave.index') }}">Leave Requests</a></li>
                <li class="breadcrumb-item active">Apply</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2" style="color: #cc0000;"></i>New Leave Application</h5>
                    <a href="{{ route('student.leave.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('student.leave.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Leave Type</label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="card border p-3 h-100 cursor-pointer form-check-label bg-light-hover">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" value="home" checked>
                                        <span class="form-check-label fw-bold">
                                            <i class="fas fa-home text-success me-2"></i>Home Visit
                                        </span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="card border p-3 h-100 cursor-pointer form-check-label bg-light-hover">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" value="medical">
                                        <span class="form-check-label fw-bold">
                                            <i class="fas fa-notes-medical text-info me-2"></i>Medical
                                        </span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="card border p-3 h-100 cursor-pointer form-check-label bg-light-hover">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" value="other">
                                        <span class="form-check-label fw-bold">
                                            <i class="fas fa-star text-secondary me-2"></i>Other
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label fw-bold">From Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label fw-bold">To Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required min="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reason" class="form-label fw-bold">Reason for Leave</label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Please provide specific details..." required></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="destination" class="form-label fw-bold">Destination</label>
                            <input type="text" class="form-control" id="destination" name="destination" placeholder="Where will you be going?">
                            <small class="text-muted">City/Town you'll be visiting</small>
                        </div>
                        <div class="col-md-6">
                            <label for="emergency_contact" class="form-label fw-bold">Emergency Contact</label>
                            <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" placeholder="Phone number while on leave">
                            <small class="text-muted">Contact number during your leave</small>
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Upon submission, your parent/guardian will be notified via email. You will receive a notification when your request is approved or rejected.
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn py-2" style="background-color: #cc0000; color: white;">
                            <i class="fas fa-paper-plane me-2"></i> Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer { cursor: pointer; }
.bg-light-hover:hover { background-color: var(--bg-primary); }
</style>
@endsection
