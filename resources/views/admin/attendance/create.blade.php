@extends('layouts.admin')

@section('page-title', 'Record Attendance')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">Attendance</a></li>
                <li class="breadcrumb-item active">Record</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-clipboard-check me-2" style="color: #cc0000;"></i>Record Student Attendance</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.attendance.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Student <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->full_name }} ({{ $student->admission_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Attendance Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="check_in" value="check_in" {{ old('type', 'check_in') == 'check_in' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="check_in">
                                        <i class="fas fa-sign-in-alt text-success me-1"></i>Check In
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="check_out" value="check_out" {{ old('type') == 'check_out' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="check_out">
                                        <i class="fas fa-sign-out-alt text-danger me-1"></i>Check Out
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <select name="location" class="form-select @error('location') is-invalid @enderror">
                                <option value="Main Gate" {{ old('location') == 'Main Gate' ? 'selected' : '' }}>Main Gate</option>
                                <option value="Back Gate" {{ old('location') == 'Back Gate' ? 'selected' : '' }}>Back Gate</option>
                                <option value="Reception" {{ old('location') == 'Reception' ? 'selected' : '' }}>Reception</option>
                                <option value="Other" {{ old('location') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Current Time</label>
                            <input type="text" class="form-control" value="{{ now()->format('M d, Y h:i A') }}" disabled>
                            <small class="text-muted">Attendance will be recorded with current timestamp</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Record Attendance
                        </button>
                        <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2" style="color: #cc0000;"></i>Quick Info</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 text-center">
                        <div class="p-3 bg-light rounded">
                            <h4 class="fw-bold mb-1" style="color: #cc0000;">{{ $students->count() }}</h4>
                            <small class="text-muted">Active Students</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-3 bg-light rounded">
                            <h4 class="fw-bold mb-1 text-success">{{ now()->format('h:i A') }}</h4>
                            <small class="text-muted">Current Time</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-3 bg-light rounded">
                            <h4 class="fw-bold mb-1 text-info">{{ now()->format('M d') }}</h4>
                            <small class="text-muted">Today's Date</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-focus on student select
    document.querySelector('select[name="student_id"]').focus();
</script>
@endpush
