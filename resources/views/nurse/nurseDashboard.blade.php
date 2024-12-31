@extends('nurse.layout')
@section('title', 'Ward Nurse Dashboard')

@section('content')
@php
function getBedConditionColor($condition) {
    return match($condition) {
        'Critical' => 'danger',
        'Serious' => 'warning',
        'Fair' => 'info',
        'Good' => 'success',
        'Stable' => 'primary',
        default => 'secondary'
    };
}
@endphp

<!--
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
-->

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
                            <h6 class="text-muted mb-2">Responsible Patients</h6>
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
                            <h3 class="mb-0" id="shiftStatus">
                                {{ auth()->user()->getTodayScheduleStatus() }}
                            </h3>
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

    <!-- Responsible Patients Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0">Responsible Patients</h5>
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
                            $today = now()->toDateString();
                            $currentTime = now()->format('H:i:s');
                            $nurseId = auth()->id();
                            
                            // Determine current shift
                            $currentShift = match(true) {
                                $currentTime >= '07:00:00' && $currentTime < '15:00:00' => 'morning',
                                $currentTime >= '15:00:00' && $currentTime < '23:00:00' => 'afternoon',
                                default => 'night'
                            };
                            
                            // Get assigned rooms for today with proper shift
                            $schedules = \App\Models\NurseSchedule::where('nurse_id', $nurseId)
                                ->whereDate('date', $today)
                                ->where('shift', $currentShift)
                                ->get();
                            
                            $assignedRoomIds = $schedules->pluck('room_id');
                            
                            $patients = \App\Models\Patient::patients()
                                ->whereHas('bed', function($query) use ($assignedRoomIds) {
                                    $query->whereIn('room_id', $assignedRoomIds)
                                        ->where('status', 'occupied')
                                        ->whereNull('deleted_at')
                                        ->whereHas('room');
                                })
                                ->with(['bed.room'])
                                ->get();
                            
                            // Debug information
                            $debug = [
                                'nurse_id' => $nurseId,
                                'today' => $today,
                                'current_time' => $currentTime,
                                'current_shift' => $currentShift,
                                'schedule_count' => $schedules->count(),
                                'assigned_rooms' => $assignedRoomIds->toArray(),
                                'patient_count' => $patients->count()
                            ];
                            
                            // Add these to debug information
                            $debug['query_sql'] = \DB::getQueryLog();
                            \DB::enableQueryLog(); // Enable query logging temporarily
                        @endphp

                        @forelse($patients as $patient)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('images/profile.png') }}" 
                                         class="rounded-circle me-2" 
                                         width="32" 
                                         height="32">
                                    {{ $patient->name }}
                                </div>
                            </td>
                            <td>
                                @if($patient->bed && $patient->bed->room)
                                    Room {{ $patient->bed->room->room_number }}
                                @else
                                    <span class="text-danger">No Room Assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($patient->bed)
                                    Bed {{ $patient->bed->bed_number }}
                                @else
                                    <span class="text-danger">No Bed Assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($patient->bed && $patient->bed->condition)
                                    <span class="badge bg-{{ getBedConditionColor($patient->bed->condition) }}-subtle 
                                         text-{{ getBedConditionColor($patient->bed->condition) }}">
                                        {{ $patient->bed->condition }}
                                    </span>
                                    @if($patient->bed->notes)
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-info-circle"></i> {{ $patient->bed->notes }}
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Not Set
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($patient->updated_at)
                                    {{ $patient->updated_at->diffForHumans() }}
                                @else
                                    <span class="text-muted">No updates</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('nurse.patient.view', ['user' => $patient->id]) }}" 
                                   class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No patients assigned for today
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add this debug section temporarily
    @if(config('app.debug'))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Debug Information</h5>
            </div>
            <div class="card-body">
                <pre>{{ json_encode($debug, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif
     -->

    <!-- Add this temporarily to see your ID -->
    <!--
    <div class="alert alert-info">
        Your Nurse ID: {{ auth()->id() }}
    </div>
    -->
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

/* Condition badge styles */
.badge.bg-danger-subtle {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.badge.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.badge.bg-info-subtle {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

.badge.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.badge.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.badge.text-danger {
    color: #dc3545 !important;
}

.badge.text-warning {
    color: #ffc107 !important;
}

.badge.text-info {
    color: #0dcaf0 !important;
}

.badge.text-success {
    color: #198754 !important;
}

.badge.text-primary {
    color: #0d6efd !important;
}

/* Notes styling */
.small.text-muted {
    font-size: 0.75rem;
    line-height: 1.2;
    margin-top: 0.25rem;
}

.small.text-muted i {
    font-size: 0.7rem;
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

// Firebase nurse call structure
const createNurseCall =(roomNumber, bedNumber, patientId, patientName) => {
    const db = firebase.database();
    const callsRef = db.ref('nurse_calls');
    
    return callsRef.push({
        room_number: roomNumber,
        bed_number: bedNumber,
        patient_id: patientId,
        patient_name: patientName,
        assigned_nurse_id: "{{ auth()->id() }}", // Current nurse ID
        call_status: true,
        created_at: firebase.database.ServerValue.TIMESTAMP,
        locations: {
            latitude: "1.534081527989047",  // Default location for room 101
            longitude: "103.68303193559673"
        }
    });
};

// Listen for nurse calls
function listenForCalls() {
    const db = firebase.database();
    const callsRef = db.ref('nurse_calls');
    const currentNurseId = String("{{ auth()->id() }}");
    
    console.log('Listening for calls - Nurse ID:', currentNurseId);

    callsRef.orderByChild('call_status').equalTo(true).on('value', (snapshot) => {
        const calls = snapshot.val();
        let activeCount = 0;
        let activeCallsHtml = '';

        if (calls) {
            Object.entries(calls).forEach(([callId, call]) => {
                if (call.call_status === true && 
                    String(call.assigned_nurse_id) === currentNurseId) {
                    
                    activeCount++;
                    
                    // Calculate distance using the global function from layout.blade.php
                    const distance = calculateDistance(call);

                    const callCard = `
                        <div class="alert alert-danger mb-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Room ${call.room_number} - Bed ${call.bed_number}</h6>
                                    <div class="small">Patient: ${call.patient_name || 'Unknown'}</div>
                                    <p class="small mb-0">Distance: ${distance} meters</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-success" 
                                            onclick="attendCall('${callId}')">
                                        Attend
                                    </button>
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="navigateToPatient('${callId}')"
                                            title="Navigate to patient">
                                        <i class="fas fa-directions"></i> Navigate
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    activeCallsHtml += callCard;
                }
            });
        }

        // Update UI elements
        document.getElementById('activeCallCount').textContent = 
            `${activeCount} Active Call${activeCount !== 1 ? 's' : ''}`;
        document.getElementById('activeCallsCount').textContent = activeCount;
        document.querySelector('.calls-count').textContent = activeCount;
        
        document.getElementById('callList').innerHTML = 
            activeCallsHtml || '<div class="alert alert-info">No active calls</div>';
        
        const floatingCalls = document.getElementById('floating-calls');
        floatingCalls.classList.toggle('d-none', activeCount === 0);
    });
}

function navigateToCall(callId) {
    const db = firebase.database();
    const callRef = db.ref(`nurse_calls/${callId}`);
    
    callRef.once('value', (snapshot) => {
        const call = snapshot.val();
        if (call && call.locations) {
            const patientLocation = {
                lat: parseFloat(call.locations.latitude),
                lng: parseFloat(call.locations.longitude)
            };
            
            // Open Google Maps in new tab
            const navigationUrl = `https://www.google.com/maps/dir/?api=1&destination=${patientLocation.lat},${patientLocation.lng}`;
            window.open(navigationUrl, '_blank');
        } else {
            console.error('No location data for call:', callId);
        }
    });
}

// Handle attending to a call
function attendCall(callId) {
    const db = firebase.database();
    db.ref(`nurse_calls/${callId}`).update({
        call_status: false,
        attended_at: firebase.database.ServerValue.TIMESTAMP,
        attended_by: "{{ auth()->id() }}"
    }).then(() => {
        console.log('Call attended successfully');
        // Remove marker if it exists
        const marker = markers.get(callId);
        if (marker) {
            marker.setMap(null);
            markers.delete(callId);
        }
        // Close any open info windows
        if (infoWindow) {
            infoWindow.close();
        }
    }).catch((error) => {
        console.error('Error attending call:', error);
    });
}

// Test function to create a sample call
function createTestCall() {
    createNurseCall(
        "101",  // Room number
        "A",    // Bed number
        "P123", // Patient ID
        "Test Patient" // Patient name
    ).then(() => {
        console.log('Test call created successfully');
    }).catch((error) => {
        console.error('Error creating test call:', error);
    });
}

let map, infoWindow, nurseMarker;
const markers = new Map();

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

// Fixed nurse location
const NURSE_LOCATION = {
    lat: 1.534776633677136,
    lng: 103.68248968623259
};

// Haversine formula for accurate distance calculation
function calculateDistance(point1, point2) {
    // Convert coordinates from degrees to radians
    const lat1 = point1.lat * Math.PI / 180;
    const lon1 = point1.lng * Math.PI / 180;
    const lat2 = point2.lat * Math.PI / 180;
    const lon2 = point2.lng * Math.PI / 180;

    // Radius of the Earth in meters
    const R = 6371e3;

    // Haversine formula
    const dLat = lat2 - lat1;
    const dLon = lon2 - lon1;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1) * Math.cos(lat2) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;

    // Return distance rounded to nearest meter
    return Math.round(distance);
}

function initMap() {
    // Initialize map
    map = new google.maps.Map(document.getElementById("map"), {
        center: NURSE_LOCATION,
        zoom: 18,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
    infoWindow = new google.maps.InfoWindow();

    // Create fixed nurse marker (blue pin)
    nurseMarker = new google.maps.Marker({
        position: NURSE_LOCATION,
        map: map,
        title: "Nurse Station",
        icon: {
            url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
        }
    });

    // Add info window for nurse marker
    nurseMarker.addListener('click', () => {
        infoWindow.setContent(`
            <div style="padding: 10px;">
                <h6>Nurse Station</h6>
                <p>Nurse: {{ auth()->user()->name }}</p>
            </div>
        `);
        infoWindow.open(map, nurseMarker);
    });

    // Listen for patient calls
    const db = firebase.database();
    const callsRef = db.ref('nurse_calls');
    const currentNurseId = String("{{ auth()->id() }}");

    callsRef.on('value', (snapshot) => {
        markers.forEach(marker => marker.setMap(null));
        markers.clear();

        const calls = snapshot.val();
        if (calls) {
            Object.entries(calls).forEach(([callId, call]) => {
                if (call.call_status === true && 
                    String(call.assigned_nurse_id) === currentNurseId &&
                    call.locations) {
                    
                    const patientLocation = {
                        lat: parseFloat(call.locations.latitude),
                        lng: parseFloat(call.locations.longitude)
                    };
                    
                    const patientMarker = new google.maps.Marker({
                        position: patientLocation,
                        map: map,
                        title: `Patient Call - Room ${call.room_number}`,
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                        },
                        animation: google.maps.Animation.BOUNCE
                    });

                    patientMarker.addListener('click', () => {
                        // Use the global calculateDistance function
                        const distance = calculateDistance(call);
                        infoWindow.setContent(`
                            <div style="padding: 10px;">
                                <h6>Patient Call</h6>
                                <p>Room: ${call.room_number}<br>
                                Bed: ${call.bed_number}<br>
                                Patient: ${call.patient_name || 'Unknown'}<br>
                                Distance: ${distance} meters</p>
                                <div class="d-flex gap-2 mt-2">
                                    <button onclick="attendCall('${callId}')" 
                                            class="btn btn-sm btn-success">
                                        Attend Call
                                    </button>
                                    <a href="https://www.google.com/maps/dir/?api=1&destination=${patientLocation.lat},${patientLocation.lng}"
                                       class="btn btn-sm btn-primary"
                                       target="_blank">
                                        <i class="fas fa-directions"></i> Navigate
                                    </a>
                                </div>
                            </div>
                        `);
                        infoWindow.open(map, patientMarker);
                    });

                    markers.set(callId, patientMarker);
                }
            });
        }
    });
}

function updateNurseLocation(position) {
    const db = firebase.database();
    db.ref(`nurse_locations/32`).update({
        latitude: position.lat,
        longitude: position.lng,
        timestamp: firebase.database.ServerValue.TIMESTAMP
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

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('Document ready, initializing calls listener');
    listenForCalls();
});
</script>

@endsection