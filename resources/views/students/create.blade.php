@extends('layouts.admin')

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
                    <div class="mb-4 p-3 bg-light rounded border">
                        <label class="form-label fw-bold">Search Approved Registrations</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="applicant_search" class="form-control" placeholder="Type student name or registration number...">
                        </div>
                        <div id="search_results" class="list-group mt-2 shadow-sm" style="display: none; position: absolute; z-index: 1000; width: calc(100% - 40px);"></div>
                        <small class="text-muted mt-1 d-block italic">Searching will fetch details from approved hostel applications.</small>
                    </div>

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
                                       maxlength="50">
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
                            <label for="program" class="col-md-4 col-form-label text-md-end">
                                {{ __('Program') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-6">
                                <select id="program" 
                                        class="form-select @error('program') is-invalid @enderror" 
                                        name="program" 
                                        required>
                                    <option value="">Select Program</option>
                                    @foreach($programs as $prog)
                                        <option value="{{ $prog->name }}" {{ old('program') == $prog->name ? 'selected' : '' }}>{{ $prog->name }}</option>
                                    @endforeach
                                </select>
                                @error('program')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="department" class="col-md-4 col-form-label text-md-end">
                                {{ __('Department') }}
                            </label>
                            <div class="col-md-6">
                                <input id="department" type="text"
                                       class="form-control @error('department') is-invalid @enderror"
                                       name="department" value="{{ old('department') }}">
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
                                    <option value="" data-start="" data-end="">Select Intake</option>
                                    @foreach($intakes as $intake)
                                        <option value="{{ $intake->name }}" 
                                                data-start="{{ $intake->start_date ? $intake->start_date->format('Y-m-d') : '' }}" 
                                                data-end="{{ $intake->end_date ? $intake->end_date->format('Y-m-d') : '' }}"
                                                {{ old('intake') == $intake->name ? 'selected' : '' }}>
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

                        <hr>
                        <h6 class="mb-3 text-primary">{{ __('Additional Demographic Information') }}</h6>

                        <div class="row mb-3">
                            <label for="date_of_birth" class="col-md-4 col-form-label text-md-end">{{ __('Date of Birth') }}</label>
                            <div class="col-md-6">
                                <input id="date_of_birth" type="date" class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                @error('date_of_birth')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="nationality" class="col-md-4 col-form-label text-md-end">{{ __('Nationality') }}</label>
                            <div class="col-md-6">
                                <input id="nationality" type="text" class="form-control @error('nationality') is-invalid @enderror" name="nationality" value="{{ old('nationality') }}">
                                @error('nationality')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="state_of_origin" class="col-md-4 col-form-label text-md-end">{{ __('State of Origin') }}</label>
                            <div class="col-md-6">
                                <input id="state_of_origin" type="text" class="form-control @error('state_of_origin') is-invalid @enderror" name="state_of_origin" value="{{ old('state_of_origin') }}">
                                @error('state_of_origin')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="local_government" class="col-md-4 col-form-label text-md-end">{{ __('LGA') }}</label>
                            <div class="col-md-6">
                                <input id="local_government" type="text" class="form-control @error('local_government') is-invalid @enderror" name="local_government" value="{{ old('local_government') }}">
                                @error('local_government')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3 text-primary">{{ __('Parent / Guardian Information') }}</h6>

                        <div class="row mb-3">
                            <label for="parent_name" class="col-md-4 col-form-label text-md-end">{{ __('Parent Full Name') }}</label>
                            <div class="col-md-6">
                                <input id="parent_name" type="text" class="form-control @error('parent_name') is-invalid @enderror" name="parent_name" value="{{ old('parent_name') }}">
                                @error('parent_name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="parent_relationship" class="col-md-4 col-form-label text-md-end">{{ __('Relationship') }}</label>
                            <div class="col-md-6">
                                <input id="parent_relationship" type="text" class="form-control @error('parent_relationship') is-invalid @enderror" name="parent_relationship" value="{{ old('parent_relationship') }}">
                                @error('parent_relationship')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="parent_phone" class="col-md-4 col-form-label text-md-end">{{ __('Parent Phone') }}</label>
                            <div class="col-md-6">
                                <input id="parent_phone" type="tel" class="form-control @error('parent_phone') is-invalid @enderror" name="parent_phone" value="{{ old('parent_phone') }}">
                                @error('parent_phone')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="parent_email" class="col-md-4 col-form-label text-md-end">{{ __('Parent Email') }}</label>
                            <div class="col-md-6">
                                <input id="parent_email" type="email" class="form-control @error('parent_email') is-invalid @enderror" name="parent_email" value="{{ old('parent_email') }}">
                                @error('parent_email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3 text-primary">{{ __('Medical Information') }}</h6>

                        <div class="row mb-3">
                            <label for="blood_group" class="col-md-4 col-form-label text-md-end">{{ __('Blood Group') }}</label>
                            <div class="col-md-6">
                                <select id="blood_group" class="form-select @error('blood_group') is-invalid @enderror" name="blood_group">
                                    <option value="">Select Blood Group</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                                @error('blood_group')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="genotype" class="col-md-4 col-form-label text-md-end">{{ __('Genotype') }}</label>
                            <div class="col-md-6">
                                <input id="genotype" type="text" class="form-control @error('genotype') is-invalid @enderror" name="genotype" value="{{ old('genotype') }}" placeholder="e.g. AA, AS, SS">
                                @error('genotype')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="medical_conditions" class="col-md-4 col-form-label text-md-end">{{ __('Medical Conditions') }}</label>
                            <div class="col-md-6">
                                <textarea id="medical_conditions" class="form-control @error('medical_conditions') is-invalid @enderror" name="medical_conditions">{{ old('medical_conditions') }}</textarea>
                                @error('medical_conditions')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_disability" id="has_disability" value="1" {{ old('has_disability') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_disability">
                                        {{ __('Student has a disability') }}
                                    </label>
                                </div>
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

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const applicantSearch = document.getElementById('applicant_search');
        const searchResults = document.getElementById('search_results');
        const studentForm = document.getElementById('studentForm');
        
        // Form fields to auto-fill
        const fields = {
            admission_number: document.getElementById('admission_number'),
            full_name: document.getElementById('full_name'),
            email: document.getElementById('email'),
            gender: document.getElementById('gender'),
            program: document.getElementById('program'),
            department: document.getElementById('department'),
            contact_number: document.getElementById('contact_number'),
            emergency_contact: document.getElementById('emergency_contact'),
            address: document.getElementById('address'),
            intake: document.getElementById('intake'),
            semester: document.getElementById('semester')
        };

        let debounceTimer;

        applicantSearch.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('students.search-applications') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'list-group-item list-group-item-action p-3';
                                btn.innerHTML = `
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-primary">${item.full_name}</div>
                                            <div class="small text-muted">
                                                <i class="fas fa-id-card me-1"></i>${item.student_id} | 
                                                <i class="fas fa-graduation-cap me-1"></i>${item.program}
                                            </div>
                                        </div>
                                        <span class="badge bg-primary rounded-pill"><i class="fas fa-plus"></i></span>
                                    </div>
                                `;
                                btn.onclick = () => selectApplicant(item);
                                searchResults.appendChild(btn);
                            });
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="list-group-item text-muted p-3">No approved registrations found for "' + query + '".</div>';
                            searchResults.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching registrations:', error);
                        searchResults.innerHTML = '<div class="list-group-item text-danger p-3">Error searching for registrations.</div>';
                        searchResults.style.display = 'block';
                    });
            }, 300);
        });

        function selectApplicant(item) {
            fields.admission_number.value = item.student_id || '';
            fields.full_name.value = item.full_name || '';
            fields.email.value = item.email || '';
            
            // Handle selects
            if (item.gender) setSelectedValue(fields.gender, item.gender);
            if (item.program) setSelectedValue(fields.program, item.program);
            fields.department.value = item.department || '';
            if (item.intake) setSelectedValue(fields.intake, item.intake);
            
            // Default Semester to 1 for new students
            setSelectedValue(fields.semester, "1");

            fields.contact_number.value = item.phone_number || '';
            fields.emergency_contact.value = item.emergency_contact_phone || item.parent_phone || '';
            fields.address.value = item.home_address || '';

            searchResults.style.display = 'none';
            applicantSearch.value = item.full_name;
            
            // Highlight filled fields
            Object.values(fields).forEach(field => {
                if (field && field.value) {
                    field.classList.add('is-valid');
                    setTimeout(() => field.classList.remove('is-valid'), 2000);
                }
            });
        }

        function setSelectedValue(selectElement, value) {
            if (!selectElement) return;
            for (let i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value === value || selectElement.options[i].text === value) {
                    selectElement.selectedIndex = i;
                    return;
                }
            }
        }

        // Close results when clicking outside
        document.addEventListener('click', function(e) {
            if (!applicantSearch.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });

        // Existing date logic
        const checkInDate = document.getElementById('check_in_date');
        const checkOutDate = document.getElementById('expected_check_out_date');

        // Auto-fill dates based on Intake selection
        const intakeSelect = document.getElementById('intake');
        if (intakeSelect) {
            intakeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const startDate = selectedOption.getAttribute('data-start');
                const endDate = selectedOption.getAttribute('data-end');

                if (startDate && checkInDate) {
                    checkInDate.value = startDate;
                    // Trigger change to update min for checkout
                    checkInDate.dispatchEvent(new Event('change'));
                }
                
                if (endDate && checkOutDate) {
                    checkOutDate.value = endDate;
                }
                
                // Visual feedback
                if (startDate || endDate) {
                    checkInDate.classList.add('is-valid');
                    checkOutDate.classList.add('is-valid');
                    setTimeout(() => {
                        checkInDate.classList.remove('is-valid');
                        checkOutDate.classList.remove('is-valid');
                    }, 1500);
                }
            });
        }

        if (checkInDate) {
            checkInDate.addEventListener('change', function() {
                if (this.value && checkOutDate) {
                    checkOutDate.min = this.value;
                    if (checkOutDate.value && checkOutDate.value < this.value) {
                        checkOutDate.value = '';
                    }
                }
            });

            if (checkInDate.value && checkOutDate) {
                checkOutDate.min = checkInDate.value;
            }
        }
    });
</script>
@endpush