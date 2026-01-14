<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Application Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
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
        .application-number {
            background: #cc0000;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 18px;
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
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 10px 0;
        }
        .info-item {
            padding: 5px 0;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .contact-info {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 LincHostel</h1>
        <h2>Application Confirmation</h2>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $application->full_name }}</strong>,</p>

        <p>Thank you for submitting your hostel application! We have successfully received your application and it is now being processed.</p>

        <div class="application-number">
            📋 Application Number: {{ $application->application_number }}
        </div>

        <p><strong>Please save this application number for future reference.</strong></p>

        <div class="info-section">
            <h3>📝 Application Summary</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name:</div>
                    <div>{{ $application->full_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Registration Number:</div>
                    <div>{{ $application->reg_number }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Department:</div>
                    <div>{{ $application->department }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Program:</div>
                    <div>{{ $application->program }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Academic Year:</div>
                    <div>{{ $application->academic_year }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Application Status:</div>
                    <div><strong>{{ ucfirst($application->status) }}</strong></div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3>👨‍👩‍👧‍👦 Parent/Guardian Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name:</div>
                    <div>{{ $application->parent_full_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Relationship:</div>
                    <div>{{ ucfirst($application->parent_relationship) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone:</div>
                    <div>{{ $application->parent_phone }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email:</div>
                    <div>{{ $application->parent_email ?: 'Not provided' }}</div>
                </div>
            </div>
        </div>

        @if($application->medical_conditions || $application->allergies || $application->blood_group)
        <div class="info-section">
            <h3>🏥 Medical Information</h3>
            @if($application->blood_group)
            <div class="info-item">
                <div class="info-label">Blood Group:</div>
                <div>{{ $application->blood_group }}</div>
            </div>
            @endif
            @if($application->genotype)
            <div class="info-item">
                <div class="info-label">Genotype:</div>
                <div>{{ $application->genotype }}</div>
            </div>
            @endif
            @if($application->medical_conditions)
            <div class="info-item">
                <div class="info-label">Medical Conditions:</div>
                <div>{{ $application->medical_conditions }}</div>
            </div>
            @endif
            @if($application->allergies)
            <div class="info-item">
                <div class="info-label">Allergies:</div>
                <div>{{ $application->allergies }}</div>
            </div>
            @endif
        </div>
        @endif

        <div class="info-section">
            <h3>📅 Next Steps</h3>
            <ol>
                <li><strong>Application Review:</strong> Our team will review your application within 3-5 business days.</li>
                <li><strong>Document Verification:</strong> We will verify all submitted documents and receipts.</li>
                <li><strong>Status Update:</strong> You will receive an email notification about your application status.</li>
                <li><strong>Room Allocation:</strong> If approved, you will be contacted for room allocation.</li>
            </ol>
        </div>

        <div class="contact-info">
            <h3>📞 Contact Information</h3>
            <p><strong>Need help or have questions?</strong></p>
            <p>
                📧 Email: <a href="mailto:info@linchostel.com">info@linchostel.com</a><br>
                📱 Phone: +234 (0) 123 456 7890<br>
                🏢 Office: Student Affairs Department, Lincoln University
            </p>
        </div>

        <div class="footer">
            <p><strong>Important:</strong> Please keep this email for your records. You may need to reference your application number during the review process.</p>
            <p>Thank you for choosing LincHostel!</p>
            <p><em>Lincoln University Hostel Management</em></p>
        </div>
    </div>
</body>
</html>