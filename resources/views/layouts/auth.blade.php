<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <title>{{ config('app.name', 'SUC Hospital') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Styles -->
    <style>
        :root {
            --primary: #0284C7;
            --primary-light: #38BDF8;
            --secondary: #06B6D4;
            --accent: #818CF8;
            --success: #10B981;
            --background: #F8FAFC;
            --card: #FFFFFF;
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
            min-height: 100vh;
            background: var(--background);
            overflow-x: hidden;
        }

        #app {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .auth-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .auth-card {
            background: var(--card);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                        0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .brand-area {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-logo {
            height: 50px;
            margin-bottom: 20px;
        }

        .brand-area h1 {
            color: var(--text);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .brand-area p {
            color: var(--text-light);
            font-size: 16px;
        }

        /* Medical Background Elements */
        .medical-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            opacity: 0.4;
        }

        .medical-shape {
            position: absolute;
            background: linear-gradient(135deg, var(--primary-light), var(--secondary));
            border-radius: 50%;
            filter: blur(40px);
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -100px;
            opacity: 0.1;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -50px;
            opacity: 0.1;
        }

        /* Medical Icons Animation */
        .medical-icons {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .medical-icon {
            position: absolute;
            color: var(--primary);
            opacity: 0.2;
            animation: float 20s infinite;
        }

        .medical-icon:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
        .medical-icon:nth-child(2) { top: 20%; right: 10%; animation-delay: 5s; }
        .medical-icon:nth-child(3) { bottom: 20%; left: 15%; animation-delay: 10s; }
        .medical-icon:nth-child(4) { bottom: 15%; right: 15%; animation-delay: 15s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 24px;
            }
            
            .brand-area h1 {
                font-size: 24px;
            }
        }
    </style>

    @stack('styles')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div id="app">
        <main class="auth-wrapper">
            <!-- Medical Background Shapes -->
            <div class="medical-shapes">
                <div class="medical-shape shape-1"></div>
                <div class="medical-shape shape-2"></div>
            </div>

            <!-- Floating Medical Icons -->
            <div class="medical-icons">
                <i class="bi bi-heart-pulse medical-icon" style="font-size: 24px;"></i>
                <i class="bi bi-capsule medical-icon" style="font-size: 28px;"></i>
                <i class="bi bi-hospital medical-icon" style="font-size: 32px;"></i>
                <i class="bi bi-clipboard2-pulse medical-icon" style="font-size: 26px;"></i>
            </div>

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>