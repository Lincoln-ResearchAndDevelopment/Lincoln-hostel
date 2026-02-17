<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script>
      // IMMEDIATE THEME APPLICATION - Runs before any CSS
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

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LincHostel | Admin Dashboard</title>

    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Bootstrap 5.3.2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Font Awesome 6.4.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap 5.3.2 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --header-height: 60px;
            --transition-speed: 0.3s;
            --primary-color: 6c757d;
            --secondary-color: #fff;
            --gray-light: #f8f9fa;
            --gray-medium: #e9ecef;
            --gray-dark: #6c757d;
            --border-color: #dee2e6;
        }

        [data-theme="dark"] {
            --primary-color: #fff;
            --secondary-color: #000;
            --gray-light: #212529;
            --gray-medium: #343a40;
            --gray-dark: #adb5bd;
            --border-color: #495057;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--secondary-color);
            color: var(--primary-color);
            transition: background-color var(--transition-speed), color var(--transition-speed);
            overflow-x: hidden;
        }

        /* Layout */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--secondary-color);
            color: var(--primary-color);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            border-right: 1px solid var(--border-color);
            transition: width var(--transition-speed);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
            white-space: nowrap;
        }

        .sidebar-brand i {
            margin-right: 0.5rem;
        }

        .sidebar-brand img {
            height: 40px;
            width: auto;
            max-width: 40px;
            object-fit: contain;
            margin-right: 0.5rem;
            transition: all var(--transition-speed);
        }

        .sidebar.collapsed .sidebar-brand img {
            height: 30px;
            max-width: 30px;
            margin-right: 0;
        }

        .sidebar.collapsed .sidebar-brand span {
            display: none;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--primary-color);
            text-decoration: none;
            transition: background-color 0.2s;
            white-space: nowrap;
        }

        .nav-link:hover, .nav-link.active {
            background-color: var(--gray-light);
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar-toggle {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .sidebar-toggle:hover {
            background-color: var(--gray-light);
        }

        .sidebar.collapsed .sidebar-toggle {
            left: 50%;
            transform: translateX(-50%);
        }

        /* Main content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed);
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        .top-header {
            height: var(--header-height);
            background-color: var(--secondary-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: var(--primary-color);
        }

        .content-container {
            padding: 1.5rem;
        }

        /* Theme toggle */
        .theme-toggle {
            background: none;
            border: 1px solid var(--border-color);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
        }

        .theme-toggle:hover {
            background-color: var(--gray-light);
        }

        .theme-toggle i {
            margin-right: 0.5rem;
        }

        /* Notification dropdown */
        .notification-dropdown {
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* User dropdown */
        .user-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--gray-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
            }
            
            .sidebar.collapsed {
                width: 0;
            }
            
            .main-content {
                margin-left: var(--sidebar-collapsed-width);
            }
            
            .main-content.expanded {
                margin-left: 0;
            }
            
            .sidebar-brand span {
                display: none;
            }
            
            .nav-link span {
                display: none;
            }
            
            .nav-link {
                justify-content: center;
            }
            
            .nav-link i {
                margin-right: 0;
            }
        }

        /* Cards and content styling */
        .card {
            background-color: var(--secondary-color);
            border: 1px solid var(--border-color);
            color: var(--primary-color);
        }

        .table {
            color: var(--primary-color);
        }

        .alert {
            border: 1px solid var(--border-color);
        }

        /* Theme transition */
        .theme-switching {
            transition: none !important;
        }

        .theme-transition * {
            transition: background-color var(--transition-speed), color var(--transition-speed), border-color var(--transition-speed) !important;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ request()->is('superadmin*') ? route('superadmin.dashboard') : route('dashboard') }}" class="sidebar-brand">
                    <img src="{{ asset('assets/img/lincoln-logo.png') }}" alt="Lincoln Logo">
                    <span>LincHostel</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                @php
                    // Determine authentication type based on current route and guards
                    $isOnSuperAdminRoute = request()->is('superadmin*');
                    // Fix: Use 'student/*' to avoid matching 'students' (plural) admin routes
                    $isOnRegularAdminRoute = !request()->is('superadmin*') && !request()->is('student/*') && !request()->is('student');

                    // Check authentication based on route context
                    if ($isOnSuperAdminRoute) {
                        $isAuthenticated = Auth::guard('superadmin')->check();
                        $currentUser = Auth::guard('superadmin')->user();
                        $isSuperAdmin = true;
                        $isRegularAdmin = false;
                    } elseif ($isOnRegularAdminRoute) {
                        $isAuthenticated = Auth::guard('web')->check();
                        $currentUser = Auth::guard('web')->user();
                        $isSuperAdmin = false;
                        $isRegularAdmin = $currentUser && $currentUser->isAdmin();
                    } else {
                        $isAuthenticated = false;
                        $currentUser = null;
                        $isSuperAdmin = false;
                        $isRegularAdmin = false;
                    }
                @endphp

                @if($isAuthenticated)
                    <ul class="nav flex-column">
                        @if($isSuperAdmin)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" href="{{ route('superadmin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Super Dashboard</span>
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        @endif

                        @if($isRegularAdmin && !$isSuperAdmin)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('hostels.*') ? 'active' : '' }}" href="{{ route('hostels.index') }}">
                                    <i class="fas fa-building"></i>
                                    <span>Hostels</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">
                                    <i class="fas fa-door-open"></i>
                                    <span>Rooms</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                                    <i class="fas fa-user-graduate"></i>
                                    <span>Students</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Payments</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}" href="{{ route('complaints.index') }}">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Complaints</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('visitors.*') ? 'active' : '' }}" href="{{ route('visitors.index') }}">
                                    <i class="fas fa-users"></i>
                                    <span>Visitors</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('staffs.*') ? 'active' : '' }}" href="{{ route('staff.index') }}">
                                    <i class="fas fa-staff-snake"></i>
                                    <span>Staffs</span>
                                </a>
                            </li>
                        @endif

                        {{-- Profile link based on user type --}}
                        @if($isSuperAdmin)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('superadmin.profile.*') ? 'active' : '' }}"
                                   href="{{ route('superadmin.profile.show') }}">
                                    <i class="fas fa-shield"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                        @elseif($isRegularAdmin)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                                   href="{{ route('profile.index') }}">
                                    <i class="fas fa-shield"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                @endif
            </nav>
            
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </button>
        </aside>

        <!-- Main Content -->
  <main class="main-content" id="mainContent">
            <header class="top-header">
                <h1 class="page-title">@yield('page-title', request()->is('superadmin*') ? 'Super Admin Dashboard' : 'Admin Dashboard')</h1>
                <div class="d-flex align-items-center">
                      <button class="theme-toggle me-3" id="themeToggle" type="button" aria-label="Toggle dark mode">
                        <i class="fas fa-sun" id="themeIcon"></i>
                        <span id="themeText">Light</span>
                    </button>
                    
                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown">
                            <li><a class="dropdown-item" href="#">No new notifications</a></li>
                        </ul>
                    </div>
                    <!-- User -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-2"></i>
                            @php
                                // Use the same logic as sidebar for consistency
                                $isOnSuperAdminRoute = request()->is('superadmin*');
                                $isOnRegularAdminRoute = !request()->is('superadmin*') && !request()->is('student*');

                                if ($isOnSuperAdminRoute) {
                                    $currentUser = Auth::guard('superadmin')->user();
                                    $userName = $currentUser ? $currentUser->name : 'Super Admin';
                                    $logoutRoute = 'superadmin.logout';
                                } elseif ($isOnRegularAdminRoute) {
                                    $currentUser = Auth::guard('web')->user();
                                    $userName = $currentUser ? $currentUser->name : 'Admin';
                                    $logoutRoute = 'logout';
                                } else {
                                    $currentUser = null;
                                    $userName = 'Guest';
                                    $logoutRoute = 'login';
                                }
                            @endphp
                            {{ $userName }}
                        </a>
                        @if($currentUser)
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            @if($isOnSuperAdminRoute)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('superadmin.profile.*') ? 'active' : '' }}"
                                       href="{{ route('superadmin.profile.show') }}">
                                        <i class="fas fa-shield"></i>
                                        <span>Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('superadmin.logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                                       href="{{ route('profile.index') }}">
                                        <i class="fas fa-shield"></i>
                                        <span>Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            @endif
                        </ul>
                        @endif
                    </div>
                </div>
            </header>
            <div class="content-container">
                @yield('content')
            </div>
        </main>
    </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced Theme Management - No more flash!
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');
            const body = document.body;
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

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

            // Sidebar toggle
            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    
                    // Update the toggle icon
                    const icon = sidebarToggle.querySelector('i');
                    if (sidebar.classList.contains('collapsed')) {
                        icon.className = 'fas fa-chevron-right';
                    } else {
                        icon.className = 'fas fa-chevron-left';
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

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
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

        // Bootstrap tooltips initialization
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

    <script>
        // Check admin session status every 60 seconds
        setInterval(() => {
            fetch("{{ url('/check-session') }}")
            .then(response => {
                if (response.status === 401) {
                    // Use the same logic as sidebar for consistency
                    const isOnSuperAdminRoute = window.location.pathname.startsWith('/superadmin');
                    const isOnRegularAdminRoute = !isOnSuperAdminRoute && !window.location.pathname.startsWith('/student');

                    if (isOnSuperAdminRoute) {
                        window.location.href = "{{ route('superadmin.login') }}";
                    } else {
                        window.location.href = "{{ route('login') }}";
                    }
                }
            });
        }, 60000); // every 60 seconds
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var dropdownElements = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        dropdownElements.map(function (el) {
            return new bootstrap.Dropdown(el);
        });
    });
</script>

</body>
</html>