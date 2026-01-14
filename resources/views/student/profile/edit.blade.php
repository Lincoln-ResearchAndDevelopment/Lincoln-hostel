@extends('layouts.student')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">Edit Profile</div>
                <div class="card-body">
                    <form action="{{ url('/student/profile/update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $student->full_name) }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" value="{{ old('contact_number', $student->contact_number) }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" value="{{ old('address', $student->address) }}" class="form-control">
                        </div>
                        <div class="mb-3 text-end">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                            <a href="{{ url('/student/profile') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
