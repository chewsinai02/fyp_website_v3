@extends('nurse.layout')
@section('title', 'Ward Nurse Dashboard')

@section('content')
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
                                {{ \App\Models\Bed::whereIn('room_id', 
                                    \App\Models\NurseSchedule::where('nurse_id', auth()->id())
                                        ->pluck('room_id'))
                                    ->where('status', 'occupied')
                                    ->count() 
                                }}
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

    <!-- Active Patient Calls Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0">Active Patient Calls</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="activeCalls">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Room</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Will be populated by Firebase -->
                    </tbody>
                </table>
            </div>
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
                            $assignedRooms = \App\Models\NurseSchedule::where('nurse_id', auth()->id())
                                ->pluck('room_id');
                            
                            $occupiedBeds = \App\Models\Bed::whereIn('room_id', $assignedRooms)
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
</style>

@push('scripts')
<script>
// Firebase Configuration
const firebaseConfig = {
    // Your Firebase config here
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const database = firebase.database();

// Listen for patient calls
function listenToPatientCalls() {
    const callsRef = database.ref('nurse_calls');
    const activeCallsTable = document.querySelector('#activeCalls tbody');
    const activeCallsCount = document.getElementById('activeCallsCount');
    
    callsRef.on('value', (snapshot) => {
        const calls = snapshot.val();
        let html = '';
        let count = 0;
        
        for (let callId in calls) {
            const call = calls[callId];
            if (call.status === 'pending') {
                count++;
                html += `
                    <tr>
                        <td>${call.patient_name}</td>
                        <td>Room ${call.room_number}</td>
                        <td>${new Date(call.created_at).toLocaleTimeString()}</td>
                        <td>
                            <span class="badge bg-danger-subtle text-danger">
                                Pending
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="attendCall('${callId}')">
                                Attend
                            </button>
                        </td>
                    </tr>
                `;
            }
        }
        
        activeCallsTable.innerHTML = html;
        activeCallsCount.textContent = count;
    });
}

function attendCall(callId) {
    const callRef = database.ref(`nurse_calls/${callId}`);
    callRef.update({
        status: 'attended',
        nurse_id: '{{ auth()->id() }}',
        attended_at: new Date().toISOString()
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    listenToPatientCalls();
});
</script>
@endpush

@endsection