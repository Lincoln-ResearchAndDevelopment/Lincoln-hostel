@extends('layouts.admin')

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
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}" {{ old('department', $student->department) == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
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
                                    <option value="" data-start="" data-end="">Select Intake</option>
                                    @foreach($intakes as $intake)
                                        <option value="{{ $intake->name }}" 
                                                data-start="{{ $intake->start_date ? $intake->start_date->format('Y-m-d') : '' }}" 
                                                data-end="{{ $intake->end_date ? $intake->end_date->format('Y-m-d') : '' }}"
                                                {{ old('intake', $student->intake) == $intake->name ? 'selected' : '' }}>
                                            {{ $intake->name }}
                                        </option>
                                    @endforeach
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
                                <select id="room_id" class="form-select @error('room_id') is-invalid @enderror" name="room_id">
                                    <option value="" {{ old('room_id', $student->room_id) == null ? 'selected' : '' }}>No Room Assigned (Unassigned)</option>
                                    @foreach($availableRooms as $room)
                                        <option value="{{ $room->id }}" 
                                                data-available-beds="{{ $room->available_beds_count ?? 0 }}"
                                                {{ old('room_id', $student->room_id) == $room->id ? 'selected' : '' }}>
                                            {{ $room->display_name ?? ucfirst($room->room_type) . ' Room - ' . $room->room_number . ' (' . $room->hostel->name . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">Rooms are filtered by student's gender and show available bed count.</small>
                            </div>
                        </div>

                        <div class="form-group row mb-3" id="bed-selection-group" style="{{ old('room_id', $student->room_id) ? '' : 'display:none;' }}">
                            <label for="bed_id" class="col-md-4 col-form-label text-md-right">{{ __('Bed Number') }} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select id="bed_id" class="form-select @error('bed_id') is-invalid @enderror" name="bed_id" {{ old('room_id', $student->room_id) ? 'required' : '' }}>
                                    <option value="">Select a bed...</option>
                                    @if($student->room_id && $availableBeds->count() > 0)
                                        @foreach($availableBeds as $bed)
                                            <option value="{{ $bed->id }}" {{ old('bed_id', $student->bed_id) == $bed->id ? 'selected' : '' }}>
                                                {{ $bed->bed_number }}{{ $bed->is_occupied && $bed->student_id == $student->id ? ' (Current)' : '' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('bed_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">Only available beds are shown. Occupied beds are excluded.</small>
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

                        <hr>
                        <h6 class="mb-3 text-primary text-center">{{ __('Demographic Details') }}</h6>

                        <div class="form-group row mb-3">
                            <label for="date_of_birth" class="col-md-4 col-form-label text-md-right">{{ __('Date of Birth') }}</label>
                            <div class="col-md-6">
                                <input id="date_of_birth" type="date" class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nationality" class="col-md-4 col-form-label text-md-right">{{ __('Nationality') }}</label>
                            <div class="col-md-6">
                                <input id="nationality" type="text" class="form-control @error('nationality') is-invalid @enderror" name="nationality" value="{{ old('nationality', $student->nationality) }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="state_of_origin" class="col-md-4 col-form-label text-md-right">{{ __('State of Origin') }}</label>
                            <div class="col-md-6">
                                <input id="state_of_origin" type="text" class="form-control @error('state_of_origin') is-invalid @enderror" name="state_of_origin" value="{{ old('state_of_origin', $student->state_of_origin) }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="local_government" class="col-md-4 col-form-label text-md-right">{{ __('LGA') }}</label>
                            <div class="col-md-6">
                                <input id="local_government" type="text" class="form-control @error('local_government') is-invalid @enderror" name="local_government" value="{{ old('local_government', $student->local_government) }}">
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3 text-primary text-center">{{ __('Parent / Guardian Information') }}</h6>

                        <div class="form-group row mb-3">
                            <label for="parent_name" class="col-md-4 col-form-label text-md-right">{{ __('Parent Full Name') }}</label>
                            <div class="col-md-6">
                                <input id="parent_name" type="text" class="form-control @error('parent_name') is-invalid @enderror" name="parent_name" value="{{ old('parent_name', $student->parent_name) }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="parent_relationship" class="col-md-4 col-form-label text-md-right">{{ __('Relationship') }}</label>
                            <div class="col-md-6">
                                <input id="parent_relationship" type="text" class="form-control @error('parent_relationship') is-invalid @enderror" name="parent_relationship" value="{{ old('parent_relationship', $student->parent_relationship) }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="parent_phone" class="col-md-4 col-form-label text-md-right">{{ __('Parent Phone') }}</label>
                            <div class="col-md-6">
                                <input id="parent_phone" type="tel" class="form-control @error('parent_phone') is-invalid @enderror" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="parent_email" class="col-md-4 col-form-label text-md-right">{{ __('Parent Email') }}</label>
                            <div class="col-md-6">
                                <input id="parent_email" type="email" class="form-control @error('parent_email') is-invalid @enderror" name="parent_email" value="{{ old('parent_email', $student->parent_email) }}">
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3 text-primary text-center">{{ __('Medical Details') }}</h6>

                        <div class="form-group row mb-3">
                            <label for="blood_group" class="col-md-4 col-form-label text-md-right">{{ __('Blood Group') }}</label>
                            <div class="col-md-6">
                                <select id="blood_group" class="form-select @error('blood_group') is-invalid @enderror" name="blood_group">
                                    <option value="">Select Blood Group</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="genotype" class="col-md-4 col-form-label text-md-right">{{ __('Genotype') }}</label>
                            <div class="col-md-6">
                                <input id="genotype" type="text" class="form-control @error('genotype') is-invalid @enderror" name="genotype" value="{{ old('genotype', $student->genotype) }}">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="medical_conditions" class="col-md-4 col-form-label text-md-right">{{ __('Medical Conditions') }}</label>
                            <div class="col-md-6">
                                <textarea id="medical_conditions" class="form-control @error('medical_conditions') is-invalid @enderror" name="medical_conditions">{{ old('medical_conditions', $student->medical_conditions) }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_disability" id="has_disability" value="1" {{ old('has_disability', $student->has_disability) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_disability">
                                        {{ __('Student has a disability') }}
                                    </label>
                                </div>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const intakeSelect = document.getElementById('intake');
        const checkInDate = document.getElementById('check_in_date');
        const checkOutDate = document.getElementById('expected_check_out_date');
        const roomSelect = document.getElementById('room_id');
        const bedSelect = document.getElementById('bed_id');
        const bedSelectionGroup = document.getElementById('bed-selection-group');
        const studentId = {{ $student->id }};

        // Intake date auto-fill
        if (intakeSelect) {
            intakeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const startDate = selectedOption.getAttribute('data-start');
                const endDate = selectedOption.getAttribute('data-end');

                if (startDate && checkInDate) {
                    checkInDate.value = startDate;
                    checkInDate.dispatchEvent(new Event('change'));
                }
                
                if (endDate && checkOutDate) {
                    checkOutDate.value = endDate;
                }
            });
        }

        if (checkInDate) {
            checkInDate.addEventListener('change', function() {
                if (this.value && checkOutDate) {
                    checkOutDate.min = this.value;
                }
            });
        }

        // Dynamic bed loading when room changes
        if (roomSelect) {
            roomSelect.addEventListener('change', function() {
                const roomId = this.value;
                
                if (!roomId) {
                    // No room selected - hide bed selection
                    bedSelectionGroup.style.display = 'none';
                    bedSelect.innerHTML = '<option value="">Select a bed...</option>';
                    bedSelect.removeAttribute('required');
                    return;
                }

                // Show bed selection group
                bedSelectionGroup.style.display = '';
                bedSelect.setAttribute('required', 'required');

                // Load available beds via AJAX
                bedSelect.innerHTML = '<option value="">Loading beds...</option>';
                bedSelect.disabled = true;

                fetch(`{{ route('students.beds.available') }}?room_id=${roomId}&student_id=${studentId}`)
                    .then(response => response.json())
                    .then(beds => {
                        bedSelect.innerHTML = '<option value="">Select a bed...</option>';
                        
                        if (beds.length === 0) {
                            bedSelect.innerHTML += '<option value="" disabled>No beds available in this room</option>';
                        } else {
                            beds.forEach(bed => {
                                const isCurrent = bed.student_id == studentId;
                                const label = bed.bed_number + (isCurrent ? ' (Current)' : '');
                                bedSelect.innerHTML += `<option value="${bed.id}">${label}</option>`;
                            });
                        }
                        
                        bedSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading beds:', error);
                        bedSelect.innerHTML = '<option value="">Error loading beds</option>';
                        bedSelect.disabled = false;
                    });
            });
        }

        // Form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const roomId = roomSelect.value;
                const bedId = bedSelect.value;

                if (roomId && !bedId) {
                    e.preventDefault();
                    alert('Please select a bed for the assigned room.');
                    bedSelect.focus();
                    return false;
                }
            });
        }
    });
</script>
@endpush