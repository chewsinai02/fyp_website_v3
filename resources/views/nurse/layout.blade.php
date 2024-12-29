<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Nurse task management system">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Modern CSS Dependencies -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

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

        .floating-calls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }

        .calls-wrapper {
            position: relative;
        }

        .active-calls {
            position: absolute;
            bottom: 100%;
            right: 0;
            width: 300px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            margin-bottom: 10px;
        }

        .active-calls.show {
            max-height: 500px;
            overflow-y: auto;
        }

        .calls-toggle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .calls-toggle i {
            font-size: 24px;
        }

        .calls-list .call-item {
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid var(--danger);
        }

        .badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }

        #callDetailsContent {
            max-height: 60vh;
            overflow-y: auto;
        }
    </style>
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-database.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-database-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-database-compat.js"></script>

    <!-- Initialize Firebase (add this before closing </body>) -->
    <script>
        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyAiElkmNSl0K-N0Rz4kuqKAXrr6Eg7oo64",
            authDomain: "fyptestv2-37c45.firebaseapp.com",
            databaseURL: "https://fyptestv2-37c45-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "fyptestv2-37c45",
            storageBucket: "fyptestv2-37c45.firebasestorage.app",
            messagingSenderId: "500961952253",
            appId: "1:500961952253:web:a846193490974d3667d994"
        };

        // Initialize Firebase
        try {
            firebase.initializeApp(firebaseConfig);
            console.log('Firebase initialized successfully');
        } catch (error) {
            console.error('Firebase initialization error:', error);
        }

        // Get database reference
        const db = firebase.database();

        // Test connection
        console.log('Testing initial Firebase connection...');
        db.ref('.info/connected').on('value', (snapshot) => {
            const connected = snapshot.val();
            console.log('Initial Firebase connection status:', connected ? 'Connected' : 'Disconnected');
        });

        // Test database write/read
const testRef = db.ref('test');
testRef.set({
    message: 'Hello Firebase!',
    timestamp: firebase.database.ServerValue.TIMESTAMP
})
.then(() => {
    console.log('Test write successful');
})
.catch((error) => {
    console.error('Test write failed:', error);
});

// Listen for changes
testRef.on('value', (snapshot) => {
    console.log('Test data:', snapshot.val());
});

// Add this after your Firebase initialization
function createTestCall() {
    const db = firebase.database();
    const callsRef = db.ref('nurse_calls');
    
    callsRef.push({
        room_number: "101",
        bed_number: "A",
        patient_id: "TEST123",
        assigned_nurse_id: "{{ auth()->id() }}", // Current logged in nurse
        status: "active",
        created_at: firebase.database.ServerValue.TIMESTAMP
    })
    .then(() => {
        console.log('Test call created successfully');
    })
    .catch((error) => {
        console.error('Error creating test call:', error);
    });
}

// Listen for all nurse calls
const callsRef = db.ref('nurse_calls');
callsRef.on('value', (snapshot) => {
    const calls = snapshot.val();
    console.log('All nurse calls:', calls);
});
    </script>
