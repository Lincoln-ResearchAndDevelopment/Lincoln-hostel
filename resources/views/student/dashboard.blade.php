<!DOCTYPE html>
<html lang="en">
<head>
    <script>
      (function() {
        // Get saved theme from localStorage or detect system preference
        const savedTheme = localStorage.getItem('theme');
        let theme = 'light'; // default
        
        if (savedTheme) {
          theme = savedTheme;
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
          theme = 'dark';
        }
        
        // Apply theme immediately to prevent flash
        document.documentElement.setAttribute('data-theme', theme);
        
        // Also set a class on html for immediate CSS targeting
        document.documentElement.className = theme + '-theme';
        
        // Store for later use
        window.__INITIAL_THEME__ = theme;
      })();
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LincHostel | Student Dashboard</title>

    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Student Dashboard CSS File -->
    <link href="{{ asset('assets/css/student.css') }}" rel="stylesheet">

    <style>
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
        }

            /* Close button for mobile sidebar */
            .close-sidebar {
                position: absolute;
                top: 8px;
                right: 8px;
                background: none;
                border: none;
                font-size: 1.25rem;
                color: var(--text-primary);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.35rem;
                border-radius: 6px;
                cursor: pointer;
            }

            @media (min-width: 769px) {
                .close-sidebar { display: none; }
            }

            /* Prevent body scroll when menu is open on mobile */
            body.no-scroll {
                overflow: hidden;
            }

        .sidebar-brand {
            color: #000000;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-brand i {
            color: #000000;
        }

        .sidebar-nav {
            padding: 1rem 0;
            background: #ffffff;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            color: #2d3748;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            border-radius: 0;
            border-left: 3px solid transparent;
            background: #ffffff;
        }

        .nav-link:hover {
            color: #000000;
            background: #f7fafc;
            border-left-color: #000000;
        }

        .nav-link.active {
            color: #000000;
            background: #f7fafc;
            border-left-color: #000000;
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            color: #4a5568;
        }

        .nav-link.active i,
        .nav-link:hover i {
            color: #000000;
        }

        /* Top Header Bar */
        .top-header {
            background: var(--navbar-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: var(--card-shadow);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Menu Toggle Button */
        .menu-toggle {
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-toggle:hover {
            background: var(--bg-tertiary);
            border-color: var(--primary-color);
        }

        /* Enhanced Theme Toggle */
        .theme-toggle {
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .theme-toggle:hover {
            background: var(--primary-color);
            color: var(--bg-primary);
            transform: translateY(-1px);
        }

        /* Notification Dropdown */
        .notification-dropdown .dropdown-toggle {
            background: none;
            border: none;
            color: var(--text-primary);
            padding: 0.5rem;
            border-radius: 6px;
            position: relative;
            transition: all 0.3s ease;
        }

        .notification-dropdown .dropdown-toggle:hover {
            background: var(--bg-tertiary);
        }

        .notification-badge {
            background: linear-gradient(45deg, #dc2626, #ef4444);
            border: 2px solid var(--navbar-bg);
            font-size: 0.7rem;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: -5px;
            right: -5px;
            color: white;
        }

        /* Enhanced Dropdown Menu */
        .dropdown-menu {
            background: var(--dropdown-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 12px !important;
            box-shadow: var(--card-shadow-hover);
            margin-top: 0.5rem !important;
            overflow: hidden;
        }

        .dropdown-header {
            background: var(--primary-color) !important;
            color: var(--bg-primary) !important;
            font-weight: 600;
            border: none !important;
        }

        .dropdown-item {
            padding: 0.75rem 1rem !important;
            border-radius: 0 !important;
            transition: all 0.2s ease;
            color: var(--text-primary) !important;
        }

        .dropdown-item:hover {
            background: var(--bg-tertiary) !important;
            color: var(--text-primary) !important;
            transform: translateX(2px);
        }

        /* Main Content Area */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background: var(--bg-secondary);
        }

        .main-content.sidebar-open {
            margin-left: 280px;
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                box-shadow: none;
            }
            
            .main-content.sidebar-open {
                margin-left: 0;
            }
            
            .header-content {
                flex-wrap: wrap;
            }
            
            .header-right {
                flex: 1;
                justify-content: flex-end;
            }
            /* Make panels stack and take full width on small screens */
            .col-md-6.section-panel {
                flex: 0 0 100%;
                max-width: 100%;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .section-panel { display: none; width: 100%; box-sizing: border-box; }
            .section-panel.active-section { display: block; margin-bottom: 1rem; animation: fadeIn 220ms ease; }

            /* Responsive thumbnails */
            .complaint-thumb { width: 80px; height: 60px; object-fit: cover; border-radius: 6px; }
            @media (max-width: 576px) {
                .complaint-thumb { width: 56px; height: 42px; }
            }
        }

        /* Panel show/hide behavior for sidebar navigation */
        .section-panel { display: none; }
        .section-panel.active-section { display: block; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }


        @media (min-width: 769px) {
            .sidebar {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 280px;
            }
            
            .menu-toggle {
                display: none;
            }
        }

        /* Dark theme adjustments for sidebar */
        [data-theme="dark"] .sidebar {
            background: var(--bg-primary);
            border-right-color: var(--border-color);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
        }

        [data-theme="dark"] .sidebar-header {
            background: var(--bg-primary);
            border-bottom-color: var(--border-color);
        }

        [data-theme="dark"] .sidebar-brand {
            color: var(--text-primary);
        }

        [data-theme="dark"] .sidebar-brand i {
            color: var(--text-primary);
        }

        [data-theme="dark"] .sidebar-nav {
            background: var(--bg-primary);
        }

        [data-theme="dark"] .nav-link {
            color: var(--text-primary);
            background: var(--bg-primary);
        }

        [data-theme="dark"] .nav-link:hover {
            color: var(--text-primary);
            background: var(--bg-tertiary);
            border-left-color: var(--primary-color);
        }

        [data-theme="dark"] .nav-link.active {
            color: var(--text-primary);
            background: var(--bg-tertiary);
            border-left-color: var(--primary-color);
        }

        [data-theme="dark"] .nav-link i {
            color: var(--text-secondary);
        }

        [data-theme="dark"] .nav-link.active i,
        [data-theme="dark"] .nav-link:hover i {
            color: var(--text-primary);
        }

        [data-theme="dark"] .top-header {
            background: var(--navbar-bg);
        }

        /* Logout Button */
        .btn-logout {
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: none;
        }

        .btn-logout:hover {
            background: var(--danger-color);
            border-color: transparent;
            color: var(--bg-primary);
            transform: translateY(-1px);
        }

        /* Student Info in Sidebar */
        .student-info-sidebar {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e2e8f0;
            margin-top: auto;
            background: #ffffff;
        }

        [data-theme="dark"] .student-info-sidebar {
            border-top-color: var(--border-color);
            background: var(--bg-primary);
        }

        .student-name {
            color: #000000;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .student-id {
            color: #4a5568;
            font-size: 0.875rem;
        }

        [data-theme="dark"] .student-name {
            color: var(--text-primary);
        }

        [data-theme="dark"] .student-id {
            color: var(--text-secondary);
        }

        /* Scrollbar styling for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        [data-theme="dark"] .sidebar::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
        }

        [data-theme="dark"] .sidebar::-webkit-scrollbar-thumb {
            background: var(--border-color);
        }

        [data-theme="dark"] .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }
    </style>
</head>
<body>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar Navigation -->
<nav class="sidebar" id="sidebar" role="navigation" aria-label="Student sidebar" aria-hidden="true">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            <i class="fas fa-building"></i>
            LincHostel
        </a>
        <button class="close-sidebar d-md-none" id="closeSidebarBtn" aria-label="Close navigation">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </div>

    <div class="sidebar-nav">
        <div class="nav-item">
            <a class="nav-link active" href="#dashboard-top">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link" href="#student-info">
                <i class="fas fa-id-card"></i>
                Profile
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link" href="#roommates">
                <i class="fas fa-users"></i>
                Roommates
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link" href="#make-payment">
                <i class="fas fa-credit-card"></i>
                Make Payment
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link" href="#make-complaint">
                <i class="fas fa-exclamation-triangle"></i>
                Make Complaint
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link" href="#payment-history">
                <i class="fas fa-history"></i>
                Payment History
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link" href="#complaint-history">
                <i class="fas fa-list"></i>
                Complaint History
            </a>
        </div>
        <div class="nav-item mt-3">
            <a class="nav-link" href="#settings">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </div>
    </div>

    <!-- Student Info in Sidebar -->
    <div class="student-info-sidebar">
        <div class="student-name">{{ $student->full_name }}</div>
        <div class="student-id">{{ $student->admission_number }}</div>
    </div>
</nav>

<!-- Top Header Bar -->
<header class="top-header">
    <div class="container">
        <div class="header-content">
            <div class="header-left">
                <button class="menu-toggle" id="menuToggle" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                    <span class="visually-hidden">Toggle navigation</span>
                </button>
                <h1 class="h4 mb-0 d-none d-md-block">Student Dashboard</h1>
            </div>

            <div class="header-right">
                <!-- Announcement Notification Dropdown -->
                <div class="notification-dropdown dropdown">
                    <a class="dropdown-toggle position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-lg"></i>
                        @if($unreadAnnouncements > 0)
                            <span class="notification-badge badge">{{ $unreadAnnouncements }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="notificationsDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="dropdown-header py-3 px-3 fw-bold">
                            <i class="fas fa-bell me-2"></i>Notifications
                            @if($unreadAnnouncements > 0)
                                <span class="badge bg-light text-dark ms-2">{{ $unreadAnnouncements }} new</span>
                            @endif
                        </li>
                        @forelse($latestAnnouncements as $announcement)
                            <li>
                                <a 
                                    href="#" 
                                    class="dropdown-item py-3 px-3 text-wrap text-break"
                                /* Main content padding for small screens */
                                .main-content {
                                    padding: 1rem 0.75rem;
                                    data-bs-toggle="modal" 
                                    data-bs-target="#announcementModal"
                                    data-title="{{ $announcement->title }}"
                                    data-description="{{ $announcement->description }}"
                                    data-has-attachment="{{ $announcement->hasAttachment() ? '1' : '0' }}"
                                    data-attachment-name="{{ $announcement->attachment_original_name ?? '' }}"
                                    data-attachment-type="{{ $announcement->attachment_type ?? '' }}"
                                    data-attachment-url="{{ $announcement->hasAttachment() ? route('announcements.download', $announcement) : '' }}"
                                >
                                    <div class="d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <strong class="mb-0 text-dark">{{ Str::limit($announcement->title, 40) }}</strong>
                                            <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="text-muted mb-2 small">{{ Str::limit($announcement->description, 80) }}</p>
                                        @if($announcement->hasAttachment())
                                            <div class="d-flex align-items-center">
                                                <i class="{{ $announcement->getAttachmentTypeIcon() }} text-primary me-2"></i>
                                                <small class="text-primary">{{ Str::limit($announcement->attachment_original_name, 25) }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-0"></li>
                        @empty
                            <li class="dropdown-item text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <div>No announcements</div>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- Enhanced Dark Mode Toggle -->
                <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle dark mode">
                    <i class="fas fa-sun" id="themeIcon"></i>
                    <span id="themeText">Light</span>
                </button>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="main-content" id="mainContent">
    <div class="container py-5">
            @if(session('complaint_success'))
                <div class="alert alert-success auto-dismiss" role="status" aria-live="polite">
                    <i class="fas fa-check-circle me-2" aria-hidden="true"></i> {{ session('complaint_success') }}
                </div>
            @endif
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Welcome Card -->
                <div class="card mb-4 section-panel" id="dashboard-top" aria-hidden="true">
                    <div class="card-header">
                        <i class="fas fa-user-graduate me-2"></i>Student Dashboard
                    </div>
                    <div class="card-body text-center">
                        @php
                            date_default_timezone_set('Africa/Lagos'); // Set to Nigerian time

                            $hour = date('H');
                            if ($hour >= 0 && $hour < 12) {
                                $greeting = 'Good morning';
                            } elseif ($hour >= 12 && $hour < 17) {
                                $greeting = 'Good afternoon';
                            } else {
                                $greeting = 'Good evening';
                            }
                        @endphp
                        <h5>{{ $greeting }}, {{ $student->full_name }}! 👋 </h5>
                        <p class="text-muted">Here's your personalized dashboard with all your details and available options.</p>
                        <!-- Student Overview Cards -->
                        <div class="row mt-4">
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body text-center py-2">
                                        <div class="h6 mb-1 text-muted">Payments</div>
                                        <div class="h5 fw-bold">{{ $total_payments }}</div>
                                        <small class="text-success">₦{{ number_format($total_paid, 2) }} paid</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body text-center py-2">
                                        <div class="h6 mb-1 text-muted">Pending Payments</div>
                                        <div class="h5 fw-bold">{{ $pending_payments }}</div>
                                        <small class="text-muted">Awaiting verification</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body text-center py-2">
                                        <div class="h6 mb-1 text-muted">Complaints</div>
                                        <div class="h5 fw-bold">{{ $total_complaints }}</div>
                                        <small class="text-warning">{{ $pending_complaints }} pending</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body text-center py-2">
                                        <div class="h6 mb-1 text-muted">Announcements</div>
                                        <div class="h5 fw-bold">{{ $unreadAnnouncements }}</div>
                                        <small class="text-muted">Latest updates</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rest of your dashboard content remains exactly the same -->
                <!-- Student Info -->
                <div class="card mb-4 section-panel" id="student-info" aria-hidden="true">
                    <div class="card-header">
                        <i class="fas fa-id-card me-2"></i>Student Information
                    </div>
                    <div class="card-body row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-hashtag me-2"></i>Admission Number:</strong> {{ $student->admission_number }}</p>
                            <p><strong><i class="fas fa-user me-2"></i>Full Name:</strong> {{ $student->full_name }}</p>
                            <p><strong><i class="fas fa-venus-mars me-2"></i>Gender:</strong> {{ $student->gender }}</p>
                            <p><strong><i class="fas fa-map-marker-alt me-2"></i>Address:</strong> {{ $student->address }}</p>
                            <p><strong><i class="fas fa-graduation-cap me-2"></i>Department:</strong> {{ $student->department }}</p>
                            <p><strong><i class="fas fa-calendar-alt me-2"></i>Semester:</strong> Semester {{ $student->semester }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-door-open me-2"></i>Room Number:</strong> {{ $student->room->room_number ?? 'Not assigned' }}</p>
                            <p><strong><i class="fas fa-calendar-check me-2"></i>Check-in Date:</strong> {{ $student->check_in_date->format('M d, Y') }}</p>
                            <p><strong><i class="fas fa-calendar-times me-2"></i>Expected Check-out:</strong> {{ $student->expected_check_out_date->format('M d, Y') }}</p>
                            <p><strong><i class="fas fa-info-circle me-2"></i>Status:</strong>
                                <span class="badge {{ $student->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                    <i class="fas {{ $student->status == 'active' ? 'fa-check' : 'fa-times' }} me-1"></i>
                                    {{ ucfirst($student->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Students in This Room -->
                <div class="card mb-4 section-panel" id="roommates" aria-hidden="true">
                    <div class="card-header">
                        <h5><i class="fas fa-users me-2"></i>Students in your Room ({{ $student->room->students->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Check-in Date</th>
                                    <th>Expected Check-out Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($student->room->students as $student)
                                    <tr>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->check_in_date->format('M d, Y') }}</td>
                                        <td>{{ $student->expected_check_out_date->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No students in your room</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Actions -->
                    <div class="row g-4" id="actions">
                    <!-- Payment -->
                    <div class="col-md-6 section-panel" id="make-payment" aria-hidden="true">
                        <div class="card h-100">
                            <div class="card-header">
                                <i class="fas fa-credit-card me-2"></i>Make Payment
                            </div>
                            <div class="card-body">

                                @if(session('payment_success'))
                                    <div class="alert alert-success auto-dismiss">
                                        <i class="fas fa-check-circle me-2"></i>{{ session('payment_success') }}
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger auto-dismiss">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('student.payments.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-money-bill-wave me-1"></i>Amount
                                        </label>
                                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-payment me-1"></i>Payment Method
                                        </label>
                                        <select name="payment_method" class="form-select" required>
                                            <option value="credit_card">💳 Credit Card</option>
                                            <option value="bank_transfer">🏦 Bank Transfer</option>
                                            <option value="other">📱 Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-receipt me-1"></i>Payment Receipt
                                        </label>
                                        <input type="file" name="receipt" class="form-control" required>
                                        <small class="text-muted">📎 JPG, PNG, PDF (Max: 2MB)</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-sticky-note me-1"></i>Notes (Optional)
                                        </label>
                                        <textarea name="notes" class="form-control" rows="2" placeholder="Add any additional notes..."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-1"></i>Submit Payment
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#paymentDetailsModal">
                                            <i class="fas fa-info-circle me-1"></i>Account Details
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Complaint -->
                    <div class="col-md-6 section-panel" id="make-complaint" aria-hidden="true">
                        <div class="card h-100">
                            <div class="card-header">
                                <i class="fas fa-exclamation-triangle me-2"></i>Make Complaint
                            </div>
                            <div class="card-body">
                                @if(session('complaint_success'))
                                    <div class="alert alert-success auto-dismiss">
                                        <i class="fas fa-check-circle me-2"></i>{{ session('complaint_success') }}
                                    </div>
                                @endif
                                <form action="{{ route('student.complaints.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-heading me-1"></i>Subject
                                        </label>
                                        <input type="text" name="subject" class="form-control" placeholder="Brief description of your issue..." required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-align-left me-1"></i>Description
                                        </label>
                                        <textarea name="description" class="form-control" rows="4" placeholder="Please provide detailed information about your complaint..." required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-paperclip me-1"></i>Attachment (photo)
                                        </label>
                                        <input type="file" name="attachment" class="form-control" accept="image/jpeg,image/png">
                                        <small class="text-muted">Optional. JPG/PNG up to 5MB. Upload a photo of the issue for faster resolution.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i>Submit Complaint
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card mt-5 section-panel" id="payment-history" aria-hidden="true">
                    <div class="card-header">
                        <i class="fas fa-history me-2"></i>Payment History
                    </div>
                    <div class="card-body">
                        @if($student->payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-receipt me-1"></i>Receipt #</th>
                                            <th><i class="fas fa-money-bill-wave me-1"></i>Amount</th>
                                            <th><i class="fas fa-calendar me-1"></i>Date</th>
                                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $payment)
                                            <tr>
                                                <td>{{ $payment->receipt_number }}</td>
                                                <td>₦{{ number_format($payment->amount, 2) }}</td>
                                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="badge 
                                                        @if($payment->status == 'completed') bg-success
                                                        @elseif($payment->status == 'pending') bg-warning text-dark
                                                        @else bg-danger @endif">
                                                        <i class="fas 
                                                            @if($payment->status == 'completed') fa-check
                                                            @elseif($payment->status == 'pending') fa-clock
                                                            @else fa-times @endif me-1"></i>
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No payment history found.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Complaint History -->
                <div class="card mt-4 section-panel" id="complaint-history" aria-hidden="true">
                    <div class="card-header">
                        <i class="fas fa-list me-2"></i>Complaint History
                    </div>
                    <div class="card-body">
                        @if($student->complaints->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-heading me-1"></i>Subject</th>
                                            <th><i class="fas fa-paperclip me-1"></i>Image</th>
                                            <th><i class="fas fa-calendar me-1"></i>Date</th>
                                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($complaints as $complaint)
                                            <tr>
                                                <td>{{ Str::limit($complaint->subject, 30) }}</td>
                                                <td>
                                                    @if($complaint->attachment_path)
                                                        <a href="{{ asset('storage/' . $complaint->attachment_path) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $complaint->attachment_path) }}" alt="attachment" class="complaint-thumb">
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="badge 
                                                        @if($complaint->status == 'resolved') bg-success
                                                        @elseif($complaint->status == 'submitted') bg-secondary
                                                        @else bg-warning text-dark @endif">
                                                        <i class="fas 
                                                            @if($complaint->status == 'resolved') fa-check
                                                            @elseif($complaint->status == 'submitted') fa-paper-plane
                                                            @else fa-clock @endif me-1"></i>
                                                        {{ ucfirst($complaint->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No complaints found.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentDetailsModalLabel">
                        <i class="fas fa-university me-2"></i>Bank Account Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Please make your payment to the account details below and upload your payment receipt through the form.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-university me-1"></i>Bank Name</th>
                                    <th><i class="fas fa-hashtag me-1"></i>Account Number</th>
                                    <th><i class="fas fa-user me-1"></i>Account Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>🏦 EcoBank Nigeria PLC</td>
                                    <td><code>3680086084</code></td>
                                    <td>Lincoln Logistics Service Limited</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> After making your payment, kindly upload your payment receipt using the "Make Payment" form on your dashboard for verification.
                    </div>
                </div>
            </div>
        </div>

                <!-- Settings -->
                <div class="card mt-4 section-panel" id="settings" aria-hidden="true">
                    <div class="card-header">
                        <i class="fas fa-cog me-2"></i>Settings
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="{{ url('/student/profile') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-user me-2"></i>Update Profile
                            </a>
                            <a href="{{ url('/student/profile/change-password') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-key me-2"></i>Change Password
                            </a>
                            <a href="{{ url('/student/notifications') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-bell me-2"></i>Notification Preferences
                            </a>
                        </div>
                    </div>
                </div>
    </div>

    <!-- Announcement Details Modal -->
    <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="announcementModalLabel">
                <i class="fas fa-bullhorn me-2"></i>Announcement
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="announcementModalBody">
              Announcement description will appear here.
            </div>
            <div id="attachmentContainer" class="mt-3 pt-3 border-top" style="display: none;">
              <h6><i class="fas fa-paperclip me-2"></i>Attachment</h6>
              <div class="d-flex align-items-center">
                <i id="attachmentIcon" class="fas fa-file me-2 fs-4"></i>
                <div>
                  <div id="attachmentName" class="fw-bold"></div>
                  <a id="attachmentDownloadLink" href="#" class="btn btn-sm btn-primary mt-2">
                    <i class="fas fa-download me-1"></i>Download
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</main>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle = document.getElementById('menuToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    let lastFocusedElement = null;

    // Initialize aria-hidden on the sidebar based on initial viewport width
    if (sidebar) {
        sidebar.setAttribute('aria-hidden', window.innerWidth >= 769 ? 'false' : 'true');
    }

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('sidebar-open');
        sidebarOverlay.classList.toggle('active');
        // Update aria-expanded on the toggle button for assistive tech
        if (menuToggle) {
            const expanded = sidebar.classList.contains('active');
            menuToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        }
        // Keep aria-hidden in sync for the sidebar for accessibility
        if (sidebar) {
            const isVisible = sidebar.classList.contains('active') || window.innerWidth >= 769;
            sidebar.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
        }
        // Lock body scroll on mobile when sidebar is open
        if (window.innerWidth < 769) {
            if (sidebar.classList.contains('active')) {
                document.body.classList.add('no-scroll');
                // Move focus into the sidebar for keyboard users
                lastFocusedElement = document.activeElement;
                const firstLink = sidebar.querySelector('.nav-link');
                if (firstLink) firstLink.focus();
            } else {
                document.body.classList.remove('no-scroll');
                // Return focus to the toggle button if available
                if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                    lastFocusedElement.focus();
                } else if (menuToggle) {
                    menuToggle.focus();
                }
            }
        }
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', toggleSidebar);
    }

    if (closeSidebarBtn) {
        closeSidebarBtn.addEventListener('click', toggleSidebar);
    }

    // Close the sidebar with Escape key and ensure accessible keyboard behavior
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            if (sidebar && sidebar.classList.contains('active') && window.innerWidth < 769) {
                toggleSidebar();
            }
        }
    });

    // Close sidebar when clicking on a nav link (mobile), but let anchor links handle their own behavior
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            // If this is an anchor link for panel navigation, do not toggle here (handled separately)
            const href = this.getAttribute('href') || '';
            if (window.innerWidth < 769 && !href.startsWith('#')) {
                toggleSidebar();
            }
        });
    });

    // Sidebar-driven panels: show/hide panels instead of scrolling
    const sectionLinks = document.querySelectorAll('.sidebar .nav-link[href^="#"]');
    const panels = Array.from(sectionLinks).map(l => document.querySelector(l.getAttribute('href'))).filter(Boolean);

    function showPanel(id) {
        panels.forEach(p => {
            if (p && p.id === id) {
                p.classList.add('active-section');
                p.setAttribute('aria-hidden', 'false');
            } else if (p) {
                p.classList.remove('active-section');
                p.setAttribute('aria-hidden', 'true');
            }
        });

        sectionLinks.forEach(l => {
            if (l.getAttribute('href') === '#' + id) {
                l.classList.add('active');
                l.setAttribute('aria-current', 'true');
                l.focus({ preventScroll: true });
            } else {
                l.classList.remove('active');
                l.removeAttribute('aria-current');
            }
        });

        // Reset mainContent scroll so content starts at top of panel
        if (mainContent) mainContent.scrollTop = 0;
    }

    // Attach click handlers to open panels
    sectionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            const id = href.replace('#', '');
            showPanel(id);
            // Close sidebar on mobile after selection
            if (window.innerWidth < 769) toggleSidebar();
        });
    });

    // Show dashboard by default
    if (document.getElementById('dashboard-top')) {
        showPanel('dashboard-top');
    }

    // If the server asked us to open a specific panel (e.g., after complaint submit), do so
    @if(session('complaint_panel'))
        showPanel('{{ session('complaint_panel') }}');
    @endif

    // Ensure responsive consistency when resizing between mobile and desktop
    function handleResize() {
        if (window.innerWidth >= 769) {
            // On desktop: clear any mobile overlay/active classes that might linger
            if (sidebar) sidebar.classList.remove('active');
            if (sidebarOverlay) sidebarOverlay.classList.remove('active');
            if (mainContent) mainContent.classList.remove('sidebar-open');
            if (sidebar) sidebar.setAttribute('aria-hidden', 'false');
        }
        else {
            if (sidebar) sidebar.setAttribute('aria-hidden', 'true');
        }
    }

    window.addEventListener('resize', handleResize);
    // Run once to normalize initial state
    handleResize();

    // Enhanced Theme Management - No more flash!
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const themeText = document.getElementById('themeText');
    const body = document.body;

    // Get the theme that was already applied by the early script
    const currentTheme = window.__INITIAL_THEME__ || localStorage.getItem('theme') || 'light';
    
    // Update the UI to match the current theme (no flash since theme is already applied)
    updateThemeUI(currentTheme);

    // Theme toggle event listener
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            // Add transition class
            body.classList.add('theme-switching');
            
            setTimeout(() => {
                setTheme(newTheme);
                body.classList.remove('theme-switching');
                body.classList.add('theme-transition');
                
                setTimeout(() => {
                    body.classList.remove('theme-transition');
                }, 300);
            }, 50);
        });

        // Keyboard navigation for theme toggle
        themeToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                themeToggle.click();
            }
        });
    }

    function setTheme(theme) {
        // Apply to both html and body for maximum compatibility
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.className = theme + '-theme';
        body.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        updateThemeUI(theme);
    }

    function updateThemeUI(theme) {
        if (themeIcon && themeText && themeToggle) {
            if (theme === 'dark') {
                themeIcon.className = 'fas fa-moon';
                themeText.textContent = 'Dark';
                themeToggle.setAttribute('aria-label', 'Switch to light mode');
            } else {
                themeIcon.className = 'fas fa-sun';
                themeText.textContent = 'Light';
                themeToggle.setAttribute('aria-label', 'Switch to dark mode');
            }
        }
    }

    // Announcement Modal Management
    const announcementModal = document.getElementById('announcementModal');
    
    if (announcementModal) {
        announcementModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const title = button.getAttribute('data-title');
            const description = button.getAttribute('data-description');
            const hasAttachment = button.getAttribute('data-has-attachment') === '1';
            const attachmentName = button.getAttribute('data-attachment-name');
            const attachmentType = button.getAttribute('data-attachment-type');
            const attachmentUrl = button.getAttribute('data-attachment-url');

            const modalTitle = announcementModal.querySelector('.modal-title');
            const modalBody = announcementModal.querySelector('#announcementModalBody');
            const attachmentContainer = document.getElementById('attachmentContainer');
            const attachmentIcon = document.getElementById('attachmentIcon');
            const attachmentNameElement = document.getElementById('attachmentName');
            const attachmentDownloadLink = document.getElementById('attachmentDownloadLink');

            modalTitle.innerHTML = '<i class="fas fa-bullhorn me-2"></i>' + title;
            modalBody.textContent = description;

            // Handle attachment display
            if (hasAttachment && attachmentName) {
                attachmentContainer.style.display = 'block';
                attachmentNameElement.textContent = attachmentName;
                attachmentDownloadLink.href = attachmentUrl;
                
                // Set appropriate icon based on file type
                if (attachmentType) {
                    attachmentIcon.className = getFileIcon(attachmentType);
                } else {
                    attachmentIcon.className = 'fas fa-file me-2 fs-4';
                }
            } else {
                attachmentContainer.style.display = 'none';
            }
        });
    }

    // Function to determine file icon based on MIME type
    function getFileIcon(mimeType) {
        if (mimeType.startsWith('image/')) {
            return 'fas fa-file-image me-2 fs-4 text-info';
        } else if (mimeType === 'application/pdf') {
            return 'fas fa-file-pdf me-2 fs-4 text-danger';
        } else if (mimeType === 'application/msword' || mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            return 'fas fa-file-word me-2 fs-4 text-primary';
        } else if (mimeType === 'application/vnd.ms-excel' || mimeType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            return 'fas fa-file-excel me-2 fs-4 text-success';
        } else {
            return 'fas fa-file me-2 fs-4 text-secondary';
        }
    }

    // Enhanced form validation feedback
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
                submitBtn.disabled = true;
            }
        });
    });

    // Auto-hide alerts after 5 seconds - ONLY for alerts with auto-dismiss class
    const autoDismissAlerts = document.querySelectorAll('.alert.auto-dismiss');
    autoDismissAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Add smooth scroll behavior for better UX
    document.documentElement.style.scrollBehavior = 'smooth';

    // Add focus management for modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const firstInput = modal.querySelector('input, button, textarea, select');
            if (firstInput) {
                firstInput.focus();
            }
        });
    });
});

// System theme detection (only if no saved preference)
if (window.matchMedia && !localStorage.getItem('theme')) {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    
    // Listen for system theme changes
    mediaQuery.addEventListener('change', function(e) {
        // Only apply if no user preference is saved
        if (!localStorage.getItem('theme')) {
            const systemTheme = e.matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', systemTheme);
            document.documentElement.className = systemTheme + '-theme';
            document.body.setAttribute('data-theme', systemTheme);
        }
    });
}
</script>

<script>
    // Check session status every 60 seconds
    setInterval(() => {
        fetch("{{ url('/check-session') }}")
        .then(response => {
            if (response.status === 401) {
                window.location.href = "{{ route('student.login') }}";
            }
        });
    }, 60000); // every 60 seconds
</script>

</body>
</html>