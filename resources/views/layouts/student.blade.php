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
    <title>LincHostel | Student Portal</title>

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

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        .app-container { display: flex; min-height: 100vh; }

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
        .sidebar-brand i { color: var(--primary-color); }
        .sidebar.collapsed .sidebar-brand span { display: none; }
        .sidebar.collapsed .sidebar-brand img { 
            height: 35px; 
            max-width: 35px;
        }
        .sidebar-nav { padding: 0.5rem 0; flex: 1; }
        .nav-section-title {
            padding: 0.75rem 1.25rem 0.5rem;
            font-size: 0.7rem; text-transform: uppercase;
            letter-spacing: 0.5px; color: var(--text-secondary); font-weight: 600;
        }
        .sidebar.collapsed .nav-section-title { display: none; }
        .nav-link {
            padding: 0.65rem 1.25rem; color: var(--text-primary);
            text-decoration: none; display: flex; align-items: center;
            gap: 0.75rem; transition: all 0.2s;
            border-left: 3px solid transparent; font-size: 0.9rem;
        }
        .nav-link:hover { background-color: var(--sidebar-active-bg); color: var(--primary-color); }
        .nav-link.active {
            background-color: var(--sidebar-active-bg);
            color: var(--primary-color);
            border-left-color: var(--primary-color); font-weight: 600;
        }
        .nav-link i { width: 20px; text-align: center; font-size: 1rem; }
        .sidebar.collapsed .nav-link { justify-content: center; padding: 0.75rem; }
        .sidebar.collapsed .nav-link span { display: none; }
        .sidebar.collapsed .nav-link i { margin: 0; font-size: 1.1rem; }
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
        .user-id { font-size: 0.75rem; color: var(--text-secondary); }
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
        .main-content {
            flex: 1; margin-left: var(--sidebar-width);
            min-height: 100vh; background-color: var(--bg-primary);
            transition: margin-left var(--transition-speed);
        }
        .main-content.expanded { margin-left: var(--sidebar-collapsed-width); }
        .top-header {
            height: var(--header-height); background: var(--navbar-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem; position: sticky; top: 0; z-index: 999;
        }
        .page-title { font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--text-primary); }
        .header-actions { display: flex; align-items: center; gap: 0.75rem; }
        .mobile-toggle {
            display: none; background: none; border: none;
            color: var(--text-primary); font-size: 1.25rem; cursor: pointer; padding: 0.5rem;
        }
        .theme-toggle {
            background: none; border: 1px solid var(--border-color);
            color: var(--text-primary); padding: 0.4rem 0.75rem;
            border-radius: 6px; cursor: pointer; display: flex;
            align-items: center; gap: 0.5rem; font-size: 0.85rem; transition: all 0.2s;
        }
        .theme-toggle:hover { background-color: var(--bg-secondary); border-color: var(--primary-color); }
        .content-container { padding: 1.5rem; }
        .card {
            background-color: var(--navbar-bg); border: 1px solid var(--border-color);
            border-radius: 0.5rem; color: var(--text-primary);
        }
        .card-header { background-color: var(--navbar-bg); border-bottom: 1px solid var(--border-color); }
        .table { color: var(--text-primary); }
        .table thead th { border-bottom-color: var(--border-color); }
        .table td, .table th { border-color: var(--border-color); }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--primary-hover); border-color: var(--primary-hover); }
        .btn-outline-primary { color: var(--primary-color); border-color: var(--primary-color); }
        .btn-outline-primary:hover { background-color: var(--primary-color); border-color: var(--primary-color); color: white; }
        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .breadcrumb-item a { color: var(--primary-color); text-decoration: none; }
        .breadcrumb-item a:hover { color: var(--primary-hover); text-decoration: underline; }
        .badge.bg-primary { background-color: var(--primary-color) !important; }
        .alert { border: 1px solid var(--border-color); }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
        }
        .btn-logout {
            width: 100%; background: transparent;
            border: 1px solid var(--primary-color); color: var(--primary-color);
            padding: 0.5rem; border-radius: 6px; font-size: 0.85rem; transition: all 0.2s;
        }
        .btn-logout:hover { background: var(--primary-color); color: white; }
        .sidebar.collapsed .btn-logout span { display: none; }
        .bg-primary-subtle { background-color: var(--primary-light) !important; }
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
        }

        /* Bottom Nav for Mobile */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--navbar-bg);
            border-top: 1px solid var(--border-color);
            z-index: 1000;
            padding: 0.5rem 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }

        .bottom-nav-inner {
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .bottom-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.65rem;
            gap: 2px;
            transition: all 0.2s;
            flex: 1;
        }

        .bottom-nav-link i {
            font-size: 1.2rem;
            margin-bottom: 2px;
        }

        .bottom-nav-link.active {
            color: var(--primary-color);
        }

        @media (max-width: 767.98px) {
            .bottom-nav { display: block; }
            .content-container { padding: 1rem 1rem 5rem 1rem !important; }
            .top-header { padding: 0 1rem; }
            .page-title { font-size: 1.1rem; }
            .theme-toggle span { display: none; }
            .theme-toggle { padding: 0.4rem; }
        }

        /* Responsive Tables Enhancement */
        .table-responsive {
            border: 0;
        }
        
        @media (max-width: 576px) {
            .card-header { padding: 0.75rem 1rem; }
            .card-body { padding: 1rem; }
            .btn-sm { padding: 0.4rem 0.6rem; }

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
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('student.dashboard') }}" class="sidebar-brand">
                    <img src="{{ asset('assets/img/lincoln-logo.png') }}" alt="Lincoln University Logo">
                    <span>LincHostel</span>
                </a>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section-title">Main</div>
                <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
                <a class="nav-link {{ request()->routeIs('student.profile.*') ? 'active' : '' }}" href="{{ route('student.profile.index') }}">
                    <i class="fas fa-user"></i><span>My Profile</span>
                </a>

                <div class="nav-section-title">Accommodation</div>
                @if(auth()->guard('student')->user()->room_id)
                <a class="nav-link {{ request()->routeIs('student.room.*') ? 'active' : '' }}" href="{{ route('student.room.details') }}">
                    <i class="fas fa-door-open"></i><span>My Room</span>
                </a>
                @endif
                <a class="nav-link {{ request()->routeIs('student.hostels.*') ? 'active' : '' }}" href="{{ route('student.hostels.index') }}">
                    <i class="fas fa-building"></i><span>Browse Hostels</span>
                </a>
                <a class="nav-link {{ request()->routeIs('student.hostel.rules') ? 'active' : '' }}" href="{{ route('student.hostel.rules') }}">
                    <i class="fas fa-book"></i><span>Hostel Rules</span>
                </a>

                <div class="nav-section-title">Information</div>
                <a class="nav-link {{ request()->routeIs('student.announcements.*') ? 'active' : '' }}" href="{{ route('student.announcements.index') }}">
                    <i class="fas fa-bullhorn"></i><span>Announcements</span>
                </a>
                <a class="nav-link {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}" href="{{ route('student.attendance.index') }}">
                    <i class="fas fa-clipboard-check"></i><span>Attendance</span>
                </a>

                <div class="nav-section-title">Finance</div>
                <a class="nav-link {{ request()->routeIs('student.fees.*') ? 'active' : '' }}" href="{{ route('student.fees.index') }}">
                    <i class="fas fa-money-bill-wave"></i><span>Fee Details</span>
                </a>

                <div class="nav-section-title">Requests</div>
                <a class="nav-link {{ request()->routeIs('student.leave.*') ? 'active' : '' }}" href="{{ route('student.leave.index') }}">
                    <i class="fas fa-calendar-alt"></i><span>Leave Requests</span>
                </a>
                <a class="nav-link {{ request()->routeIs('student.complaints.*') ? 'active' : '' }}" href="{{ route('student.complaints.index') }}">
                    <i class="fas fa-exclamation-circle"></i><span>Complaints</span>
                </a>
                <a class="nav-link {{ request()->routeIs('student.notifications') ? 'active' : '' }}" href="{{ route('student.notifications') }}">
                    <i class="fas fa-bell"></i><span>Notifications</span>
                    @php $unreadCount = auth()->guard('student')->user()->notifications()->unread()->count(); @endphp
                    <span id="sidebar-notif-count" class="badge bg-danger ms-auto {{ $unreadCount > 0 ? '' : 'd-none' }}">{{ $unreadCount }}</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->guard('student')->user()->first_name, 0, 1)) }}</div>
                    <div class="user-details">
                        <div class="user-name">{{ auth()->guard('student')->user()->full_name }}</div>
                        <div class="user-id">{{ auth()->guard('student')->user()->admission_number }}</div>
                    </div>
                </div>
                <form action="{{ route('student.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></button>
                </form>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-chevron-left"></i></button>
        </aside>

        <main class="main-content" id="mainContent">
            <header class="top-header">
                <div class="d-flex align-items-center">
                    <button class="mobile-toggle me-3" id="mobileToggle"><i class="fas fa-bars"></i></button>
                    <h1 class="page-title">@yield('page-title', 'Student Portal')</h1>
                </div>
                <div class="header-actions">
                    <!-- Notifications -->
                    <a href="{{ route('student.notifications') }}" class="theme-toggle position-relative" style="text-decoration: none;">
                        <i class="fas fa-bell"></i>
                        @php $unreadCount = auth()->guard('student')->user()->notifications()->unread()->count(); @endphp
                        <span id="header-notif-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unreadCount > 0 ? '' : 'd-none' }}" style="font-size: 0.65rem;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    </a>
                    <button class="theme-toggle" id="themeToggle" type="button">
                        <i class="fas fa-sun" id="themeIcon"></i>
                        <span id="themeText">Light</span>
                    </button>
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
                @yield('content')
            </div>
        </main>

        <!-- Mobile Bottom Nav -->
        <nav class="bottom-nav">
            <div class="bottom-nav-inner">
                <a href="{{ route('student.dashboard') }}" class="bottom-nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('student.announcements.index') }}" class="bottom-nav-link {{ request()->routeIs('student.announcements.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>
                    <span>Notices</span>
                </a>
                <a href="{{ route('student.fees.index') }}" class="bottom-nav-link {{ request()->routeIs('student.fees.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Fees</span>
                </a>
                <a href="{{ route('student.complaints.index') }}" class="bottom-nav-link {{ request()->routeIs('student.complaints.*') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Issues</span>
                </a>
                <a href="{{ route('student.profile.index') }}" class="bottom-nav-link {{ request()->routeIs('student.profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Realtime Notification Toasts -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1060;">
        <div id="notif-toast" class="toast hide border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-primary text-white">
                <i class="fas fa-bell me-2"></i>
                <strong class="me-auto" id="toast-title">Notification</strong>
                <small id="toast-time">Just now</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white text-dark py-3" id="toast-message">
                New notification received.
            </div>
            <div class="toast-footer p-2 bg-light text-end">
                <a href="{{ route('student.notifications') }}" class="btn btn-sm btn-outline-primary py-1">View All</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Existing sidebar/theme logic
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileToggle = document.getElementById('mobileToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');

            // --- REALTIME NOTIFICATION POLLING ---
            let lastNotifId = localStorage.getItem('last_notif_id') || 0;
            const toastEl = document.getElementById('notif-toast');
            const toast = new bootstrap.Toast(toastEl, { delay: 10000 });
            
            function pollNotifications() {
                fetch('{{ route("student.notifications.fetch") }}')
                    .then(response => response.json())
                    .then(data => {
                        // Update UI counts
                        const headerCount = document.getElementById('header-notif-count');
                        const sidebarCount = document.getElementById('sidebar-notif-count');
                        
                        if (data.unreadCount > 0) {
                            headerCount.classList.remove('d-none');
                            headerCount.textContent = data.unreadCount > 9 ? '9+' : data.unreadCount;
                            sidebarCount.classList.remove('d-none');
                            sidebarCount.textContent = data.unreadCount;

                            // Check for new notification to show Toast
                            if (data.notifications.length > 0) {
                                const latest = data.notifications[0];
                                if (latest.id > lastNotifId) {
                                    // If we are on the notifications list page and a new notification arrived, 
                                    // we might want to refresh to show it in the list
                                    const isNotificationPage = window.location.href.includes('/student/notifications');
                                    
                                    lastNotifId = latest.id;
                                    localStorage.setItem('last_notif_id', lastNotifId);
                                    
                                    // Only show toast if NOT on the notifications page (to avoid redundancy)
                                    // OR refresh if ON the notifications page
                                    if (isNotificationPage) {
                                        window.location.reload(); 
                                    } else {
                                        // Populate and show Toast
                                        document.getElementById('toast-title').textContent = latest.title;
                                        document.getElementById('toast-message').textContent = latest.message;
                                        toast.show();
                                    }
                                }
                            }
                        } else {
                            if(headerCount) headerCount.classList.add('d-none');
                            if(sidebarCount) sidebarCount.classList.add('d-none');
                        }
                    })
                    .catch(error => console.error('Error fetching notifications:', error));
            }

            // Start polling every 10 seconds
            setInterval(pollNotifications, 10000);
            // Initial poll
            pollNotifications();
            // --- END POLLING ---

            const currentTheme = window.__INITIAL_THEME__ || localStorage.getItem('theme') || 'light';
            updateThemeUI(currentTheme);

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    const icon = sidebarToggle.querySelector('i');
                    icon.className = sidebar.classList.contains('collapsed') ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
                });
            }

            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                    sidebarOverlay.classList.toggle('active');
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('active');
                });
            }

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
