@extends('nurse.layout')
@section('title', 'Ward Nurse Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div id="connectionStatus" class="alert alert-info">
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Checking Firebase connection...
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-gradient fs-1 mb-2">Nurse Dashboard</h2>
            <p class="text-muted">Welcome back, {{ auth()->user()->name }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Assigned Patients</h6>
                            <h3 class="mb-0">
                                {{ $patients->count() }}
                            </h3>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded">
                            <i class="fa-solid fa-hospital-user fa-lg text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Active Calls</h6>
                            <h3 class="mb-0" id="activeCallsCount">0</h3>
                        </div>
                        <div class="bg-danger-subtle p-3 rounded">
                            <i class="bi bi-bell fa-lg text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Today's Tasks</h6>
                            <h3 class="mb-0 d-flex align-items-center gap-2">
                                {{ $taskCount }}
                                <small class="text-muted fs-6">
                                    ({{ $completedTaskCount }} Completed)
                                </small>
                            </h3>
                        </div>
                        <div class="bg-success-subtle p-3 rounded">
                            <i class="bi bi-list-check fa-lg text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Shift Status</h6>
                            <h3 class="mb-0" id="shiftStatus">On Duty</h3>
                        </div>
                        <div class="bg-info-subtle p-3 rounded">
                            <i class="bi bi-clock-history fa-lg text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Active Patient Calls</h5>
            <span id="activeCallCount" class="badge bg-danger">0 Active Calls</span>
        </div>
        <div class="card-body">
            <div id="map" style="height: 400px;" class="mb-3">
                <!-- Map will be initialized here -->
            </div>
            <div id="callList" class="mt-3">
                <!-- Active calls will be listed here -->
            </div>
        </div>
    </div>

    <!-- Floating Calls Container -->
    <div id="floating-calls" class="floating-calls d-none">
        <div class="calls-wrapper">
            <div id="active-calls" class="active-calls">
                <!-- Calls will be populated here dynamically -->
            </div>
            <button type="button" class="btn btn-danger calls-toggle" id="calls-toggle">
                <i class="fas fa-bell"></i>
                <span class="calls-count">0</span>
            </button>
        </div>
    </div>

    <!-- Assigned Patients Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0">My Assigned Patients</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Room</th>
                            <th>Bed</th>
                            <th>Condition</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Get assigned rooms for today with proper date filtering
                            $assignedRoomIds = \App\Models\NurseSchedule::where('nurse_id', auth()->id())
                                ->whereDate('date', today())
                                ->pluck('room_id');
                            
                            $occupiedBeds = \App\Models\Bed::whereIn('room_id', $assignedRoomIds)
                                ->where('status', 'occupied')
                                ->with(['patient', 'room'])
                                ->get();
                        @endphp

                        @foreach($occupiedBeds as $bed)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('images/profile.png') }}" 
                                         class="rounded-circle me-2" 
                                         width="32" 
                                         height="32">
                                    {{ $bed->patient->name }}
                                </div>
                            </td>
                            <td>Room {{ $bed->room->room_number }}</td>
                            <td>Bed {{ $bed->bed_number }}</td>
                            <td>
                                @if($bed->condition)
                                    <span class="badge bg-{{ $bed->condition_color }}">
                                        {{ $bed->condition }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Not Set
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($bed->patient && ($bed->latest_update ?? null))
                                    {{ $bed->latest_update->diffForHumans() }}
                                @else
                                    <span class="text-muted">No updates</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('nurse.patient.view', ['user' => $bed->patient_id]) }}" 
                                   class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add this temporarily to see your ID -->
    <div class="alert alert-info">
        Your Nurse ID: {{ auth()->id() }}
    </div>
</div>

<style>
/* Modern styling */
.text-gradient {
    background: linear-gradient(45deg, #2C3E50, #3498DB);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
}

.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1);
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1);
}

.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1);
}

.bg-info-subtle {
    background-color: rgba(13, 202, 240, 0.1);
}

