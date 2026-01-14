<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Hostel Application Received</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #cc0000, #990000);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .application-number {
            background: #cc0000;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .info-section {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #cc0000;
        }
        .info-section h3 {
            color: #cc0000;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        .info-item {
            padding: 8px 0;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .btn-primary {
            background: #cc0000;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .documents-list {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .documents-list ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .priority-high {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #f0f0f0;
            border-radius: 5px;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 LincHostel Admin Panel</h1>
        <h2>New Application Alert</h2>
    </div>

    <div class="content">
        <div class="alert priority-high">
            <strong>🚨 Action Required:</strong> A new hostel application has been submitted and requires your review.
        </div>

        <div class="application-number">
            📋 Application #{{ $application->application_number }}
        </div>

        <div class="info-section">
            <h3>👤 Student Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name:</div>
                    <div class="info-value">{{ $application->full_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Registration Number:</div>
                    <div class="info-value">{{ $application->reg_number }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $application->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">{{ $application->phone_number }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Gender:</div>
                    <div class="info-value">{{ ucfirst($application->gender) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date of Birth:</div>
                    <div class="info-value">{{ $application->date_of_birth->format('M d, Y') }} ({{ $application->age }} years)</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Department:</div>
                    <div class="info-value">{{ $application->department }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Program:</div>
                    <div class="info-value">{{ $application->program }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Academic Year:</div>
                    <div class="info-value">{{ $application->academic_year }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Intake:</div>
                    <div class="info-value">{{ $application->intake }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">State of Origin:</div>
                    <div class="info-value">{{ $application->state_of_origin }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Amount Paid:</div>
                    <div class="info-value">₦{{ number_format($application->amount_paid) }}</div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3>👨‍👩‍👧‍👦 Parent/Guardian Details</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name:</div>
                    <div class="info-value">{{ $application->parent_full_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Relationship:</div>
                    <div class="info-value">{{ ucfirst($application->parent_relationship) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">{{ $application->parent_phone }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $application->parent_email ?: 'Not provided' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Occupation:</div>
                    <div class="info-value">{{ $application->parent_occupation }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Workplace:</div>
                    <div class="info-value">{{ $application->parent_workplace ?: 'Not provided' }}</div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Address:</div>
                <div class="info-value">{{ $application->parent_address }}</div>
            </div>
        </div>

        <div class="info-section">
            <h3>🚨 Emergency Contact</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name:</div>
                    <div class="info-value">{{ $application->emergency_contact_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">{{ $application->emergency_contact_phone }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Relationship:</div>
                    <div class="info-value">{{ $application->emergency_contact_relationship }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Address:</div>
                <div class="info-value">{{ $application->emergency_contact_address }}</div>
            </div>
        </div>

        @if($application->medical_conditions || $application->allergies || $application->blood_group || $application->has_disability)
        <div class="info-section">
            <h3>🏥 Medical Information</h3>
            <div class="info-grid">
                @if($application->blood_group)
                <div class="info-item">
                    <div class="info-label">Blood Group:</div>
                    <div class="info-value">{{ $application->blood_group }}</div>
                </div>
                @endif
                @if($application->genotype)
                <div class="info-item">
                    <div class="info-label">Genotype:</div>
                    <div class="info-value">{{ $application->genotype }}</div>
                </div>
                @endif
                @if($application->has_disability)
                <div class="info-item">
                    <div class="info-label">Has Disability:</div>
                    <div class="info-value">Yes</div>
                </div>
                @endif
            </div>
            
            @if($application->medical_conditions)
            <div class="info-item">
                <div class="info-label">Medical Conditions:</div>
                <div class="info-value">{{ $application->medical_conditions }}</div>
            </div>
            @endif
            
            @if($application->allergies)
            <div class="info-item">
                <div class="info-label">Allergies:</div>
                <div class="info-value">{{ $application->allergies }}</div>
            </div>
            @endif
            
            @if($application->medications)
            <div class="info-item">
                <div class="info-label">Current Medications:</div>
                <div class="info-value">{{ $application->medications }}</div>
            </div>
            @endif
            
            @if($application->disability_details)
            <div class="info-item">
                <div class="info-label">Disability Details:</div>
                <div class="info-value">{{ $application->disability_details }}</div>
            </div>
            @endif
            
            @if($application->dietary_requirements)
            <div class="info-item">
                <div class="info-label">Dietary Requirements:</div>
                <div class="info-value">{{ $application->dietary_requirements }}</div>
            </div>
            @endif
        </div>
        @endif

        @if($application->preferred_hostel_type || $application->preferred_room_type || $application->special_accommodation_needs)
        <div class="info-section">
            <h3>🏠 Accommodation Preferences</h3>
            @if($application->preferred_hostel_type)
            <div class="info-item">
                <div class="info-label">Preferred Hostel Type:</div>
                <div class="info-value">{{ ucfirst($application->preferred_hostel_type) }}</div>
            </div>
            @endif
            
            @if($application->preferred_room_type)
            <div class="info-item">
                <div class="info-label">Preferred Room Type:</div>
                <div class="info-value">{{ ucfirst($application->preferred_room_type) }}</div>
            </div>
            @endif
            
            @if($application->special_accommodation_needs)
            <div class="info-item">
                <div class="info-label">Special Accommodation Needs:</div>
                <div class="info-value">{{ $application->special_accommodation_needs }}</div>
            </div>
            @endif
        </div>
        @endif

        <div class="documents-list">
            <h3>📎 Submitted Documents</h3>
            <ul>
                @if($application->passport_photo)
                <li>✅ Passport Photo</li>
                @endif
                @if($application->applicationform_receipt)
                <li>✅ Application Form Receipt</li>
                @endif
                @if($application->hostelfee_receipt)
                <li>✅ Hostel Fee Receipt</li>
                @endif
                @if($application->medical_report)
                <li>✅ Medical Report</li>
                @endif
                @if($application->birth_certificate)
                <li>✅ Birth Certificate</li>
                @endif
                @if($application->admission_letter)
                <li>✅ Admission Letter</li>
                @endif
            </ul>
        </div>

        <div class="info-section">
            <h3>📅 Application Timeline</h3>
            <div class="info-item">
                <div class="info-label">Submitted:</div>
                <div class="info-value">{{ $application->created_at->format('M d, Y H:i A') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status:</div>
                <div class="info-value"><strong>{{ ucfirst($application->status) }}</strong></div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                🏠 View in Admin Dashboard
            </a>
            <a href="{{ url('/students') }}" class="btn btn-secondary">
                👥 Manage Students
            </a>
        </div>

        <div class="footer">
            <p><strong>This is an automated notification from LincHostel Management System.</strong></p>
            <p>Please review this application promptly to ensure timely processing.</p>
            <p><em>Generated on {{ now()->format('M d, Y H:i A') }}</em></p>
        </div>
    </div>
</body>
</html>