</head>
<body>
    <!-- Add this simple audio element at the top of body -->
    <audio id="callAlert" preload="auto">
        <source src="{{ asset('audios/nurse_calling_button_sound.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <a href="{{ route('nurseDashboard') }}" class="text-decoration-none text-white">
                <img src="{{ asset('images/logo.png') }}" alt="SUC Hospital" class="img-fluid mb-2">
                <h6 class="mb-0">SUC Hospital</h6>
            </a>
        </div>

        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a href="{{ route('nurseDashboard') }}" class="nav-link {{ request()->routeIs('nurseDashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('nurse.schedule') }}" class="nav-link {{ request()->routeIs('nurse.schedule*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-week"></i>
                    <span>My Schedule</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('nurse.patients') }}" class="nav-link {{ request()->routeIs('nurse.patients*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bed-pulse"></i>
                    <span>My Patients</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('nurse.tasksList') }}" class="nav-link {{ request()->routeIs('nurse.tasksList') ? 'active' : '' }}">
                    <i class="bi bi-list-check"></i>
                    <span>Tasks List</span>
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
                    <small class="text-muted-light">Ward Nurse</small>
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
            <button class="btn" 
                    onclick="window.history.back()" 
                    title="Go back"
                    aria-label="Go back">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                <span class="visually-hidden">Go back</span>
            </button>
            <a href="{{ route('nurseDashboard') }}" class="text-decoration-none text-black">
                <img src="{{ asset('images/logo.png') }}" alt="SUC Hospital" class="img-fluid ms-3 rounded-circle" style="width: 5%; height: 5%; border-black">
                &nbsp;&nbsp;SUC Hospital        
            </a>
        </div>

        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('nurseDashboard') }}" class="btn btn-primary">
                <i class="bi bi-house"></i>
                <span>Home</span>
            </a>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="{{ auth()->user()->profile_picture ? asset(auth()->user()->profile_picture) : asset('images/profile.png') }}"
                         alt="Profile" 
                         class="profile-img">
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('nurse.manageProfile') }}">
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
    <script>
        function confirmLogout(event) {
            event.preventDefault();
            if (confirm("Are you sure you want to log out?")) {
                document.getElementById('logout-form').submit();
            }
        }
    </script>
    <script>
        // Debug log
        console.log('Starting Firebase initialization...');

        // Initialize Firebase immediately
        const firebaseConfig = {
            apiKey: "AIzaSyAiElkmNSl0K-N0Rz4kuqKAXrr6Eg7oo64",
            authDomain: "fyptestv2-37c45.firebaseapp.com",
            databaseURL: "https://fyptestv2-37c45-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "fyptestv2-37c45",
            storageBucket: "fyptestv2-37c45.firebasestorage.app",
            messagingSenderId: "500961952253",
            appId: "1:500961952253:web:a846193490974d3667d994"
        };

        // Initialize Firebase with error handling
        try {
            firebase.initializeApp(firebaseConfig);
            console.log('Firebase initialized successfully');
            
            // Test database reference
            const db = firebase.database();
            console.log('Database reference created');
            
            // Test immediate connection
            db.ref('.info/connected').on('value', (snapshot) => {
                console.log('Initial connection check:', snapshot.val() ? 'Connected' : 'Waiting...');
            });
            
        } catch (error) {
            console.error('Firebase initialization error:', error);
        }
    </script>

    <!-- Floating Calls Container -->
    <div id="floating-calls" class="floating-calls">
        <div class="calls-wrapper">
            <div id="active-calls" class="active-calls">
                <!-- Calls will be populated here dynamically -->
            </div>
            <button type="button" class="btn btn-danger calls-toggle position-relative" id="calls-toggle" 
                    data-bs-toggle="modal" data-bs-target="#callDetailsModal">
                <i class="fas fa-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                    <span id="calls-count">0</span>
                    <span class="visually-hidden">unread messages</span>
                </span>
            </button>
        </div>
    </div>

    <!-- Call Details Modal -->
    <div class="modal fade" id="callDetailsModal" tabindex="-1" aria-labelledby="callDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="callDetailsModalLabel">
                        <i class="fas fa-bell me-2"></i>Active Calls
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="callDetailsContent" class="calls-list">
                        <!-- Call details will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update the test buttons -->
    <!--
    <div style="position: fixed; bottom: 100px; right: 20px; z-index: 9999;">
        <button class="btn btn-warning test-beep-btn">
            <i class="fas fa-volume-up me-2"></i>Test Beep
        </button>
        <button class="btn btn-info test-sound-btn">
            <i class="fas fa-music me-2"></i>Test MP3
        </button>
    </div>
    -->

    <!-- Move these functions outside of DOMContentLoaded -->
    <script>
        let audioInterval; // Global variable to store the interval

        function startRepeatingSound() {
            const audio = document.getElementById('callAlert');
            if (audio) {
                // Clear any existing interval
                clearInterval(audioInterval);
                
                // Function to play sound
                const playAudio = () => {
                    audio.currentTime = 0;
                    audio.play().catch(error => console.error('Audio playback failed:', error));
                };

                // Play immediately
                playAudio();

                // Add ended event listener to replay after each completion
                audio.addEventListener('ended', () => {
                    setTimeout(playAudio, 1000); // Wait 1 second before replaying
                });
            }
        }

        function stopRepeatingSound() {
            const audio = document.getElementById('callAlert');
            if (audio) {
                // Remove the ended event listener
                audio.removeEventListener('ended', () => {});
                audio.pause();
                audio.currentTime = 0;
            }
        }

        // Global function to handle attending a call
        function attendCall(callId) {
            const db = firebase.database();
            db.ref('nurse_calls/' + callId).update({
                call_status: false,
                attended_at: firebase.database.ServerValue.TIMESTAMP,
                attended_by: "{{ auth()->id() }}"
            }).then(() => {
                // Stop the repeating sound
                stopRepeatingSound();
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('callDetailsModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Call Attended',
                    text: 'You have successfully attended to this call',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch((error) => {
                console.error('Error attending call:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to attend call. Please try again.'
                });
            });
        }

        // Global function to navigate to patient
        function navigateToPatient(callId) {
            const db = firebase.database();
            db.ref('nurse_calls/' + callId).once('value', (snapshot) => {
                const call = snapshot.val();
                if (call && call.locations) {
                    const patientLocation = `${call.locations.latitude},${call.locations.longitude}`;
                    const nurseLocation = '1.534776633677136,103.68248968623259'; // Fixed nurse location
                    const navigationUrl = `https://www.google.com/maps/dir/${nurseLocation}/${patientLocation}`;
                    window.open(navigationUrl, '_blank');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Navigation Error',
                        text: 'Patient location not available'
                    });
                }
            });
        }

        // Global function to calculate distance
        function calculateDistance(call) {
            if (!call.locations) return 'N/A';

            const NURSE_LOCATION = {
                lat: 1.534776633677136,
                lng: 103.68248968623259
            };

            const patientLocation = {
                lat: parseFloat(call.locations.latitude),
                lng: parseFloat(call.locations.longitude)
            };

            const R = 6371e3; // Earth's radius in meters
            const φ1 = NURSE_LOCATION.lat * Math.PI/180;
            const φ2 = patientLocation.lat * Math.PI/180;
            const Δφ = (patientLocation.lat - NURSE_LOCATION.lat) * Math.PI/180;
            const Δλ = (patientLocation.lng - NURSE_LOCATION.lng) * Math.PI/180;

            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                    Math.cos(φ1) * Math.cos(φ2) *
                    Math.sin(Δλ/2) * Math.sin(Δλ/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const distance = R * c;

            return Math.round(distance);
        }

        // Keep your DOMContentLoaded event listener, but remove these functions from inside it
        document.addEventListener('DOMContentLoaded', function() {
            const db = firebase.database();
            const callsRef = db.ref('nurse_calls');
            const currentNurseId = "{{ auth()->id() }}";
            let previousCallCount = 0;

            // Simple function to play sound
            function playSound() {
                const audio = document.getElementById('callAlert');
                if (audio) {
                    audio.currentTime = 0; // Reset to start
                    audio.volume = 1.0; // Set volume to maximum
                    
                    // Play with promise handling
                    const playPromise = audio.play();
                    if (playPromise !== undefined) {
                        playPromise
                            .then(() => {
                                console.log('Audio played successfully');
                            })
                            .catch(error => {
                                console.error('Audio playback failed:', error);
                            });
                    }
                }
            }

            // Test beep function
            function testBeep() {
                const context = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = context.createOscillator();
                const gainNode = context.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(context.destination);

                oscillator.type = 'sine';
                oscillator.frequency.value = 440;
                gainNode.gain.value = 0.5;

                oscillator.start();
                setTimeout(() => oscillator.stop(), 200);
            }

            // Add click event listeners to test buttons
            document.querySelectorAll('.test-sound-btn').forEach(button => {
                button.addEventListener('click', () => {
                    console.log('Test button clicked');
                    playSound();
                });
            });

            document.querySelectorAll('.test-beep-btn').forEach(button => {
                button.addEventListener('click', () => {
                    console.log('Beep button clicked');
                    testBeep();
                });
            });

            // Listen for active calls
            callsRef.on('value', (snapshot) => {
                const calls = snapshot.val();
                let activeCalls = [];
                let callsHtml = '';
                
                if (calls) {
                    Object.entries(calls).forEach(([callId, call]) => {
                        if (call.call_status === true && String(call.assigned_nurse_id) === currentNurseId) {
                            activeCalls.push(call);
                            
                            callsHtml += `
                                <div class="call-item animate__animated animate__fadeIn">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">Room ${call.room_number} - Bed ${call.bed_number}</h6>
                                            <p class="mb-1 text-muted small">Patient: ${call.patient_name || 'Unknown'}</p>
                                            <p class="mb-0 text-muted small">Distance: ${calculateDistance(call)} meters</p>
                                        </div>
                                        <div class="d-flex flex-column gap-2">
                                            <button class="btn btn-sm btn-success" onclick="attendCall('${callId}')">
                                                <i class="fas fa-check me-1"></i>Attend
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="navigateToPatient('${callId}')">
                                                <i class="fas fa-directions me-1"></i>Navigate
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    });
                }

                // Start or stop repeating sound based on active calls
                if (activeCalls.length > 0) {
                    startRepeatingSound();
                } else {
                    stopRepeatingSound();
                }

                previousCallCount = activeCalls.length;

                // Update the calls count
                document.getElementById('calls-count').textContent = activeCalls.length;
                
                // Update the modal content
                document.getElementById('callDetailsContent').innerHTML = 
                    activeCalls.length ? callsHtml : '<p class="text-center text-muted my-3">No active calls</p>';
                
                // Show/hide the floating button
                document.getElementById('floating-calls').style.display = 
                    activeCalls.length ? 'block' : 'none';
            });
        });
    </script>

    @stack('scripts')
</body>
</html>