.table th {
    font-weight: 600;
    color: #1e293b;
    border-top: none;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.badge {
    font-weight: 500;
    padding: 0.5em 1em;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-primary {
    background-color: #0d6efd !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
}

/* Hover effects */
.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.table tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.floating-calls {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1050;
}

.calls-wrapper {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.active-calls {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    margin-bottom: 10px;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    width: 300px;
}

.active-calls.show {
    max-height: 400px;
    overflow-y: auto;
    padding: 10px;
}

.calls-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    position: relative;
}

.calls-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: white;
    color: #dc3545;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    border: 2px solid #dc3545;
}
</style>

<!-- Load Google Maps JavaScript API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUWg6Bqgdcb7Qx3vQ7R1vyYL_PCjlJ2ew&callback=initMap" async defer></script>

<script>
console.log('Dashboard script starting...');

// Function to update status
function updateConnectionStatus(status, message, type = 'info') {
    const connectionStatus = document.getElementById('connectionStatus');
    if (!connectionStatus) {
        console.error('Connection status element not found');
        return;
    }
    
    console.log('Updating status:', status, message, type);
    
    connectionStatus.className = `alert alert-${type}`;
    connectionStatus.innerHTML = message;
}

// Check Firebase immediately
function checkConnection() {
    console.log('Checking Firebase connection...');
    
    if (typeof firebase === 'undefined') {
        updateConnectionStatus(
            false, 
            '<i class="fas fa-times-circle"></i> Firebase not loaded', 
            'danger'
        );
        return;
    }

    try {
        const db = firebase.database();
        
        // Test write to verify connection
        db.ref('connection_test').set({
            timestamp: Date.now(),
            test: 'Connection Test'
        })
        .then(() => {
            console.log('Write test successful');
            updateConnectionStatus(
                true,
                '<i class="fas fa-check-circle"></i> Connected to Firebase!',
                'success'
            );
        })
        .catch(error => {
            console.error('Write test failed:', error);
            updateConnectionStatus(
                false,
                '<i class="fas fa-exclamation-circle"></i> Connection test failed: ' + error.message,
                'danger'
            );
        });

    } catch (error) {
        console.error('Firebase check error:', error);
        updateConnectionStatus(
            false,
            '<i class="fas fa-times-circle"></i> Firebase error: ' + error.message,
            'danger'
        );
    }
}

// Run checks when document is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Initial check
    checkConnection();
    
    // Recheck every 5 seconds until connected
    const interval = setInterval(() => {
        if (document.querySelector('.alert-success')) {
            clearInterval(interval);
        } else {
            checkConnection();
        }
    }, 5000);
});

function listenForCalls() {
    const db = firebase.database();
    const callsRef = db.ref('nurse_calls');
    const floatingCalls = document.getElementById('floating-calls');
    const activeCalls = document.getElementById('active-calls');
    const callsToggle = document.getElementById('calls-toggle');
    const callsCount = document.querySelector('.calls-count');

    console.log('Starting call listener...');

    callsRef.on('value', (snapshot) => {
        const calls = snapshot.val();
        console.log('Received calls:', calls);

        if (!calls) {
            floatingCalls.classList.add('d-none');
            return;
        }

        const pendingCalls = Object.entries(calls).filter(([_, call]) => 
            call.call_status === true && 
            call.assigned_nurse_id === {{ auth()->id() }}
        );

        console.log('Pending calls:', pendingCalls);

        if (pendingCalls.length === 0) {
            floatingCalls.classList.add('d-none');
            return;
        }

        floatingCalls.classList.remove('d-none');
        callsCount.textContent = pendingCalls.length;

        activeCalls.innerHTML = pendingCalls.map(([id, call]) => `
            <div class="call-item p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">Room ${call.room_number}</h6>
                        <small class="text-muted">Bed ${call.bed_number}</small>
                        <div class="text-muted">${call.patient_name}</div>
                    </div>
                    <span class="badge bg-danger">New Call</span>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-2">
                    <button class="btn btn-sm btn-success attend-call" data-call-id="${id}">
                        Attend
                    </button>
                </div>
            </div>
        `).join('');
    });

    // Toggle calls list
    callsToggle.addEventListener('click', () => {
        activeCalls.classList.toggle('show');
    });

    // Handle attend button clicks
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('attend-call')) {
            const callId = e.target.dataset.callId;
            callsRef.child(callId).update({
                call_status: false,
                attended_at: firebase.database.ServerValue.TIMESTAMP,
                attended_by: {{ auth()->id() }}
            });
        }
    });
}

// Start listening for calls after connection is confirmed
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.alert-success')) {
        listenForCalls();
    } else {
        const checkInterval = setInterval(() => {
            if (document.querySelector('.alert-success')) {
                listenForCalls();
                clearInterval(checkInterval);
            }
        }, 1000);
    }
});

let map, infoWindow;
let markers = new Map();

// SUC Center coordinates
const SUC_CENTER = {
    lat: 1.5347778,  // Your current location
    lng: 103.6825
};

// Patient location coordinates
const PATIENT_LOCATION = {
    lat: 1.534073454318247,  // Updated patient location
    lng: 103.68301029425147
};

