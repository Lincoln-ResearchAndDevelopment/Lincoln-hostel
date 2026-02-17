<!DOCTYPE html>
<html lang="en">
<head>
    <script>
      (function() {
        const savedTheme = localStorage.getItem('theme');
        let theme = 'light';
        if (savedTheme) {
          theme = savedTheme;
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
          theme = 'dark';
        }
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.className = theme + '-theme';
        window.__INITIAL_THEME__ = theme;
      })();
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LincHostel | Admin Dashboard</title>

    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            --header-height: 60px;
            --transition-speed: 0.3s;
            --primary-color: #cc0000;
            --primary-hover: #a30000;
            --primary-light: #fff5f5;
            --primary-rgb: 204, 0, 0;
            --secondary-color: #ffffff;
            --navbar-bg: #ffffff;
            --sidebar-bg: #ffffff;
            --sidebar-active-bg: #fff5f5;
            --bg-primary: #f8f9fa;
            --bg-secondary: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
        }

        [data-theme="dark"] {
            --primary-color: #dc3545;
            --primary-hover: #c82333;
            --primary-light: #2c0b0e;
            --primary-rgb: 220, 53, 69;
            --secondary-color: #121212;
            --navbar-bg: #1a1a1a;
            --sidebar-bg: #1a1a1a;
            --sidebar-active-bg: #2c0b0e;
            --bg-primary: #121212;
            --bg-secondary: #1a1a1a;
            --text-primary: #e0e0e0;
            --text-secondary: #b0b0b0;
            --border-color: #2d2d2d;
        }

        * { box-sizing: border-box; }
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            margin: 0;
        }
        .app-container { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            transition: all var(--transition-speed) ease;
            z-index: 1000; overflow-y: auto; overflow-x: hidden;
            display: flex; flex-direction: column;
        }
        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        .sidebar-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex; align-items: center;
            min-height: var(--header-height);
        }
        .sidebar-brand {
            font-size: 1.35rem; font-weight: 700;
            color: var(--primary-color); text-decoration: none;
            display: flex; align-items: center; gap: 0.75rem;
        }
        .sidebar-brand img {
            height: 45px;
            width: auto;
            max-width: 45px;
            object-fit: contain;
            object-position: center;
            transition: all var(--transition-speed);
        }
        .sidebar-brand:hover { color: var(--primary-hover); }
        .sidebar.collapsed .sidebar-brand span { display: none; }
        .sidebar.collapsed .sidebar-brand img { 
            height: 35px; 
            max-width: 35px;
        }
        .sidebar-nav { padding: 0.5rem 0; flex: 1; overflow-y: auto; }
        .nav-section-title {
            padding: 0.75rem 1.25rem 0.5rem;
            font-size: 0.7rem; text-transform: uppercase;
            letter-spacing: 0.5px; color: var(--text-secondary); font-weight: 600;
        }
        .sidebar.collapsed .nav-section-title { display: none; }
        .nav-link {
            padding: 0.6rem 1.25rem; color: var(--text-primary);
            text-decoration: none; display: flex; align-items: center;
            gap: 0.75rem; transition: all 0.2s;
            border-left: 3px solid transparent; font-size: 0.875rem;
        }
        .nav-link:hover { background-color: var(--sidebar-active-bg); color: var(--primary-color); }
        .nav-link.active {
            background-color: var(--sidebar-active-bg);
            color: var(--primary-color);
            border-left-color: var(--primary-color); font-weight: 600;
        }
        .nav-link i { width: 20px; text-align: center; font-size: 0.95rem; }
        .nav-link .badge { font-size: 0.65rem; padding: 0.25rem 0.5rem; }
        .sidebar.collapsed .nav-link { justify-content: center; padding: 0.75rem; }
        .sidebar.collapsed .nav-link span, .sidebar.collapsed .nav-link .badge { display: none; }
        .sidebar.collapsed .nav-link i { margin: 0; font-size: 1.1rem; }
        
        /* Sidebar Footer */
        .sidebar-footer {
            padding: 1rem; border-top: 1px solid var(--border-color);
            background: var(--bg-primary);
        }
        .user-info { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
        .user-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: var(--primary-color); color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 1rem;
        }
        .user-details { flex: 1; min-width: 0; }
        .user-name {
            font-weight: 600; font-size: 0.9rem; color: var(--text-primary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-role { font-size: 0.75rem; color: var(--text-secondary); }
        .sidebar.collapsed .user-details { display: none; }
        .sidebar.collapsed .user-avatar { width: 36px; height: 36px; }
        .sidebar-toggle {
            position: absolute; bottom: 5rem; right: -12px;
            width: 24px; height: 24px; background: var(--primary-color);
            border: none; border-radius: 50%; color: white; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 1001;
        }
        .sidebar-toggle:hover { background: var(--primary-hover); }

        /* Main Content */
        .main-content {
            flex: 1; margin-left: var(--sidebar-width);
            min-height: 100vh; background-color: var(--bg-primary);
            transition: margin-left var(--transition-speed);
        }
        .main-content.expanded { margin-left: var(--sidebar-collapsed-width); }
        
        /* Header */
        .top-header {
            height: var(--header-height); background: var(--navbar-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem; position: sticky; top: 0; z-index: 999;
        }
        .page-title { font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--text-primary); }
        .header-actions { display: flex; align-items: center; gap: 0.5rem; }
        .mobile-toggle {
            display: none; background: none; border: none;
            color: var(--text-primary); font-size: 1.25rem; cursor: pointer; padding: 0.5rem;
        }
        
        /* Header Buttons */
        .header-btn {
            background: none; border: 1px solid var(--border-color);
            color: var(--text-primary); padding: 0.4rem 0.75rem;
            border-radius: 6px; cursor: pointer; display: flex;
            align-items: center; gap: 0.5rem; font-size: 0.85rem; transition: all 0.2s;
            position: relative;
        }
        .header-btn:hover { background-color: var(--bg-secondary); border-color: var(--primary-color); }
        .header-btn .notification-badge {
            position: absolute; top: -5px; right: -5px;
            background: var(--primary-color); color: white;
            border-radius: 50%; width: 18px; height: 18px;
            font-size: 0.65rem; display: flex; align-items: center; justify-content: center;
        }
        
        .content-container { padding: 1.5rem; }
        
        /* Cards */
        .card {
            background-color: var(--navbar-bg); border: 1px solid var(--border-color);
            border-radius: 0.5rem; color: var(--text-primary);
        }
        .card-header { background-color: var(--navbar-bg); border-bottom: 1px solid var(--border-color); }
        
        /* Tables */
        .table { color: var(--text-primary); }
        .table thead th { border-bottom-color: var(--border-color); background: var(--bg-primary); }
        .table td, .table th { border-color: var(--border-color); vertical-align: middle; }
        .table-hover tbody tr:hover { background-color: var(--sidebar-active-bg); }
        
        /* Buttons */
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--primary-hover); border-color: var(--primary-hover); }
        .btn-outline-primary { color: var(--primary-color); border-color: var(--primary-color); }
        .btn-outline-primary:hover { background-color: var(--primary-color); border-color: var(--primary-color); color: white; }
        
        /* Text & Background */
        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-primary-subtle { background-color: var(--primary-light) !important; }
        
        /* Forms */
        .form-control, .form-select {
            background-color: var(--navbar-bg); color: var(--text-primary);
            border-color: var(--border-color);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
            background-color: var(--navbar-bg); color: var(--text-primary);
        }
        
        /* Alerts */
        .alert { border: 1px solid var(--border-color); }
        
        /* Breadcrumb */
        .breadcrumb-item a { color: var(--primary-color); text-decoration: none; }
        .breadcrumb-item a:hover { color: var(--primary-hover); text-decoration: underline; }
        
        /* Logout Button */
        .btn-logout {
            width: 100%; background: transparent;
            border: 1px solid var(--primary-color); color: var(--primary-color);
            padding: 0.5rem; border-radius: 6px; font-size: 0.85rem; transition: all 0.2s;
            cursor: pointer;
        }
        .btn-logout:hover { background: var(--primary-color); color: white; }
        .sidebar.collapsed .btn-logout span { display: none; }
        
        /* Stat Cards */
        .stat-card {
            border-radius: 12px; overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        
        /* Dropdown */
        .dropdown-menu {
            background-color: var(--navbar-bg); border-color: var(--border-color);
        }
        .dropdown-item { color: var(--text-primary); }
        .dropdown-item:hover { background-color: var(--sidebar-active-bg); color: var(--primary-color); }
        
        /* Modal */
        .modal-content {
            background-color: var(--navbar-bg); color: var(--text-primary);
            border-color: var(--border-color);
        }
        .modal-header, .modal-footer { border-color: var(--border-color); }
        
        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .main-content.expanded { margin-left: 0; }
            .mobile-toggle { display: block; }
            .sidebar-toggle { display: none; }
            .sidebar-overlay {
                display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5); z-index: 999;
            }
            .sidebar-overlay.active { display: block; }
            .page-title { font-size: 1rem; }
        }
        
        @media (max-width: 767.98px) {
            .content-container { padding: 1rem; }
            .top-header { padding: 0 1rem; }
            .header-btn span { display: none; }
            .header-btn { padding: 0.5rem; width: 40px; height: 40px; justify-content: center; }
            .header-actions { gap: 0.25rem; }
        }

        /* Responsive Table Enhancement */
        .table-responsive {
            border: 0;
            margin-bottom: 0;
        }
        
        @media (max-width: 576px) {
            .card-header { padding: 0.75rem 1rem; }
            .card-body { padding: 1rem; }
            .btn-sm { padding: 0.4rem 0.6rem; }
            .breadcrumb { display: none; } /* Save space on tiny screens */
            
            /* Full width buttons on mobile */
            .btn-mobile-full { width: 100%; margin-bottom: 0.5rem; }
            .d-flex.gap-2 { flex-direction: column; }
            .d-flex.gap-2 .btn { width: 100%; }
        }

        /* Prevent auto-zoom on iOS */
        @media screen and (max-width: 768px) {
            input, select, textarea {
                font-size: 16px !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-container">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <img src="{{ asset('assets/img/lincoln-logo.png') }}" alt="Lincoln University Logo">
                    <span>LincHostel</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <!-- Main -->
                <div class="nav-section-title">Main</div>
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                
                <!-- Hostel Management -->
                <div class="nav-section-title">Hostel Management</div>
                <a class="nav-link {{ request()->routeIs('hostels.index') && !request('type') ? 'active' : '' }}" href="{{ route('hostels.index') }}">
                    <i class="fas fa-building"></i><span>All Hostels</span>
                </a>
                <a class="nav-link {{ request('type') == 'male' ? 'active' : '' }}" href="{{ route('hostels.index', ['type' => 'male']) }}" style="padding-left: 2rem;">
                    <i class="fas fa-mars text-primary"></i><span>Boys Hostels</span>
                </a>
                <a class="nav-link {{ request('type') == 'female' ? 'active' : '' }}" href="{{ route('hostels.index', ['type' => 'female']) }}" style="padding-left: 2rem;">
                    <i class="fas fa-venus text-danger"></i><span>Girls Hostels</span>
                </a>
                <a class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">
                    <i class="fas fa-door-open"></i><span>Rooms & Beds</span>
                </a>
                <a class="nav-link {{ request('type') == 'booking' ? 'active' : '' }}" href="{{ route('payments.index', ['type' => 'booking', 'status' => 'pending']) }}">
                    <i class="fas fa-home"></i><span>Room Bookings</span>
                    @php $pendingBookingsCount = \App\Models\Payment::where('status', 'pending')->whereNotNull('room_id')->count(); @endphp
                    @if($pendingBookingsCount > 0)
                        <span class="badge bg-warning text-dark">{{ $pendingBookingsCount }}</span>
                    @endif
                </a>
                
                <!-- Student Management -->
                <div class="nav-section-title">Students</div>
                <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                    <i class="fas fa-user-graduate"></i><span>All Students</span>
                </a>
                <a class="nav-link {{ request()->routeIs('applications.*') ? 'active' : '' }}" href="{{ route('applications.index') }}">
                    <i class="fas fa-file-alt"></i><span>Applications</span>
                    @php $pendingApps = \App\Models\HostelApplication::where('status', 'pending')->count(); @endphp
                    @if($pendingApps > 0)
                        <span class="badge bg-warning text-dark">{{ $pendingApps }}</span>
                    @endif
                </a>
                <a class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                    <i class="fas fa-clipboard-check"></i><span>Attendance</span>
                </a>
                
                <!-- Finance -->
                <div class="nav-section-title">Finance</div>
                <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">
                    <i class="fas fa-credit-card"></i><span>Payments</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}" href="{{ route('admin.fees.index') }}">
                    <i class="fas fa-money-bill-wave"></i><span>Fee Structure</span>
                </a>
                
                <!-- Requests & Issues -->
                <div class="nav-section-title">Requests & Issues</div>
                <a class="nav-link {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}" href="{{ route('admin.leave.index') }}">
                    <i class="fas fa-calendar-alt"></i><span>Leave Requests</span>
                    @php $pendingLeave = \App\Models\LeaveRequest::where('status', 'pending')->count(); @endphp
                    @if($pendingLeave > 0)
                        <span class="badge bg-warning text-dark">{{ $pendingLeave }}</span>
                    @endif
                </a>
                <a class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}" href="{{ route('complaints.index') }}">
                    <i class="fas fa-exclamation-triangle"></i><span>Complaints</span>
                    @php $pendingComplaints = \App\Models\Complaint::whereIn('status', ['submitted', 'in progress'])->count(); @endphp
                    @if($pendingComplaints > 0)
                        <span class="badge bg-danger">{{ $pendingComplaints }}</span>
                    @endif
                </a>
                
                <!-- Communication -->
                <div class="nav-section-title">Communication</div>
                <a class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
                    <i class="fas fa-bullhorn"></i><span>Announcements</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.rules.*') ? 'active' : '' }}" href="{{ route('admin.rules.index') }}">
                    <i class="fas fa-book"></i><span>Hostel Rules</span>
                </a>
                
                <!-- Visitors & Staff -->
                <div class="nav-section-title">Others</div>
                <a class="nav-link {{ request()->routeIs('visitors.*') ? 'active' : '' }}" href="{{ route('visitors.index') }}">
                    <i class="fas fa-users"></i><span>Visitors</span>
                </a>
                <a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.index') }}">
                    <i class="fas fa-id-badge"></i><span>Staff</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-chart-bar"></i><span>Reports</span>
                </a>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></button>
                </form>
            </div>
            
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-chevron-left"></i></button>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <header class="top-header">
                <div class="d-flex align-items-center">
                    <button class="mobile-toggle me-3" id="mobileToggle"><i class="fas fa-bars"></i></button>
                    <h1 class="page-title">@yield('page-title', 'Admin Dashboard')</h1>
                </div>
                <div class="header-actions">
                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="header-btn" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            @php
                                $pendingBookings = \App\Models\Payment::where('status', 'pending')->whereNotNull('room_id')->count();
                                $notifCount = \App\Models\HostelApplication::where('status', 'pending')->count() 
                                            + \App\Models\LeaveRequest::where('status', 'pending')->count()
                                            + \App\Models\Complaint::whereIn('status', ['submitted'])->count()
                                            + $pendingBookings;
                            @endphp
                            @if($notifCount > 0)
                                <span class="notification-badge">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 280px;">
                            <li class="dropdown-header fw-bold">Notifications</li>
                            
                            @if($pendingBookings > 0)
                            <li><a class="dropdown-item d-flex align-items-center" href="{{ route('payments.index', ['status' => 'pending', 'type' => 'booking']) }}">
                                <div class="bg-primary-subtle p-2 rounded-circle me-3">
                                    <i class="fas fa-home text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $pendingBookings }} New Room Bookings</div>
                                    <small class="text-muted">Requires verification</small>
                                </div>
                            </a></li>
                            @endif

                            @php $pendingApps = \App\Models\HostelApplication::where('status', 'pending')->count(); @endphp
                            @if($pendingApps > 0)
                            <li><a class="dropdown-item d-flex align-items-center" href="{{ route('applications.index', ['status' => 'pending']) }}">
                                <div class="bg-warning-subtle p-2 rounded-circle me-3">
                                    <i class="fas fa-file-alt text-warning"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $pendingApps }} Pending Applications</div>
                                    <small class="text-muted">Review new students</small>
                                </div>
                            </a></li>
                            @endif
                            
                            @php $pendingLeave = \App\Models\LeaveRequest::where('status', 'pending')->count(); @endphp
                            @if($pendingLeave > 0)
                            <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.leave.index') }}">
                                <div class="bg-info-subtle p-2 rounded-circle me-3">
                                    <i class="fas fa-calendar-alt text-info"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $pendingLeave }} Leave Requests</div>
                                    <small class="text-muted">Approval required</small>
                                </div>
                            </a></li>
                            @endif

                            @php $newComplaints = \App\Models\Complaint::where('status', 'submitted')->count(); @endphp
                            @if($newComplaints > 0)
                            <li><a class="dropdown-item d-flex align-items-center" href="{{ route('complaints.index') }}">
                                <div class="bg-danger-subtle p-2 rounded-circle me-3">
                                    <i class="fas fa-exclamation-circle text-danger"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $newComplaints }} New Complaints</div>
                                    <small class="text-muted">Urgent feedback</small>
                                </div>
                            </a></li>
                            @endif

                            @if($notifCount == 0)
                            <li class="p-3 text-center">
                                <i class="fas fa-bell-slash text-muted d-block mb-2"></i>
                                <span class="text-muted small">No new notifications</span>
                            </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center small text-primary fw-bold" href="{{ route('admin.notifications.index') }}">View All Notifications</a></li>
                        </ul>
                    </div>
                    
                    <!-- Theme Toggle -->
                    <button class="header-btn" id="themeToggle" type="button">
                        <i class="fas fa-sun" id="themeIcon"></i>
                        <span id="themeText">Light</span>
                    </button>
                    
                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <button class="header-btn" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i>
                            <span>{{ Auth::user()->name ?? 'Admin' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                                <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            
            <div class="content-container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileToggle = document.getElementById('mobileToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');

            const currentTheme = window.__INITIAL_THEME__ || localStorage.getItem('theme') || 'light';
            updateThemeUI(currentTheme);

            // Sidebar Toggle (Desktop)
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    const icon = sidebarToggle.querySelector('i');
                    icon.className = sidebar.classList.contains('collapsed') ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
                });
            }

            // Mobile Toggle
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                    sidebarOverlay.classList.toggle('active');
                });
            }

            // Close sidebar on overlay click
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('active');
                });
            }

            // Theme Toggle
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                    setTheme(currentTheme === 'light' ? 'dark' : 'light');
                });
            }

            function setTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                document.documentElement.className = theme + '-theme';
                localStorage.setItem('theme', theme);
                updateThemeUI(theme);
            }

            function updateThemeUI(theme) {
                if (themeIcon && themeText) {
                    themeIcon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
                    themeText.textContent = theme === 'dark' ? 'Dark' : 'Light';
                }
            }

            // Auto-hide alerts
            document.querySelectorAll('.alert').forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
