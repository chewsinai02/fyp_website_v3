<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <title>SUC Hospital - Welcome</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset('images/logo.png') }}" alt="SUC Logo" height="40" class="me-2">
                <span class="fw-bold text-primary">SUC Hospital</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="{{ auth()->user()->profile_picture ? asset(auth()->user()->profile_picture) : asset('images/profile.png') }}" 
                                     class="rounded-circle me-2" 
                                     width="35" 
                                     height="35"
                                     alt="Profile">
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->role === 'admin')
                                    <li><a class="dropdown-item" href="{{ route('adminDashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                @elseif(auth()->user()->role === 'doctor')
                                    <li><a class="dropdown-item" href="{{ route('doctorDashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                 @elseif(auth()->user()->role === 'nurse_admin')
                                    <li><a class="dropdown-item" href="{{ route('nurseadminDashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                @elseif(auth()->user()->role === 'nurse')
                                    <li><a class="dropdown-item" href="{{ route('nurseDashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Hidden Logout Form -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="margin-top: 70px;">
        <!-- Hero Section -->
        <div class="hero-section text-center py-5 mb-5">
            <div class="container">
                <h1 class="display-4 text-gradient mb-3">Welcome to SUC Hospital</h1>
                <p class="lead text-muted mb-4">Providing Quality Healthcare with Compassion</p>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <img src="{{ asset('images/logo.png') }}" alt="Hospital Logo" class="img-fluid mb-4" style="max-width: 200px;">
                    </div>
                </div>
                <h5 class="text-muted">A Real-Time Communication and<br>
                    Management System for Families and<br>
                    Hospital in Patient Care System</h5>
            </div>
        </div>

        <!-- Features Section -->
        <div class="container mb-5">
            <div class="row g-4">
                <!-- Staff Login Card -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-wrapper mb-3">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3>Medical Staff</h3>
                        <p class="text-muted">Access your dashboard and manage patient care efficiently.</p>
                        @if (Auth::check())
                            @php
                                $role = Auth::user()->role;
                                $dashboardRoute = match($role) {
                                    'nurse_admin' => 'nurseadminDashboard',
                                    'nurse' => 'nurseDashboard',
                                    'doctor' => 'doctor_dashboard',
                                    'admin' => 'dashboard',
                                    default => 'home'
                                };
                            @endphp
                            
                            <a href="{{ route($dashboardRoute) }}" class="btn btn-primary mt-auto">
                                <i class="fas fa-columns me-2"></i>Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary mt-auto">
                                <i class="fas fa-sign-in-alt me-2"></i>Staff Login
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Patient Portal Card -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-wrapper mb-3">
                            <i class="fas fa-hospital-user"></i>
                        </div>
                        <h3>Patient Portal</h3>
                        <p class="text-muted">View your medical records and upcoming appointments.</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary mt-auto">
                            <i class="fas fa-user me-2"></i>Patient Access
                        </a>
                    </div>
                </div>

                <!-- Emergency Card -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-wrapper mb-3 bg-danger">
                            <i class="fas fa-ambulance"></i>
                        </div>
                        <h3>Emergency</h3>
                        <p class="text-muted">24/7 emergency services and immediate care.</p>
                        <a href="mailto:chewsinai2002@gmail.com" class="btn btn-danger mt-auto">
                            <i class="fas fa-phone-alt me-2"></i>Emergency Contact
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="bg-light py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="mb-4">Why Choose SUC Hospital?</h2>
                        <div class="info-item mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Expert Medical Professionals</span>
                        </div>
                        <div class="info-item mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>State-of-the-art Facilities</span>
                        </div>
                        <div class="info-item mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Patient-Centered Care</span>
                        </div>
                        <div class="info-item mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>24/7 Emergency Services</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img src="{{ asset('images/doctor.png') }}" alt="Hospital" class="img-fluid rounded-3 shadow">
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>SUC Hospital</h5>
                    <p class="mb-0">Providing quality healthcare services</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; {{ date('Y') }} SUC Hospital. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <style>
        :root {
            --primary: #0284C7;
            --primary-light: #38BDF8;
            --secondary: #06B6D4;
            --success: #22C55E;
            --danger: #EF4444;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .hero-section {
            background: linear-gradient(135deg, #f6f8ff 0%, #f1f5ff 100%);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .icon-wrapper.bg-danger {
            background: linear-gradient(135deg, var(--danger), #ff6b6b);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .info-item {
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
        }

        .info-item i {
            font-size: 1.2rem;
            margin-right: 1rem;
        }

        .navbar {
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 50px 0;
            }
            
            .feature-card {
                margin-bottom: 1rem;
            }
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 0.5rem;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8fafc;
            transform: translateX(5px);
        }

        .dropdown-item.text-danger:hover {
            background-color: #fee2e2;
        }

        .nav-link {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: #f8fafc;
        }
    </style>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
