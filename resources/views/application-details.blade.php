<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details - {{ $application->application_number }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .application-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .header-card {
            background: linear-gradient(135deg, #cc0000, #990000);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(204, 0, 0, 0.3);
        }
        
        .application-number {
            font-size: 24px;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 10px;
        }
        
        .status-card {
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .status-pending {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border: 2px solid #ffc107;
            color: #856404;
        }
        
        .status-approved {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border: 2px solid #28a745;
            color: #155724;
        }
        
        .status-rejected {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border: 2px solid #dc3545;
            color: #721c24;
        }
        
        .status-under_review {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            border: 2px solid #17a2b8;
            color: #0c5460;
        }
        
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #cc0000;
        }
        
        .info-card h5 {
            color: #cc0000;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid #cc0000;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #333;
            font-size: 16px;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #cc0000;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            padding: 15px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -27px;
            top: 20px;
            width: 12px;
            height: 12px;
            background: #cc0000;
            border-radius: 50%;
            border: 3px solid white;
        }
        
        .contact-card {
            background: linear-gradient(135deg, #e8f4fd, #d1ecf1);
            border: 2px solid #17a2b8;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
        }
        
        .btn-custom {
            background: linear-gradient(135deg, #cc0000, #990000);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            background: linear-gradient(135deg, #990000, #660000);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(204, 0, 0, 0.3);
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .application-container {
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>
    <div class="application-container">
        <!-- Header -->
        <div class="header-card">
            <h1><i class="fas fa-home me-2"></i>LincHostel</h1>
            <h3>Application Status</h3>
            <div class="application-number">
                📋 {{ $application->application_number }}
            </div>
        </div>

        <!-- Status Card -->
        <div class="status-card status-{{ $application->status }}">
            <h2>
                @if($application->status === 'approved')
                    <i class="fas fa-check-circle me-2"></i>Application Approved!
                @elseif($application->status === 'rejected')
                    <i class="fas fa-times-circle me-2"></i>Application Rejected
                @elseif($application->status === 'under_review')
                    <i class="fas fa-search me-2"></i>Under Review
                @else
                    <i class="fas fa-clock me-2"></i>Pending Review
                @endif
            </h2>
            <p class="mb-0 fs-5">
                <strong>Current Status: {{ ucfirst(str_replace('_', ' ', $application->status)) }}</strong>
            </p>
            @if($application->reviewed_at)
                <p class="mb-0 mt-2">
                    <small>Last updated: {{ $application->reviewed_at->format('M d, Y H:i A') }}</small>
                </p>
            @endif
        </div>

        <!-- Admin Notes -->
        @if($application->admin_notes)
        <div class="info-card">
            <h5><i class="fas fa-sticky-note"></i>Admin Notes</h5>
            <div class="alert alert-info">
                {{ $application->admin_notes }}
            </div>
        </div>
        @endif

        <!-- Application Summary -->
        <div class="info-card">
            <h5><i class="fas fa-user-graduate"></i>Application Summary</h5>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $application->full_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Student ID</div>
                    <div class="info-value">{{ $application->student_id }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Department</div>
                    <div class="info-value">{{ $application->department }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Program</div>
                    <div class="info-value">{{ $application->program }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Academic Year</div>
                    <div class="info-value">{{ $application->academic_year }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Amount Paid</div>
                    <div class="info-value">₦{{ number_format($application->amount_paid) }}</div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="info-card">
            <h5><i class="fas fa-address-book"></i>Contact Information</h5>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $application->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $application->phone_number }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Emergency Contact</div>
                    <div class="info-value">{{ $application->emergency_contact_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Emergency Phone</div>
                    <div class="info-value">{{ $application->emergency_contact_phone }}</div>
                </div>
            </div>
        </div>

        <!-- Application Timeline -->
        <div class="info-card">
            <h5><i class="fas fa-history"></i>Application Timeline</h5>
            <div class="timeline">
                <div class="timeline-item">
                    <h6><i class="fas fa-paper-plane text-primary me-2"></i>Application Submitted</h6>
                    <p class="mb-0">{{ $application->created_at->format('M d, Y H:i A') }}</p>
                    <small class="text-muted">Your application was successfully submitted and is in our system.</small>
                </div>
                
                @if($application->status !== 'pending')
                <div class="timeline-item">
                    <h6>
                        @if($application->status === 'approved')
                            <i class="fas fa-check text-success me-2"></i>Application Approved
                        @elseif($application->status === 'rejected')
                            <i class="fas fa-times text-danger me-2"></i>Application Rejected
                        @else
                            <i class="fas fa-search text-info me-2"></i>Under Review
                        @endif
                    </h6>
                    <p class="mb-0">{{ $application->reviewed_at->format('M d, Y H:i A') }}</p>
                    <small class="text-muted">
                        @if($application->status === 'approved')
                            Congratulations! Your application has been approved.
                        @elseif($application->status === 'rejected')
                            Your application was not approved at this time.
                        @else
                            Your application is currently being reviewed by our team.
                        @endif
                    </small>
                </div>
                @endif
            </div>
        </div>

        <!-- Next Steps -->
        <div class="info-card">
            <h5><i class="fas fa-list-check"></i>Next Steps</h5>
            @if($application->status === 'approved')
                <div class="alert alert-success">
                    <h6><i class="fas fa-party-horn me-2"></i>Congratulations!</h6>
                    <ul class="mb-0">
                        <li>You will be contacted within 2-3 business days for room allocation</li>
                        <li>Prepare your documents for the check-in process</li>
                        <li>Attend the mandatory hostel orientation session</li>
                        <li>Collect your room keys from the hostel porter</li>
                    </ul>
                </div>
            @elseif($application->status === 'rejected')
                <div class="alert alert-warning">
                    <h6><i class="fas fa-info-circle me-2"></i>What you can do:</h6>
                    <ul class="mb-0">
                        <li>Review the hostel requirements and eligibility criteria</li>
                        <li>Contact us for clarification on the rejection reasons</li>
                        <li>Consider reapplying if you become eligible</li>
                        <li>Explore alternative accommodation options</li>
                    </ul>
                </div>
            @elseif($application->status === 'under_review')
                <div class="alert alert-info">
                    <h6><i class="fas fa-clock me-2"></i>Review in Progress:</h6>
                    <ul class="mb-0">
                        <li>We are currently verifying your submitted documents</li>
                        <li>Standard background verification is in progress</li>
                        <li>You will receive the final decision within 2-3 business days</li>
                        <li>Keep checking your email for updates</li>
                    </ul>
                </div>
            @else
                <div class="alert alert-primary">
                    <h6><i class="fas fa-hourglass-half me-2"></i>Application Pending:</h6>
                    <ul class="mb-0">
                        <li>Your application is in the review queue</li>
                        <li>Applications are typically processed within 3-5 business days</li>
                        <li>Ensure all required documents were submitted correctly</li>
                        <li>We appreciate your patience during the review process</li>
                    </ul>
                </div>
            @endif
        </div>

        <!-- Contact Support -->
        <div class="contact-card">
            <h5><i class="fas fa-headset me-2"></i>Need Help?</h5>
            <p>If you have any questions about your application or need assistance, please contact our support team.</p>
            <div class="row text-start">
                <div class="col-md-6">
                    <p><i class="fas fa-envelope me-2"></i><strong>Email:</strong><br>info@linchostel.com</p>
                    <p><i class="fas fa-phone me-2"></i><strong>Phone:</strong><br>+234 (0) 123 456 7890</p>
                </div>
                <div class="col-md-6">
                    <p><i class="fas fa-building me-2"></i><strong>Office:</strong><br>Student Affairs Department<br>Lincoln University</p>
                    <p><i class="fas fa-clock me-2"></i><strong>Hours:</strong><br>Monday - Friday<br>8:00 AM - 5:00 PM</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-4">
            <a href="/" class="btn-custom me-3">
                <i class="fas fa-home me-2"></i>Back to Home
            </a>
            <a href="/hostel/apply" class="btn-custom">
                <i class="fas fa-plus me-2"></i>New Application
            </a>
        </div>

        <!-- Footer -->
        <div class="text-center mt-5 mb-3">
            <p class="text-muted">
                <small>
                    <strong>Important:</strong> Please save this page or bookmark it for future reference.<br>
                    Your application number is: <strong>{{ $application->application_number }}</strong>
                </small>
            </p>
            <p class="text-muted">
                <small>&copy; {{ date('Y') }} LincHostel - Lincoln University Hostel Management</small>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>