<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LincHostel | Comprehensive Hostel Application Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

     <!-- Home Page CSS File -->
    <link href="{{ asset('assets/css/apply.css') }}" rel="stylesheet">
    
    <style>
        .form-card {
            background: var(--form-bg-light);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color-light);
            height: 100%;
            transition: all 0.3s ease;
        }

        body[data-theme="dark"] .form-card {
            background: var(--form-bg-dark);
            border-color: var(--border-color-dark);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .form-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        body[data-theme="dark"] .form-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 15px 20px;
            margin: -25px -25px 20px -25px;
            border-radius: 12px 12px 0 0;
            border: none;
        }

        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            font-size: 16px;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .section-title {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .declaration-card {
            background: var(--declaration-bg-light);
            border-left: 4px solid var(--primary-color);
        }

        body[data-theme="dark"] .declaration-card {
            background: var(--declaration-bg-dark);
        }

        .file-input-group {
            position: relative;
        }

        .file-input-group input[type="file"] {
            padding: 12px 15px;
            border: 2px dashed var(--border-color-light);
            border-radius: 8px;
            background: var(--form-bg-light);
            transition: all 0.3s ease;
        }

        body[data-theme="dark"] .file-input-group input[type="file"] {
            border-color: var(--border-color-dark);
            background: var(--form-bg-dark);
        }

        .file-input-group input[type="file"]:hover {
            border-color: var(--primary-color);
            background: rgba(204, 0, 0, 0.02);
        }

        body[data-theme="dark"] .file-input-group input[type="file"]:hover {
            background: rgba(204, 0, 0, 0.05);
        }

        .submit-section {
            text-align: center;
            padding: 30px 0;
            border-top: 1px solid var(--border-color-light);
            margin-top: 20px;
        }

        body[data-theme="dark"] .submit-section {
            border-top-color: var(--border-color-dark);
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            padding: 15px 50px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            min-width: 250px;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, var(--primary-hover), #660000);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(204, 0, 0, 0.3);
        }

        .account-details-btn {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            width: 100%;
        }

        .account-details-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }

        .form-row {
            margin-bottom: 15px;
        }

        .progress-indicator {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
        }

        .step.active .step-number {
            background: var(--primary-color);
        }

        .step.completed .step-number {
            background: #28a745;
        }

        .step-title {
            font-size: 12px;
            font-weight: 500;
            color: #6c757d;
        }

        .step.active .step-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        .required-field {
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .form-card {
                margin-bottom: 20px;
                padding: 20px;
            }
            
            .card-header {
                margin: -20px -20px 15px -20px;
                padding: 12px 15px;
            }
            
            .section-title {
                font-size: 20px;
            }
            
            .submit-btn {
                width: 100%;
                min-width: auto;
            }

            .progress-steps {
                flex-direction: column;
                gap: 10px;
            }

            .step {
                display: flex;
                align-items: center;
                gap: 15px;
                text-align: left;
            }

            .step-number {
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- ======= Hostel Application Section ======= -->
<section id="hostel-application-form" class="hostel-application-form">

  <div class="info-button-container" tabindex="0" aria-label="Hostel application info">
    <div class="info-button">i</div>
    <div class="info-tooltip" role="tooltip">
      STUDENTS register for hostel at the student affairs department and fill hostel application form for 2000 naira only.
            <br/><br/>
      <b>PAYMENT:</b> Students make payment online for a semester/year (may generate receipt online).
            <br/><br/>
      <b>RECEIPT:</b> Student obtains both transaction and receipt from Finance Department after payment approval.
            <br/><br/>
      <b>STUDENT AFFAIRS:</b> Student presents receipt and fills necessary information on the form.
            <br/><br/>
      <b>STUDENT AFFAIRS:</b> Student processes full documentation and collects hostel clearance form.
            <br/><br/>
      <b>HOSTEL PORTER:</b> Student submits clearance form, receives room allocation, and signs in.
            <br/><br/>
      <b>VACATION:</b> Students submit keys to the porter, sign out, and move out of the hostel.
    </div>
  </div>

  <a href="/"><img src="{{ asset('assets/img/favicon.ico') }}" alt="Lincoln Logo" style="height: 100px; width: 200px; margin-right: 20px; margin-bottom:20px; border-radius: 10px;"></a>
      
  <h2>Comprehensive Hostel Application Form</h2>

  <!-- Progress Indicator -->
  <div class="progress-indicator">
    <div class="progress-steps">
      <div class="step active">
        <div class="step-number">1</div>
        <div class="step-title">Student Info</div>
      </div>
      <div class="step">
        <div class="step-number">2</div>
        <div class="step-title">Parent/Guardian</div>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <div class="step-title">Medical Report</div>
      </div>
      <div class="step">
        <div class="step-number">4</div>
        <div class="step-title">Documents</div>
      </div>
      <div class="step">
        <div class="step-number">5</div>
        <div class="step-title">Declaration</div>
      </div>
    </div>
    <div class="progress">
      <div class="progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </div>

  @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show">
          <i class="fas fa-check-circle me-2"></i>
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif

  @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show">
          <i class="fas fa-exclamation-triangle me-2"></i>
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif

  @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show">
          <strong><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following errors:</strong>
          <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif

  <form id="hostelApplicationForm" action="{{ route('hostel.apply') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- SECTION 1: STUDENT INFORMATION -->
    <div class="form-section" id="section-1">
        <h3 class="section-title">
            <i class="fas fa-user-graduate"></i>
            1. STUDENT INFORMATION
        </h3>
        
        <div class="row">
            <!-- Personal Details Card -->
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-id-card"></i>
                            Personal Details
                        </h4>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">Full Name <span class="required-field">*</span></label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="reg_number">Registration Number <span class="required-field">*</span></label>
                        <input type="text" name="reg_number" id="reg_number" value="{{ old('reg_number') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">Gender <span class="required-field">*</span></label>
                                <select name="gender" id="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth <span class="required-field">*</span></label>
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone_number">Phone Number <span class="required-field">*</span></label>
                                <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email Address <span class="required-field">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Details Card -->
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-graduation-cap"></i>
                            Academic Details
                        </h4>
                    </div>
                    
                    <div class="form-group">
                        <label for="academic_year">Academic Year <span class="required-field">*</span></label>
                        <input type="text" name="academic_year" id="academic_year" placeholder="e.g. 2024-2025" value="{{ old('academic_year') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="intake">Intake <span class="required-field">*</span></label>
                        <input type="text" name="intake" id="intake" value="{{ old('intake') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="program">Program <span class="required-field">*</span></label>
                        <input type="text" name="program" id="program" value="{{ old('program') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="department">Department <span class="required-field">*</span></label>
                        <input type="text" name="department" id="department" value="{{ old('department') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="amount_paid">Amount Paid (₦) <span class="required-field">*</span></label>
                        <input type="text" name="amount_paid" id="amount_paid" value="{{ old('amount_paid') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Details -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-map-marker-alt"></i>
                            Location & Address Details
                        </h4>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nationality">Nationality <span class="required-field">*</span></label>
                                <input type="text" name="nationality" id="nationality" value="{{ old('nationality', 'Nigerian') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="state_of_origin">State of Origin <span class="required-field">*</span></label>
                                <input type="text" name="state_of_origin" id="state_of_origin" value="{{ old('state_of_origin') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="local_government">Local Government <span class="required-field">*</span></label>
                                <input type="text" name="local_government" id="local_government" value="{{ old('local_government') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="home_address">Home Address <span class="required-field">*</span></label>
                        <textarea name="home_address" id="home_address" rows="3" required>{{ old('home_address') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SECTION 2: PARENT/GUARDIAN DETAILS -->
    <div class="form-section" id="section-2">
        <h3 class="section-title">
            <i class="fas fa-users"></i>
            2. PARENT/GUARDIAN DETAILS
        </h3>
        
        <div class="row">
            <!-- Parent Information Card -->
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-user-friends"></i>
                            Parent/Guardian Information
                        </h4>
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_full_name">Full Name <span class="required-field">*</span></label>
                        <input type="text" name="parent_full_name" id="parent_full_name" value="{{ old('parent_full_name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="parent_relationship">Relationship <span class="required-field">*</span></label>
                        <select name="parent_relationship" id="parent_relationship" required>
                            <option value="">Select Relationship</option>
                            <option value="father" {{ old('parent_relationship') == 'father' ? 'selected' : '' }}>Father</option>
                            <option value="mother" {{ old('parent_relationship') == 'mother' ? 'selected' : '' }}>Mother</option>
                            <option value="guardian" {{ old('parent_relationship') == 'guardian' ? 'selected' : '' }}>Guardian</option>
                            <option value="uncle" {{ old('parent_relationship') == 'uncle' ? 'selected' : '' }}>Uncle</option>
                            <option value="aunt" {{ old('parent_relationship') == 'aunt' ? 'selected' : '' }}>Aunt</option>
                            <option value="sibling" {{ old('parent_relationship') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                            <option value="other" {{ old('parent_relationship') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parent_phone">Phone Number <span class="required-field">*</span></label>
                                <input type="tel" name="parent_phone" id="parent_phone" value="{{ old('parent_phone') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parent_email">Email Address</label>
                                <input type="email" name="parent_email" id="parent_email" value="{{ old('parent_email') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="parent_address">Address <span class="required-field">*</span></label>
                        <textarea name="parent_address" id="parent_address" rows="3" required>{{ old('parent_address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Occupation Details Card -->
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-briefcase"></i>
                            Occupation Details
                        </h4>
                    </div>
                    
                    <div class="form-group">
                        <label for="parent_occupation">Occupation <span class="required-field">*</span></label>
                        <input type="text" name="parent_occupation" id="parent_occupation" value="{{ old('parent_occupation') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="parent_workplace">Workplace/Company</label>
                        <input type="text" name="parent_workplace" id="parent_workplace" value="{{ old('parent_workplace') }}">
                    </div>

                    <!-- Emergency Contact (if different from parent) -->
                    <div class="mt-4">
                        <h6><i class="fas fa-phone-alt text-danger me-2"></i>Emergency Contact</h6>
                        <small class="text-muted">If different from parent/guardian above</small>
                        
                        <div class="form-group">
                            <label for="emergency_contact_name">Emergency Contact Name <span class="required-field">*</span></label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_phone">Phone Number <span class="required-field">*</span></label>
                                    <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_relationship">Relationship <span class="required-field">*</span></label>
                                    <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="emergency_contact_address">Emergency Contact Address <span class="required-field">*</span></label>
                            <textarea name="emergency_contact_address" id="emergency_contact_address" rows="2" required>{{ old('emergency_contact_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 3: MEDICAL REPORT -->
    <div class="form-section" id="section-3">
        <h3 class="section-title">
            <i class="fas fa-heartbeat"></i>
            3. MEDICAL REPORT & HEALTH INFORMATION
        </h3>
        
        <div class="row">
            <!-- Medical Conditions Card -->
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-stethoscope"></i>
                            Medical Conditions & Allergies
                        </h4>
                    </div>
                    
                    <div class="form-group">
                        <label for="medical_conditions">Medical Conditions</label>
                        <textarea name="medical_conditions" id="medical_conditions" rows="3" placeholder="List any chronic conditions, disabilities, or ongoing medical issues">{{ old('medical_conditions') }}</textarea>
                        <small class="text-muted">Leave blank if none</small>
                    </div>

                    <div class="form-group">
                        <label for="allergies">Allergies</label>
                        <textarea name="allergies" id="allergies" rows="2" placeholder="Food allergies, drug allergies, environmental allergies, etc.">{{ old('allergies') }}</textarea>
                        <small class="text-muted">Leave blank if none</small>
                    </div>

                    <div class="form-group">
                        <label for="medications">Current Medications</label>
                        <textarea name="medications" id="medications" rows="2" placeholder="List all medications you are currently taking">{{ old('medications') }}</textarea>
                        <small class="text-muted">Leave blank if none</small>
                    </div>

                    <div class="form-group">
                        <label for="dietary_requirements">Dietary Requirements</label>
                        <textarea name="dietary_requirements" id="dietary_requirements" rows="2" placeholder="Special dietary needs, religious restrictions, etc.">{{ old('dietary_requirements') }}</textarea>
                        <small class="text-muted">Leave blank if none</small>
                    </div>
                </div>
            </div>

            <!-- Health Details Card -->
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-user-md"></i>
                            Health Details
                        </h4>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="blood_group">Blood Group</label>
                                <select name="blood_group" id="blood_group">
                                    <option value="">Select Blood Group</option>
                                    <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="genotype">Genotype</label>
                                <select name="genotype" id="genotype">
                                    <option value="">Select Genotype</option>
                                    <option value="AA" {{ old('genotype') == 'AA' ? 'selected' : '' }}>AA</option>
                                    <option value="AS" {{ old('genotype') == 'AS' ? 'selected' : '' }}>AS</option>
                                    <option value="SS" {{ old('genotype') == 'SS' ? 'selected' : '' }}>SS</option>
                                    <option value="AC" {{ old('genotype') == 'AC' ? 'selected' : '' }}>AC</option>
                                    <option value="SC" {{ old('genotype') == 'SC' ? 'selected' : '' }}>SC</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="has_disability" id="has_disability" value="1" class="form-check-input" {{ old('has_disability') ? 'checked' : '' }}>
                            <label for="has_disability" class="form-check-label">I have a disability or special needs</label>
                        </div>
                    </div>

                    <div class="form-group" id="disability-details" style="display: none;">
                        <label for="disability_details">Disability Details</label>
                        <textarea name="disability_details" id="disability_details" rows="3" placeholder="Please describe your disability and any special accommodations needed">{{ old('disability_details') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="special_accommodation_needs">Special Accommodation Needs</label>
                        <textarea name="special_accommodation_needs" id="special_accommodation_needs" rows="2" placeholder="Any special room requirements or accommodations">{{ old('special_accommodation_needs') }}</textarea>
                    </div>

                    <!-- Accommodation Preferences -->
                    <div class="mt-3">
                        <h6><i class="fas fa-bed text-primary me-2"></i>Accommodation Preferences</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preferred_hostel_type">Preferred Hostel Type</label>
                                    <select name="preferred_hostel_type" id="preferred_hostel_type">
                                        <option value="">No Preference</option>
                                        <option value="male" {{ old('preferred_hostel_type') == 'male' ? 'selected' : '' }}>Male Only</option>
                                        <option value="female" {{ old('preferred_hostel_type') == 'female' ? 'selected' : '' }}>Female Only</option>
                                        <option value="mixed" {{ old('preferred_hostel_type') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preferred_room_type">Preferred Room Type</label>
                                    <select name="preferred_room_type" id="preferred_room_type">
                                        <option value="">No Preference</option>
                                        <option value="single" {{ old('preferred_room_type') == 'single' ? 'selected' : '' }}>Single Room</option>
                                        <option value="double" {{ old('preferred_room_type') == 'double' ? 'selected' : '' }}>Double Room</option>
                                        <option value="triple" {{ old('preferred_room_type') == 'triple' ? 'selected' : '' }}>Triple Room</option>
                                        <option value="quad" {{ old('preferred_room_type') == 'quad' ? 'selected' : '' }}>Quad Room</option>
                                        <option value="dormitory" {{ old('preferred_room_type') == 'dormitory' ? 'selected' : '' }}>Dormitory</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 4: DOCUMENTS -->
    <div class="form-section" id="section-4">
        <h3 class="section-title">
            <i class="fas fa-file-upload"></i>
            4. REQUIRED DOCUMENTS
        </h3>
        
        <div class="row">
            <!-- Required Documents Card -->
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-file-alt"></i>
                            Required Documents <span class="required-field">*</span>
                        </h4>
                    </div>
                    
                    <div class="form-group file-input-group">
                        <label for="passport_photo">Passport Photo <span class="required-field">*</span></label>
                        <input type="file" name="passport_photo" id="passport_photo" accept="image/*" required>
                        <small class="text-muted">Upload a clear passport-sized photograph (JPG, PNG)</small>
                    </div>

                    <div class="form-group file-input-group">
                        <label for="applicationform_receipt">Application Form Receipt <span class="required-field">*</span></label>
                        <input type="file" name="applicationform_receipt" id="applicationform_receipt" accept="image/*,application/pdf" required>
                        <small class="text-muted">Receipt for ₦2,000 application form fee</small>
                    </div>

                    <div class="form-group file-input-group">
                        <label for="hostelfee_receipt">Hostel Fees Receipt <span class="required-field">*</span></label>
                        <input type="file" name="hostelfee_receipt" id="hostelfee_receipt" accept="image/*,application/pdf" required>
                        <small class="text-muted">Receipt for ₦180,000 (semester) or ₦400,000 (year)</small>
                    </div>

                    <button type="button" class="account-details-btn" data-bs-toggle="modal" data-bs-target="#paymentDetailsModal">
                        <i class="fas fa-info-circle me-2"></i>View Payment Account Details
                    </button>
                </div>
            </div>

            <!-- Optional Documents Card -->
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-file-medical"></i>
                            Additional Documents
                        </h4>
                    </div>
                    
                    <div class="form-group file-input-group">
                        <label for="medical_report">Medical Report</label>
                        <input type="file" name="medical_report" id="medical_report" accept="image/*,application/pdf">
                        <small class="text-muted">Recent medical examination report (recommended)</small>
                    </div>

                    <div class="form-group file-input-group">
                        <label for="birth_certificate">Birth Certificate</label>
                        <input type="file" name="birth_certificate" id="birth_certificate" accept="image/*,application/pdf">
                        <small class="text-muted">Copy of birth certificate or age declaration</small>
                    </div>

                    <div class="form-group file-input-group">
                        <label for="admission_letter">Admission Letter</label>
                        <input type="file" name="admission_letter" id="admission_letter" accept="image/*,application/pdf">
                        <small class="text-muted">Copy of university admission letter</small>
                    </div>

                    <!-- Additional Information -->
                    <div class="mt-4">
                        <h6><i class="fas fa-info-circle text-info me-2"></i>Additional Information</h6>
                        
                        <div class="form-group">
                            <label for="previous_hostel_experience">Previous Hostel Experience</label>
                            <textarea name="previous_hostel_experience" id="previous_hostel_experience" rows="2" placeholder="Have you lived in a hostel before? Describe your experience">{{ old('previous_hostel_experience') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="why_choose_hostel">Why Choose Hostel Accommodation?</label>
                            <textarea name="why_choose_hostel" id="why_choose_hostel" rows="2" placeholder="Why do you prefer hostel accommodation over other options?">{{ old('why_choose_hostel') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 5: DECLARATION -->
    <div class="form-section" id="section-5">
        <h3 class="section-title">
            <i class="fas fa-file-signature"></i>
            5. DECLARATION & SIGNATURES
        </h3>
        
        <div class="row">
            <!-- Declaration Card -->
            <div class="col-md-6">
                <div class="form-card declaration-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-handshake"></i>
                            Student Declaration
                        </h4>
                    </div>
                    
                    <div class="form-group declaration">
                        <p><strong>I, <input type="text" name="declaration_name" placeholder="Your Full Name" value="{{ old('declaration_name') }}" required style="display: inline; width: auto; border: none; border-bottom: 1px solid #ccc; background: transparent;"> declare that:</strong></p>
                        
                        <ul class="mt-3">
                            <li>All information provided in this application is true and accurate to the best of my knowledge.</li>
                            <li>I understand that providing false information may result in the rejection of my application or termination of accommodation.</li>
                            <li>I agree to abide by all hostel rules and regulations set by the institution.</li>
                            <li>I understand that hostel accommodation is subject to availability and approval.</li>
                            <li>I consent to the processing of my personal data for accommodation purposes.</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label for="applicant_signature">Applicant Signature <span class="required-field">*</span></label>
                        <input type="text" name="applicant_signature" id="applicant_signature" placeholder="Type your full name as signature" value="{{ old('applicant_signature') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="applicant_date">Date <span class="required-field">*</span></label>
                        <input type="date" name="applicant_date" id="applicant_date" value="{{ old('applicant_date', date('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

            <!-- Guardian Declaration Card -->
            <div class="col-md-6">
                <div class="form-card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-user-shield"></i>
                            Parent/Guardian Consent
                        </h4>
                    </div>
                    
                    <div class="form-group declaration">
                        <p><strong>Parent/Guardian Declaration:</strong></p>
                        
                        <ul class="mt-3">
                            <li>I confirm that I am the parent/guardian of the above-named applicant.</li>
                            <li>I give my consent for my child/ward to reside in the hostel accommodation.</li>
                            <li>I understand and accept the hostel rules and regulations.</li>
                            <li>I agree to be responsible for any damages caused by my child/ward.</li>
                            <li>I authorize the hostel management to take necessary medical action in case of emergency.</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label for="guardian_signature">Parent/Guardian Signature <span class="required-field">*</span></label>
                        <input type="text" name="guardian_signature" id="guardian_signature" placeholder="Type parent/guardian full name as signature" value="{{ old('guardian_signature') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="guardian_date">Date <span class="required-field">*</span></label>
                        <input type="date" name="guardian_date" id="guardian_date" value="{{ old('guardian_date', date('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Final Notes and Submission -->
    <div class="submit-section">
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle me-2"></i>Important Notes</h5>
            <ul class="mb-0">
                <li>Ensure all information is accurate before submitting.</li>
                <li>After submission, you will receive an application number for tracking.</li>
                <li>Your application will be reviewed by the hostel management team.</li>
                <li>You will be notified via email and phone about the status of your application.</li>
                <li>Keep your application number safe for future reference.</li>
            </ul>
        </div>
        
        <p class="text-center mb-4">
            <strong>For any issues or questions, please contact the hostel management office.</strong><br>
            <em>Thank you for choosing LincHostel!</em>
        </p>
        
        <button type="submit" class="submit-btn">
            <i class="fas fa-paper-plane me-2"></i>Submit Complete Application
        </button>
    </div>
  </form>

</section>
<!-- ======= End Hostel Application Section ======= -->

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentDetailsModalLabel">
          <i class="fas fa-university me-2"></i> Bank Account Details for Payment
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          Please make your payment to the account details below and upload your payment receipt through the form.
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th><i class="fas fa-university me-1"></i>Bank Name</th>
                <th><i class="fas fa-hashtag me-1"></i>Account Number</th>
                <th><i class="fas fa-user me-1"></i>Account Name</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>EcoBank Nigeria PLC</strong></td>
                <td><code class="fs-5">3680086084</code></td>
                <td><strong>Lincoln Logistics Service Limited</strong></td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="row mt-4">
          <div class="col-md-6">
            <div class="alert alert-warning">
              <h6><i class="fas fa-money-bill-wave me-2"></i>Application Form Fee</h6>
              <p class="mb-0"><strong>Amount: ₦2,000</strong><br>
              <small>One-time application processing fee</small></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="alert alert-success">
              <h6><i class="fas fa-home me-2"></i>Hostel Accommodation Fee</h6>
              <p class="mb-0">
                <strong>Semester: ₦180,000</strong><br>
                <strong>Full Year: ₦400,000</strong><br>
                <small>Choose based on your preference</small>
              </p>
            </div>
          </div>
        </div>
        
        <div class="alert alert-danger mt-3">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <strong>Important:</strong> After making your payment, kindly upload your payment receipt using the form above for verification. Both receipts (application form fee and hostel fee) are required.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Font Awesome JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

<!-- Form Enhancement Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Theme Management
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.body.setAttribute('data-theme', savedTheme);
    
    if (window.matchMedia && !localStorage.getItem('theme')) {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        document.body.setAttribute('data-theme', mediaQuery.matches ? 'dark' : 'light');
        
        mediaQuery.addEventListener('change', function(e) {
            if (!localStorage.getItem('theme')) {
                document.body.setAttribute('data-theme', e.matches ? 'dark' : 'light');
            }
        });
    }

    // Disability details toggle
    const disabilityCheckbox = document.getElementById('has_disability');
    const disabilityDetails = document.getElementById('disability-details');
    
    function toggleDisabilityDetails() {
        if (disabilityCheckbox.checked) {
            disabilityDetails.style.display = 'block';
            document.getElementById('disability_details').required = true;
        } else {
            disabilityDetails.style.display = 'none';
            document.getElementById('disability_details').required = false;
            document.getElementById('disability_details').value = '';
        }
    }
    
    disabilityCheckbox.addEventListener('change', toggleDisabilityDetails);
    toggleDisabilityDetails(); // Initialize on page load

    // Form progress tracking
    const sections = document.querySelectorAll('.form-section');
    const steps = document.querySelectorAll('.step');
    const progressBar = document.querySelector('.progress-bar');
    
    function updateProgress() {
        let completedSections = 0;
        
        sections.forEach((section, index) => {
            const requiredFields = section.querySelectorAll('input[required], select[required], textarea[required]');
            let filledFields = 0;
            
            requiredFields.forEach(field => {
                if (field.type === 'checkbox') {
                    if (field.checked) filledFields++;
                } else if (field.value.trim() !== '') {
                    filledFields++;
                }
            });
            
            const isComplete = filledFields === requiredFields.length;
            
            if (isComplete) {
                completedSections++;
                steps[index].classList.add('completed');
                steps[index].classList.remove('active');
            } else {
                steps[index].classList.remove('completed');
            }
        });
        
        // Update progress bar
        const progressPercentage = (completedSections / sections.length) * 100;
        progressBar.style.width = progressPercentage + '%';
        progressBar.setAttribute('aria-valuenow', progressPercentage);
        
        // Update active step
        steps.forEach(step => step.classList.remove('active'));
        if (completedSections < sections.length) {
            steps[completedSections].classList.add('active');
        }
    }
    
    // Add event listeners to all form inputs
    const allInputs = document.querySelectorAll('input, select, textarea');
    allInputs.forEach(input => {
        input.addEventListener('input', updateProgress);
        input.addEventListener('change', updateProgress);
    });
    
    // Initial progress update
    updateProgress();

    // Auto-fill emergency contact with parent info
    const parentName = document.getElementById('parent_full_name');
    const parentPhone = document.getElementById('parent_phone');
    const parentAddress = document.getElementById('parent_address');
    const parentRelationship = document.getElementById('parent_relationship');
    
    const emergencyName = document.getElementById('emergency_contact_name');
    const emergencyPhone = document.getElementById('emergency_contact_phone');
    const emergencyAddress = document.getElementById('emergency_contact_address');
    const emergencyRelationship = document.getElementById('emergency_contact_relationship');
    
    function autoFillEmergencyContact() {
        if (emergencyName.value === '' && parentName.value !== '') {
            emergencyName.value = parentName.value;
        }
        if (emergencyPhone.value === '' && parentPhone.value !== '') {
            emergencyPhone.value = parentPhone.value;
        }
        if (emergencyAddress.value === '' && parentAddress.value !== '') {
            emergencyAddress.value = parentAddress.value;
        }
        if (emergencyRelationship.value === '' && parentRelationship.value !== '') {
            emergencyRelationship.value = parentRelationship.value;
        }
    }
    
    [parentName, parentPhone, parentAddress, parentRelationship].forEach(field => {
        field.addEventListener('blur', autoFillEmergencyContact);
    });

    // Form validation enhancement
    const form = document.getElementById('hostelApplicationForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (field.type === 'checkbox') {
                if (!field.checked && field.hasAttribute('required')) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            } else if (field.value.trim() === '') {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields before submitting.');
            
            // Scroll to first invalid field
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }
    });

    // File upload preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                
                // Create or update file info display
                let fileInfo = this.parentNode.querySelector('.file-info');
                if (!fileInfo) {
                    fileInfo = document.createElement('small');
                    fileInfo.className = 'file-info text-success d-block mt-1';
                    this.parentNode.appendChild(fileInfo);
                }
                
                fileInfo.innerHTML = `<i class="fas fa-check-circle me-1"></i>Selected: ${fileName} (${fileSize} MB)`;
            }
        });
    });
});
</script>

</body>
</html>