function initMap() {
    // Initialize map centered between your location and patient location
    const centerPoint = {
        lat: (SUC_CENTER.lat + PATIENT_LOCATION.lat) / 2,
        lng: (SUC_CENTER.lng + PATIENT_LOCATION.lng) / 2
    };

    map = new google.maps.Map(document.getElementById("map"), {
        center: centerPoint,
        zoom: 19, // High zoom for detailed view
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
            position: google.maps.ControlPosition.TOP_RIGHT
        }
    });
    
    infoWindow = new google.maps.InfoWindow();

    // Add current location button
    const locationButton = document.createElement("button");
    locationButton.textContent = "My Location";
    locationButton.classList.add("custom-map-control-button");
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);

    // Create a marker for your fixed location
    const nurseMarker = new google.maps.Marker({
        position: SUC_CENTER,
        map: map,
        title: "Your Location",
        icon: {
            url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
        }
    });

    // Listen for active calls from Firebase
    const db = firebase.database();
    db.ref('nurse_calls').orderByChild('call_status').equalTo(true).on('value', (snapshot) => {
        updateMarkers(snapshot.val());
    });

    // Handle location button click
    locationButton.addEventListener("click", () => {
        map.setCenter(SUC_CENTER);
        map.setZoom(19);
        infoWindow.setContent("You are here");
        infoWindow.open(map, nurseMarker);
        
        // Update nurse location in Firebase
        updateNurseLocation(SUC_CENTER);
    });
}

function updateMarkers(calls) {
    // Clear existing markers
    markers.forEach(marker => marker.setMap(null));
    markers.clear();
    
    let activeCount = 0;
    let activeCallsHtml = '';

    if (calls) {
        Object.entries(calls).forEach(([callId, call]) => {
            if (call.call_status === true) {
                activeCount++;
                
                // Use the correct patient location
                const position = {
                    lat: PATIENT_LOCATION.lat,
                    lng: PATIENT_LOCATION.lng
                };

                // Create marker for patient call
                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: `Room ${call.room_number}`,
                    icon: {
                        url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                    }
                });

                marker.addListener('click', () => {
                    infoWindow.setContent(`
                        <div style="padding: 10px;">
                            <h6>Room ${call.room_number}</h6>
                            <p>Bed: ${call.bed_number}<br>
                            Patient: ${call.patient_name}</p>
                            <button onclick="attendCall('${callId}')" 
                                    class="btn btn-sm btn-success">
                                Attend Call
                            </button>
                        </div>
                    `);
                    infoWindow.open(map, marker);
                });

                markers.set(callId, marker);

                // Create call card HTML
                const callCard = `
                    <div class="alert alert-danger mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Room ${call.room_number} - Bed ${call.bed_number}</h6>
                                <div class="small">Patient: ${call.patient_name}</div>
                                <div class="small text-muted">
                                    Distance: ${calculateDistance(SUC_CENTER, position)} meters
                                </div>
                            </div>
                            <button class="btn btn-sm btn-success attend-call" 
                                    onclick="attendCall('${callId}')">
                                Attend
                            </button>
                        </div>
                    </div>
                `;
                
                activeCallsHtml += callCard;
            }
        });
    }

    // Update UI
    document.getElementById('activeCallCount').textContent = 
        `${activeCount} Active Call${activeCount !== 1 ? 's' : ''}`;
    document.getElementById('callList').innerHTML = 
        activeCallsHtml || '<div class="alert alert-info">No active calls</div>';

    // Update floating container
    const floatingCallsDiv = document.getElementById('floating-calls');
    const floatingCallsCount = document.querySelector('.calls-count');
    if (activeCount > 0) {
        floatingCallsDiv.classList.remove('d-none');
        floatingCallsCount.textContent = activeCount;
        document.getElementById('active-calls').innerHTML = activeCallsHtml;
    } else {
        floatingCallsDiv.classList.add('d-none');
    }
}

function calculateDistance(point1, point2) {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = point1.lat * Math.PI/180;
    const φ2 = point2.lat * Math.PI/180;
    const Δφ = (point2.lat-point1.lat) * Math.PI/180;
    const Δλ = (point2.lng-point1.lng) * Math.PI/180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return Math.round(R * c); // Distance in meters, rounded
}

function updateNurseLocation(position) {
    const db = firebase.database();
    db.ref(`nurse_locations/32`).update({
        latitude: position.lat,
        longitude: position.lng,
        timestamp: firebase.database.ServerValue.TIMESTAMP
    });
}

function attendCall(callId) {
    const db = firebase.database();
    db.ref(`nurse_calls/${callId}`).update({
        call_status: false,
        attended_at: firebase.database.ServerValue.TIMESTAMP,
        attended_by: '32'
    }).then(() => {
        const marker = markers.get(callId);
        if (marker) {
            marker.setMap(null);
            markers.delete(callId);
        }
        infoWindow.close();
    });
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(
        browserHasGeolocation
            ? "Error: The Geolocation service failed."
            : "Error: Your browser doesn't support geolocation."
    );
    infoWindow.open(map);
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', initMap);

// Add toggle functionality for floating calls
document.getElementById('calls-toggle').addEventListener('click', () => {
    document.getElementById('active-calls').classList.toggle('show');
});
</script>

@endsection