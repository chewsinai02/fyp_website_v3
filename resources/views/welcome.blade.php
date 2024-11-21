<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MedCare') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #0284C7;
            --primary-light: #38BDF8;
            --secondary: #06B6D4;
            --background: #F0F9FF;
            --text: #1E293B;
            --text-light: #64748B;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            height: 100vh;
            overflow: hidden;
            background: var(--background);
        }

        /* Navbar */
        .navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            transition: color 0.2s;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
        }

        /* Main Content */
        .main-content {
            height: calc(100vh - 72px);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Left Side */
        .content-side {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2rem;
            padding: 2rem;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-text p {
            font-size: 1.2rem;
            color: var(--text-light);
            margin-bottom: 2rem;
        }

        .feature-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .feature-card {
            background: white;
            padding: 1.25rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .feature-card i {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .feature-card h3 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        /* Right Side */
        .image-side {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            height: 100%;
            padding: 2rem;
        }

        .hero-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 1rem;
            }

            .image-side {
                display: none;
            }

            .nav-links a:not(.btn) {
                display: none;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/" class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="SUC Hospital" class="logo-image" style="width: 5%; height: 5%;">
                SUC Hospital
            </a>
            
            <div class="nav-links">
                <a href="#services" class="nav-link">Services</a>
                <a href="#about" class="nav-link">About</a>
                <a href="#contact" class="nav-link">Contact</a>
                
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Sign In</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="content-side">
            <div class="hero-text">
                <h1>Your Care, Make Easy</h1>
                <p>Experience modern healthcare solutions with our comprehensive medical services.</p>
                <a href="{{ route('login') }}" class="btn btn-primary">Get Started</a>
            </div>

            <div class="feature-cards">
                <div class="feature-card">
                    <i class="bi bi-calendar-check"></i>
                    <h3>Easy Scheduling</h3>
                </div>
                <div class="feature-card">
                    <i class="bi bi-file-medical"></i>
                    <h3>Digital Records</h3>
                </div>
                <div class="feature-card">
                    <i class="bi bi-shield-check"></i>
                    <h3>Secure & Private</h3>
                </div>
                <div class="feature-card">
                    <i class="bi bi-heart-pulse"></i>
                    <h3>Expert Care</h3>
                </div>
            </div>
        </div>

        <div class="image-side">
            <img src="{{ asset('images/doctor.png') }}" alt="Doctor" class="hero-image">
        </div>
    </div>
</body>
</html>