@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Student') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('students.update', $student) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-3">
                            <label for="admission_number" class="col-md-4 col-form-label text-md-right">{{ __('Admission Number') }}</label>
                            <div class="col-md-6">
                                <input id="admission_number" type="text" class="form-control @error('admission_number') is-invalid @enderror" name="admission_number" value="{{ old('admission_number', $student->admission_number) }}" required>
                                @error('admission_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="full_name" class="col-md-4 col-form-label text-md-right">{{ __('Full Name') }}</label>
                            <div class="col-md-6">
                                <input id="full_name" type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{ old('full_name', $student->full_name) }}" required>
                                @error('full_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="gender" class="col-md-4 col-form-label text-md-right">{{ __('Gender') }}</label>
                            <div class="col-md-6">
                                <select id="gender" class="form-select @error('gender') is-invalid @enderror" name="gender">
                                    <option value="" disabled {{ old('gender', $student->gender) == null ? 'selected' : '' }}>Select Gender</option>
                                    <option value="Male" {{ old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Department Field -->
                        <div class="form-group row mb-3">
                            <label for="department" class="col-md-4 col-form-label text-md-right">{{ __('Department') }}</label>
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
                        
                        <!-- Semester Field -->
                        <div class="row mb-3">
                            <label for="semester" class="col-md-4 col-form-label text-md-right">{{ __('Semester') }}</label>
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
                        
                        <!-- Intake Field -->
                        <div class="row mb-3">
                            <label for="intake" class="col-md-4 col-form-label text-md-right">{{ __('Intake') }}</label>
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

                        <div class="form-group row mb-3">
                            <label for="room_id" class="col-md-4 col-form-label text-md-right">{{ __('Room') }}</label>
                            <div class="col-md-6">
                                <select id="room_id" class="form-control @error('room_id') is-invalid @enderror" name="room_id" required>
                                    <option value="">Select a room</option>
                                    @foreach($availableRooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id', $student->room_id) == $room->id ? 'selected' : '' }}>
                                            {{ $room->room_number }} ({{ $room->occupied }}/{{ $room->capacity }} occupied)
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

                        <div class="form-group row mb-3">
                            <label for="contact_number" class="col-md-4 col-form-label text-md-right">{{ __('Contact Number') }}</label>
                            <div class="col-md-6">
                                <input id="contact_number" type="text" class="form-control @error('contact_number') is-invalid @enderror" name="contact_number" value="{{ old('contact_number', $student->contact_number) }}" required>
                                @error('contact_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="emergency_contact" class="col-md-4 col-form-label text-md-right">{{ __('Emergency Contact') }}</label>
                            <div class="col-md-6">
                                <input id="emergency_contact" type="text" class="form-control @error('emergency_contact') is-invalid @enderror" name="emergency_contact" value="{{ old('emergency_contact', $student->emergency_contact) }}" required>
                                @error('emergency_contact')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>
                            <div class="col-md-6">
                                <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" required>{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="check_in_date" class="col-md-4 col-form-label text-md-right">{{ __('Check-in Date') }}</label>
                            <div class="col-md-6">
                                <input id="check_in_date" type="date" class="form-control @error('check_in_date') is-invalid @enderror" name="check_in_date" value="{{ old('check_in_date', $student->check_in_date->format('Y-m-d')) }}" required>
                                @error('check_in_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="expected_check_out_date" class="col-md-4 col-form-label text-md-right">{{ __('Expected Check-out Date') }}</label>
                            <div class="col-md-6">
                                <input id="expected_check_out_date" type="date" class="form-control @error('expected_check_out_date') is-invalid @enderror" name="expected_check_out_date" value="{{ old('expected_check_out_date', $student->expected_check_out_date->format('Y-m-d')) }}" required>
                                @error('expected_check_out_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                            <div class="col-md-6">
                                <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" required>
                                    <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Update Student') }}
                                </button>
                                <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection