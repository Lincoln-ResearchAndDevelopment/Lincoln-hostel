@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-3">
                    <h2 class="h5 mb-0">Edit Profile</h2>
                </div>
                
                <div class="card-body bg-light">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label text-dark fw-semibold">Name</label>
                            <input type="text" name="name" class="form-control border-gray" 
                                   value="{{ Auth::user()->name }}" required
                                   style="background-color: #fff; border-color: #dee2e6;">
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label text-dark fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control border-gray" 
                                   value="{{ Auth::user()->email }}" required
                                   style="background-color: #fff; border-color: #dee2e6;">
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('profile.index') }}" class="btn btn-outline-dark px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-dark px-4">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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