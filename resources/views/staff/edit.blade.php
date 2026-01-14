@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-3">
                    <h2 class="h5 mb-0">Edit Staff Member</h2>
                </div>
                
                <div class="card-body bg-light">
                    <form action="{{ route('staff.update', $staff) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="form-label text-dark fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control border-gray" 
                                   value="{{ old('name', $staff->name) }}" required
                                   style="background-color: #fff; border-color: #dee2e6;">
                            @error('name')
                                <div class="text-danger mt-2 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="contact" class="form-label text-dark fw-semibold">Contact Number</label>
                            <input type="text" name="contact" class="form-control border-gray" 
                                   value="{{ old('contact', $staff->contact) }}" required
                                   style="background-color: #fff; border-color: #dee2e6;">
                            @error('contact')
                                <div class="text-danger mt-2 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="staff_gender" class="form-label text-dark fw-semibold">Staff Gender</label>
                                    <select name="staff_gender" class="form-control border-gray" required
                                            style="background-color: #fff; border-color: #dee2e6;">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('staff_gender', $staff->staff_gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('staff_gender', $staff->staff_gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('staff_gender')
                                        <div class="text-danger mt-2 small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="assigned_hostel_gender" class="form-label text-dark fw-semibold">Assigned Hostel</label>
                                    <select name="assigned_hostel_gender" class="form-control border-gray" required
                                            style="background-color: #fff; border-color: #dee2e6;">
                                        <option value="">Select Hostel Type</option>
                                        <option value="male" {{ old('assigned_hostel_gender', $staff->assigned_hostel_gender) == 'male' ? 'selected' : '' }}>Male Hostel</option>
                                        <option value="female" {{ old('assigned_hostel_gender', $staff->assigned_hostel_gender) == 'female' ? 'selected' : '' }}>Female Hostel</option>
                                    </select>
                                    @error('assigned_hostel_gender')
                                        <div class="text-danger mt-2 small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('staff.index') }}" class="btn btn-outline-dark px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-dark px-4">
                                Update Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Same styles as create form */
body {
    background-color: #f8f9fa;
    color: #212529;
}

.card {
    border-radius: 8px;
}

.card-header {
    border-radius: 8px 8px 0 0 !important;
}

.form-control {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 0.75rem;
    transition: all 0.3s ease;
    color: #212529;
}

.form-control:focus {
    border-color: #000;
    box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

.form-label {
    color: #000;
    margin-bottom: 0.5rem;
}

.btn-dark {
    background-color: #000;
    border-color: #000;
    transition: all 0.3s ease;
}

.btn-dark:hover {
    background-color: #333;
    border-color: #333;
    transform: translateY(-1px);
}

.btn-outline-dark {
    border-color: #000;
    color: #000;
    transition: all 0.3s ease;
}

.btn-outline-dark:hover {
    background-color: #000;
    border-color: #000;
    color: #fff;
    transform: translateY(-1px);
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1) !important;
}

.border-gray {
    border-color: #dee2e6 !important;
}
</style>
@endsection