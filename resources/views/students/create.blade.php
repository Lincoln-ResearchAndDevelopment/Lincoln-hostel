@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Add New Student') }}</h5>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('students.store') }}" id="studentForm">
                        @csrf

                        <div class="row mb-3">
                            <label for="admission_number" class="col-md-4 col-form-label text-md-end">
                                {{ __('Admission Number') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="admission_number" type="text" 
                                       class="form-control @error('admission_number') is-invalid @enderror" 
                                       name="admission_number" 
                                       value="{{ old('admission_number') }}" 
                                       required
                                       maxlength="50"
                                       autofocus>
                                @error('admission_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="full_name" class="col-md-4 col-form-label text-md-end">
                                {{ __('Full Name') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="full_name" type="text" 
                                       class="form-control @error('full_name') is-invalid @enderror" 
                                       name="full_name" 
                                       value="{{ old('full_name') }}" 
                                       required
                                       maxlength="255">
                                @error('full_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="gender" class="col-md-4 col-form-label text-md-end">
                                {{ __('Gender') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="gender" 
                                        class="form-select @error('gender') is-invalid @enderror" 
                                        name="gender" required>
                                    <option value="" disabled selected>Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">
                                {{ __('Email') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="department" class="col-md-4 col-form-label text-md-end">
                                {{ __('Department') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="department" 
                                        class="form-select @error('department') is-invalid @enderror" 
                                        name="department" 
                                        required>
                                    <option value="">Select Department</option>
                                    <option value="Computer Software Engineering" {{ old('department') == 'Computer Software Engineering' ? 'selected' : '' }}>Computer Software Engineering</option>
                                    <option value="Foundation of Nursing" {{ old('department') == 'Foundation of Nursing' ? 'selected' : '' }}>Foundation of Nursing</option>
                                    <option value="Business and Banking Operations" {{ old('department') == 'Business and Banking Operations' ? 'selected' : '' }}>Business and Banking Operations</option>
                                    <option value="English and Mass Communication" {{ old('department') == 'English and Mass Communication' ? 'selected' : '' }}>English and Mass Communication</option>
                                    <option value="Psychology" {{ old('department') == 'Psychology' ? 'selected' : '' }}>Psychology</option>
                                </select>
                                @error('department')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="semester" class="col-md-4 col-form-label text-md-end">
                                {{ __('Semester') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="semester" 
                                        class="form-select @error('semester') is-invalid @enderror" 
                                        name="semester" 
                                        required>
                                    <option value="">Select Semester</option>
                                    @for($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('semester')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="intake" class="col-md-4 col-form-label text-md-end">
                                {{ __('Intake') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="intake" 
                                        class="form-select @error('intake') is-invalid @enderror" 
                                        name="intake" 
                                        required>
                                    <option value="">Select Intake</option>
                                    <option value="March 2023" {{ old('intake') == 'March 2023' ? 'selected' : '' }}>March 2023</option>
                                    <option value="July 2023" {{ old('intake') == 'July 2023' ? 'selected' : '' }}>July 2023</option>
                                    <option value="November 2023" {{ old('intake') == 'November 2023' ? 'selected' : '' }}>November 2023</option>
                                    <option value="March 2024" {{ old('intake') == 'March 2024' ? 'selected' : '' }}>March 2024</option>
                                    <option value="July 2024" {{ old('intake') == 'July 2024' ? 'selected' : '' }}>July 2024</option>
                                    <option value="November 2024" {{ old('intake') == 'November 2024' ? 'selected' : '' }}>November 2024</option>
                                    <option value="March 2025" {{ old('intake') == 'March 2025' ? 'selected' : '' }}>March 2025</option>
                                </select>
                                @error('intake')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="room_id" class="col-md-4 col-form-label text-md-end">
                                {{ __('Room') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="room_id" 
                                        class="form-select @error('room_id') is-invalid @enderror" 
                                        name="room_id" 
                                        required>
                                    <option value="">Select a room</option>
                                    @foreach($availableRooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->room_number }} ({{ $room->occupied }}/{{ $room->capacity }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="contact_number" class="col-md-4 col-form-label text-md-end">
                                {{ __('Contact Number') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="contact_number" type="tel" 
                                       class="form-control @error('contact_number') is-invalid @enderror" 
                                       name="contact_number" 
                                       value="{{ old('contact_number') }}" 
                                       required
                                       maxlength="20">
                                @error('contact_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="emergency_contact" class="col-md-4 col-form-label text-md-end">
                                {{ __('Emergency Contact') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="emergency_contact" type="tel" 
                                       class="form-control @error('emergency_contact') is-invalid @enderror" 
                                       name="emergency_contact" 
                                       value="{{ old('emergency_contact') }}" 
                                       required
                                       maxlength="20">
                                @error('emergency_contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="address" class="col-md-4 col-form-label text-md-end">
                                {{ __('Address') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <textarea id="address" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          name="address" 
                                          required
                                          maxlength="255">{{ old('address') }}</textarea>
                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="check_in_date" class="col-md-4 col-form-label text-md-end">
                                {{ __('Check-in Date') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="check_in_date" type="date" 
                                       class="form-control @error('check_in_date') is-invalid @enderror" 
                                       name="check_in_date" 
                                       value="{{ old('check_in_date', now()->format('Y-m-d')) }}" 
                                       min="{{ now()->format('Y-m-d') }}" 
                                       required>
                                @error('check_in_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="expected_check_out_date" class="col-md-4 col-form-label text-md-end">
                                {{ __('Expected Check-out Date') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <input id="expected_check_out_date" type="date" 
                                       class="form-control @error('expected_check_out_date') is-invalid @enderror" 
                                       name="expected_check_out_date" 
                                       value="{{ old('expected_check_out_date') }}" 
                                       required>
                                @error('expected_check_out_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn" style="background-color: #2c3e50; color: white">
                                    <i class="fas fa-save"></i> {{ __('Register Student') }}
                                </button>
                                <button type="reset" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-undo"></i> {{ __('Reset') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set minimum check-out date based on check-in date
        const checkInDate = document.getElementById('check_in_date');
        const checkOutDate = document.getElementById('expected_check_out_date');

        checkInDate.addEventListener('change', function() {
            if (this.value) {
                checkOutDate.min = this.value;
                if (checkOutDate.value && checkOutDate.value < this.value) {
                    checkOutDate.value = '';
                }
            }
        });

        // Initialize the min date for check-out if check-in has a value
        if (checkInDate.value) {
            checkOutDate.min = checkInDate.value;
        }
    });
</script>
@endsection
@endsection