@extends('doctor.layout')
@section('title', 'Doctor Dashboard')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-gradient fs-1 mb-2">Doctor Dashboard</h2>
            <p class="text-muted">Welcome back, {{ auth()->user()->name }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Appointments Card -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Appointments</h6>
                            <h3 class="mb-0">{{ $activeAppointmentsCount }}</h3>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded">
                            <i class="bi bi-calendar-check fa-lg text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Today's Appointments</h6>
                            <h3 class="mb-0">
                                {{ $appointments->where('appointment_date', today())->count() }}
                            </h3>
                        </div>
                        <div class="bg-success-subtle p-3 rounded">
                            <i class="bi bi-calendar2-day fa-lg text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Reports -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Pending Reports</h6>
                            <h3 class="mb-0">
                                {{ $appointments->where('status', 'active')->count() }}
                            </h3>
                        </div>
                        <div class="bg-warning-subtle p-3 rounded">
                            <i class="bi bi-file-earmark-text fa-lg text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Unread Messages</h6>
                            <h3 class="mb-0" id="unreadCount">
                                {{ \App\Models\Message::where('receiver_id', auth()->id())
                                    ->where('is_read', false)
                                    ->count() }}
                            </h3>
                        </div>
                        <div class="bg-info-subtle p-3 rounded">
                            <a href="{{ route('doctorMessage') }}" class="text-decoration-none">
                                <i class="bi bi-chat-dots fa-lg text-info"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Today's Schedule</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $todayAppointments = $appointments
                                ->where('appointment_date', today())
                                ->sortBy('appointment_time');
                        @endphp

                        @forelse($todayAppointments as $appointment)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $appointment->patient->profile_picture ? asset($appointment->patient->profile_picture) : asset('images/profile.png') }}" 
                                             class="rounded-circle me-2" 
                                             width="32" 
                                             height="32">
                                        {{ $appointment->patient->name }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $appointment->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('doctor.chat', $appointment->patient->id) }}" 
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-chat"></i>
                                    </a>
                                    <a href="{{ route('doctor.editPatientDetails', $appointment->patient->id) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-calendar-x me-2"></i>
                                        No appointments scheduled for today
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Activity</h5>
            <a href="{{ route('doctorAppointment.index') }}" class="btn btn-sm btn-primary">
                View All
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments->take(5) as $appointment)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $appointment->patient->profile_picture ? asset($appointment->patient->profile_picture) : asset('images/profile.png') }}" 
                                             class="rounded-circle me-2" 
                                             width="32" 
                                             height="32">
                                        {{ $appointment->patient->name }}
                                    </div>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                    <br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $appointment->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('doctor.addReport', $appointment->patient->id) }}" 
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-plus-lg"></i>
                                    </a>
                                    <a href="{{ route('doctor.reportList', $appointment->patient->id) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
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
    border-radius: 12px;
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

<script>
// Function to update unread message count
function updateUnreadCount() {
    fetch('/doctor/messages/unread-count')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const unreadCountElement = document.getElementById('unreadCount');
            if (unreadCountElement) {
                unreadCountElement.textContent = data.count;
            }
        })
        .catch(error => {
            console.error('Error fetching unread count:', error);
        });
}

// Update count when page loads and every 30 seconds
document.addEventListener('DOMContentLoaded', function() {
    // Initial count is already set from the server-side
    
    // Update count every 30 seconds
    setInterval(updateUnreadCount, 30000);
    
    // Add click handler for the messages icon
    const messagesIcon = document.querySelector('.bi-chat-dots');
    if (messagesIcon) {
        messagesIcon.closest('a').addEventListener('click', function(e) {
            // Don't prevent default - let it navigate to messages page
            updateUnreadCount(); // Update count when clicking messages
        });
    }
});
</script>
@endsection 