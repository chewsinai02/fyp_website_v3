<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Modern CSS Dependencies -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <title>@yield('title', 'SUC Hospital')</title>

    <style>
        :root {
            --primary: #0284C7;
            --primary-light: #38BDF8;
            --secondary: #06B6D4;
            --background: #F0F9FF;
            --text: #1E293B;
            --text-light: #64748B;
            --danger: #EF4444; 
            --sidebar-width: 250px;
            --header-height: 60px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--background);
        }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            height: 100vh;
            width: var(--sidebar-width);
            background: white;
            color: var(--text);
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* Logo Container */
        .logo-container {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .logo-container img {
            max-height: 50px;
            margin-bottom: 0.5rem;
        }

        /* Navigation */
        .nav.flex-column {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 1.5rem 0;
        }

        .nav-link {
            color: var(--text);
            padding: 0.8rem 1.5rem;
            margin: 0.2rem 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link:hover {
            background: var(--background);
            color: var(--primary);
            transform: translateX(5px);
        }

        .nav-link i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            color: var(--primary);
        }

        /* Profile Section */
        .profile-section {
            padding: 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            background: white;
            margin-top: auto;
            position: sticky;
            bottom: 0;
            width: 100%;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        }

        .profile-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid var(--primary-light);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            object-fit: cover;
        }

        .logout-btn {
            background: white;
            color: var(--danger);
            border: 2px solid var(--danger);
            width: 100%;
            padding: 0.75rem 1.5rem;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            right: 0;
            width: calc(100% - var(--sidebar-width));
            height: var(--header-height);
            background: white;
            z-index: 999;
            padding: 0 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: calc(var(--header-height) + 2rem) 2rem 2rem;
            min-height: 100vh;
        }

        /* Button Styling */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            background: white;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        /* Dropdown Styling */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: var(--background);
            color: var(--primary);
        }

        /* Text Gradient */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Utilities */
        .text-muted-light {
            color: var(--text-light);
        }

        /* Add to your existing <style> section */
        .message-notification .btn {
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .message-notification .btn:hover {
            background-color: var(--background);
            transform: translateY(-2px);
        }

        .message-notification .btn i {
            font-size: 1.2rem;
            color: var(--text);
        }

        .message-notification .badge {
            font-size: 0.65rem;
            padding: 0.35em 0.65em;
            min-width: 1.5em;
            transform: translate(-40%, -40%) !important;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .message-notification .badge:not(:empty) {
            animation: pulse 2s infinite;
        }

        /* Add to your existing style section */
        .nav-link.active {
            background: var(--background);
            color: var(--primary);
            font-weight: 600;
        }

        .nav-link.active i {
            color: var(--primary);
        }

        /* Add section divider */
        .nav-section {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-light);
            margin-top: 1rem;
        }

        /* Dropdown Styles */
        .header .dropdown {
            position: relative;
        }

        .header .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            z-index: 1500;  /* Higher than sidebar */
            min-width: 200px;
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            background: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.2s ease-in-out;
        }

        .header .dropdown-item {
            padding: 0.75rem 1.5rem;
            color: var(--text);
            transition: all 0.3s ease;
        }

        .header .dropdown-item:hover {
            background-color: var(--background);
            color: var(--primary);
            transform: translateX(5px);
        }

        .header .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }

        .header .dropdown-divider {
            margin: 0.5rem 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Profile */
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <a href="{{ route('nurseadminDashboard') }}" class="text-decoration-none text-white">
                <img src="{{ asset('images/logo.png') }}" alt="SUC Hospital" class="img-fluid mb-2">
                <h6 class="mb-0">SUC Hospital</h6>
            </a>
        </div>

        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a href="{{ route('nurseadminDashboard') }}" class="nav-link {{ request()->routeIs('nurseadminDashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('nurseadmin.nurseList') }}" class="nav-link {{ request()->routeIs('nurseadmin.nurseList*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Nurse Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('nurseadmin.roomList') }}" class="nav-link {{ request()->routeIs('nurseadmin.roomList*') ? 'active' : '' }}">
                    <i class="bi bi-door-open"></i>
                    <span>Room Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('nurseadmin.scheduleList') }}" class="nav-link {{ request()->routeIs('nurseadmin.scheduleList*') ? 'active' : '' }}">
                    <i class="bi bi-calendar2-check"></i>
                    <span>Nurse Schedules</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('nurseadmin.reports') }}" class="nav-link {{ request()->routeIs('nurseadmin.reports*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('full-calendar') }}" class="nav-link {{ request()->routeIs('full-calendar*') ? 'active' : '' }}">
                    <i class="bi bi-calendar2"></i>
                    <span>Calendar</span>
                </a>
            </li>
        </ul>

        <div class="profile-section">
            <div class="d-flex align-items-center mb-3">
                <img src="{{ auth()->user()->profile_picture ? asset(auth()->user()->profile_picture) : asset('images/profile.png') }}" 
                     alt="Profile" 
                     class="profile-img">
                <div class="ms-3">
                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                    <small class="text-muted-light">Nurse Administrator</small>
                </div>
            </div>
            <a href="#" onclick="confirmLogout(event)" class="btn logout-btn">
                <i class="bi bi-box-arrow-left"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="d-flex align-items-center">
            <button class="btn" onclick="window.history.back()">
                <i class="bi bi-arrow-left"></i>
            </button>
            <a href="{{ route('nurseadminDashboard') }}" class="text-decoration-none text-black">
                <img src="{{ asset('images/logo.png') }}" alt="SUC Hospital" class="img-fluid ms-3 rounded-circle" style="width: 5%; height: 5%; border-black">
                &nbsp;&nbsp;SUC Hospital        
            </a>
        </div>

        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('nurseadminDashboard') }}" class="btn btn-primary">
                <i class="bi bi-house"></i>
                <span>Home</span>
            </a>
            <div class="dropdown">
                <button type="button" 
                        class="btn p-0 border-0" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false"
                        id="profileDropdown">
                    <img src="{{ auth()->user()->profile_picture ? asset(auth()->user()->profile_picture) : asset('images/profile.png') }}"
                         alt="Profile" 
                         class="profile-img">
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('nurseadmin.manageProfile') }}">
                            <i class="bi bi-person me-2"></i>Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" onclick="confirmLogout(event)">
                            <i class="bi bi-box-arrow-left me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    @stack('scripts')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function confirmLogout(event) {
            event.preventDefault();
            if (confirm("Are you sure you want to log out?")) {
                document.getElementById('logout-form').submit();
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'))
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            });
        });
    </script>
    <script>
        // Debug Bootstrap availability
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? bootstrap.Dropdown.VERSION : 'not loaded');
            
            // Manual initialization of dropdown
            const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
            const dropdownList = [...dropdownElementList].map(dropdownToggleEl => {
                return new bootstrap.Dropdown(dropdownToggleEl, {
                    autoClose: true
                });
            });

            // Add click event listener
            document.getElementById('profileDropdown').addEventListener('click', function(e) {
                e.preventDefault();
                bootstrap.Dropdown.getOrCreateInstance(this).toggle();
            });
        });
    </script>
</body>
</html>