<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update</title>
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
        .status-update {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .status-approved {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .status-under-review {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .status-pending {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
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
        .next-steps {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .next-steps h3 {
            color: #0c5460;
            margin-top: 0;
        }
        .contact-info {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .admin-notes {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 LincHostel</h1>
        <h2>Application Status Update</h2>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $application->full_name }}</strong>,</p>

        <p>We have an update regarding your hostel application.</p>

        <div class="application-number">
            📋 Application Number: {{ $application->application_number }}
        </div>

        <div class="status-update status-{{ $application->status }}">
            <h2>
                @if($application->status === 'approved')
                    ✅ Application Approved!
                @elseif($application->status === 'rejected')
                    ❌ Application Rejected
                @elseif($application->status === 'under_review')
                    🔍 Application Under Review
                @else
                    ⏳ Application Pending
                @endif
            </h2>
            <p><strong>Current Status: {{ ucfirst(str_replace('_', ' ', $application->status)) }}</strong></p>
            @if($application->reviewed_at)
                <p><small>Updated on: {{ $application->reviewed_at->format('M d, Y H:i A') }}</small></p>
            @endif
        </div>

        @if($application->admin_notes)
        <div class="admin_notes">
            <h3>📝 Admin Notes</h3>
            <p>{{ $application->admin_notes }}</p>
        </div>
        @endif

        @if($application->status === 'approved')
        <div class="next-steps">
            <h3>🎉 Congratulations! Next Steps:</h3>
            <ol>
                <li><strong>Room Allocation:</strong> You will be contacted within 2-3 business days for room allocation.</li>
                <li><strong>Check-in Process:</strong> Prepare your documents for the check-in process.</li>
                <li><strong>Hostel Orientation:</strong> Attend the mandatory hostel orientation session.</li>
                <li><strong>Key Collection:</strong> Collect your room keys from the hostel porter.</li>
            </ol>
            <p><strong>Welcome to LincHostel family! 🏠</strong></p>
        </div>
        @elseif($application->status === 'rejected')
        <div class="next-steps">
            <h3>📋 What You Can Do:</h3>
            <ul>
                <li><strong>Review Requirements:</strong> Check if you meet all hostel requirements.</li>
                <li><strong>Reapply:</strong> You may submit a new application if eligible.</li>
                <li><strong>Contact Us:</strong> Reach out for clarification on rejection reasons.</li>
                <li><strong>Alternative Options:</strong> Consider off-campus accommodation options.</li>
            </ul>
        </div>
        @elseif($application->status === 'under_review')
        <div class="next-steps">
            <h3>🔍 Review in Progress:</h3>
            <ul>
                <li><strong>Document Verification:</strong> We are currently verifying your submitted documents.</li>
                <li><strong>Background Check:</strong> Standard background verification is in progress.</li>
                <li><strong>Final Decision:</strong> You will receive the final decision within 2-3 business days.</li>
                <li><strong>Stay Updated:</strong> Keep checking your email for further updates.</li>
            </ul>
        </div>
        @else
        <div class="next-steps">
            <h3>⏳ Application Pending:</h3>
            <ul>
                <li><strong>Queue Position:</strong> Your application is in the review queue.</li>
                <li><strong>Processing Time:</strong> Applications are typically processed within 3-5 business days.</li>
                <li><strong>Document Check:</strong> Ensure all required documents were submitted.</li>
                <li><strong>Patience:</strong> We appreciate your patience during the review process.</li>
            </ul>
        </div>
        @endif

        <div class="info-section">
            <h3>📋 Application Summary</h3>
            <p><strong>Registration Number:</strong> {{ $application->reg_number }}</p>
            <p><strong>Department:</strong> {{ $application->department }}</p>
            <p><strong>Program:</strong> {{ $application->program }}</p>
            <p><strong>Academic Year:</strong> {{ $application->academic_year }}</p>
            <p><strong>Submitted:</strong> {{ $application->created_at->format('M d, Y H:i A') }}</p>
            @if($application->reviewed_by)
                <p><strong>Reviewed by:</strong> {{ $application->reviewer->name ?? 'Admin' }}</p>
            @endif
        </div>

        <div class="contact-info">
            <h3>📞 Need Help?</h3>
            <p><strong>Contact our support team:</strong></p>
            <p>
                📧 Email: <a href="mailto:info@linchostel.com">info@linchostel.com</a><br>
                📱 Phone: +234 (0) 123 456 7890<br>
                🏢 Office: Student Affairs Department, Lincoln University<br>
                🕒 Office Hours: Monday - Friday, 8:00 AM - 5:00 PM
            </p>
        </div>

        <div class="footer">
            <p><strong>Important:</strong> Please keep this email for your records and reference your application number in all communications.</p>
            <p>Thank you for choosing LincHostel!</p>
            <p><em>Lincoln University Hostel Management</em></p>
        </div>
    </div>
</body>
</html>