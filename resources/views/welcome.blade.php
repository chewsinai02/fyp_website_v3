<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SUC Hospital') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
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

        .section {
            padding: 4rem 2rem;
        }

        .section-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: var(--text);
        }

        /* Services Styles */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .service-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .service-card h3 {
            margin-bottom: 1rem;
            color: var(--text);
        }

        .service-card p {
            color: var(--text-light);
        }

        /* About Styles */
        .about-content {
            display: grid;
            gap: 2rem;
        }

        .about-text {
            text-align: center;
        }

        .about-text p {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item h3 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        /* Contact Styles */
        .contact-content {
            display: grid;
            gap: 2rem;
        }

        .contact-info {
            display: grid;
            gap: 2rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .contact-item i {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .contact-item h3 {
            margin-bottom: 0.25rem;
            color: var(--text);
        }

        .contact-item p {
            color: var(--text-light);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .section {
                padding: 2rem 1rem;
            }

            .section-title {
                font-size: 2rem;
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

    <!-- Services Section -->
    <section id="services" class="section">
        <div class="section-container">
            <h2 class="section-title">Our Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <i class="bi bi-hospital"></i>
                    <h3>Emergency Care</h3>
                    <p>24/7 emergency medical services with state-of-the-art facilities.</p>
                </div>
                <div class="service-card">
                    <i class="bi bi-clipboard2-pulse"></i>
                    <h3>General Check-ups</h3>
                    <p>Comprehensive health screenings and preventive care.</p>
                </div>
                <div class="service-card">
                    <i class="bi bi-capsule"></i>
                    <h3>Pharmacy Services</h3>
                    <p>Full-service pharmacy with prescription management.</p>
                </div>
                <div class="service-card">
                    <i class="bi bi-heart-pulse"></i>
                    <h3>Specialized Care</h3>
                    <p>Expert care in various medical specialties.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section">
        <div class="section-container">
            <h2 class="section-title">About Us</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>SUC Hospital is committed to providing exceptional healthcare services to our community. With our team of dedicated healthcare professionals and modern facilities, we ensure the highest quality of patient care.</p>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <h3>50+</h3>
                            <p>Medical Experts</p>
                        </div>
                        <div class="stat-item">
                            <h3>24/7</h3>
                            <p>Emergency Care</p>
                        </div>
                        <div class="stat-item">
                            <h3>1000+</h3>
                            <p>Patients Served</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <div class="section-container">
            <h2 class="section-title">Contact Us</h2>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="bi bi-geo-alt"></i>
                        <div>
                            <h3>Location</h3>
                            <p>PTD 64888, Jalan Selatan Utama, KM 15, Off, Skudai Lbh, 81300 Skudai, Johor</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-telephone"></i>
                        <div>
                            <h3>Phone</h3>
                            <p>+60-177423008</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>chewsinai2002@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>