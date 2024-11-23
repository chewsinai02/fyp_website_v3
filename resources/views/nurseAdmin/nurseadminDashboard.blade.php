@extends('nurseAdmin.layout')
@section('title', 'Dashboard')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-gradient fs-1 mb-2">Nurse Admin Dashboard</h2>
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
                            <h6 class="text-muted mb-2">Total Nurses</h6>
                            <h3 class="mb-0">{{ \App\Models\User::where('role', 'nurse')->count() }}</h3>
                        </div>
                        <div class="bg-primary-subtle p-3 rounded">
                            <i class="fa-solid fa-user-nurse fa-lg text-primary"></i>
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
                            <h6 class="text-muted mb-2">On Duty Today</h6>
                            <h3 class="mb-0">{{ \App\Models\NurseSchedule::whereDate('date', today())->where('status', 'scheduled')->count() }}</h3>
                        </div>
                        <div class="bg-success-subtle p-3 rounded">
                            <i class="bi bi-calendar-check fa-lg text-success"></i>
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
                            <h6 class="text-muted mb-2">Total Patients</h6>
                            <h3 class="mb-0">{{ \App\Models\Bed::where('status', 'occupied')->count() }}</h3>
                        </div>
                        <div class="bg-success-subtle p-3 rounded">
                            <i class="bi bi-people fa-lg text-success"></i>
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
                            <h6 class="text-muted mb-2">Available Nurses</h6>
                            <h3 class="mb-0">
                                {{ \App\Models\User::where('role', 'nurse')
                                    ->whereDoesntHave('schedules', function($query) {
                                        $query->whereDate('date', today())
                                            ->where('status', 'scheduled');
                                    })->count() }}
                            </h3>
                        </div>
                        <div class="bg-info-subtle p-3 rounded">
                            <i class="bi bi-person-check fa-lg text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Today's Schedule</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Schedule
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nurse</th>
                            <th>Shift</th>
                            <th>Room Assignment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\NurseSchedule::with(['nurse', 'room'])
                            ->whereDate('date', today())
                            ->orderBy('shift')
                            ->get() as $schedule)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $schedule->nurse->profile_picture ? asset($schedule->nurse->profile_picture) : asset('images/profile.png') }}" 
                                         class="rounded-circle me-2" 
                                         width="32" 
                                         height="32">
                                    {{ $schedule->nurse->name }}
                                </div>
                            </td>
                            <td>{{ ucfirst($schedule->shift) }}</td>
                            <td>
                                @if($schedule->room)
                                    Room {{ $schedule->room->room_number }}
                                @else
                                    <span class="text-muted">No room assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $schedule->status_color }}-subtle text-{{ $schedule->status_color }}">
                                    {{ ucfirst($schedule->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('nurseadmin.editSchedule', ['schedule' => $schedule->id]) }}" 
                                   class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteSchedule({{ $schedule->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('nurseAdmin.schedule')

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
function editSchedule(id) {
    // Add a console.log to debug
    console.log('Editing schedule:', id);
    
    fetch(`/nurseadmin/schedule/${id}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Schedule data:', data); // Debug log
        
        // Populate modal fields
        document.getElementById('schedule_id').value = data.id;
        document.getElementById('nurse_id').value = data.nurse_id;
        document.getElementById('room_id').value = data.room_id;
        document.getElementById('date').value = data.date;
        document.getElementById('shift').value = data.shift;
        if (document.getElementById('notes')) {
            document.getElementById('notes').value = data.notes || '';
        }
        
        // Update form attributes
        const form = document.getElementById('scheduleForm');
        form.setAttribute('action', `/nurseadmin/schedule/${id}`);
        
        // Add method field for PUT request
        let methodField = form.querySelector('input[name="_method"]');
        if (!methodField) {
            methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            form.appendChild(methodField);
        }
        methodField.value = 'PUT';
        
        // Update modal title
        document.querySelector('#scheduleModal .modal-title').textContent = 'Edit Schedule';
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
        modal.show();
    })
    .catch(error => {
        console.error('Error:', error); // Debug log
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load schedule details'
        });
    });
}

function deleteSchedule(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This schedule will be deleted permanently!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/nurseadmin/schedules/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Schedule has been deleted.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload(); // Reload the page after deletion
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to delete schedule'
                });
            });
        }
    });
}

// Schedule form validation
document.addEventListener('DOMContentLoaded', function() {
    const scheduleForm = document.getElementById('schedule_form');
    
    scheduleForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            nurse_id: this.querySelector('[name="nurse_id"]').value,
            date: this.querySelector('[name="date"]').value,
            shift: this.querySelector('[name="shift"]').value,
            _token: document.querySelector('meta[name="csrf-token"]').content
        };

        // Check for conflicts first
        fetch('/nurseadmin/check-schedule-conflict', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.conflict) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Schedule Conflict',
                    text: 'This nurse is already scheduled for this shift!',
                    showCancelButton: true,
                    confirmButtonText: 'Schedule Anyway',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            } else {
                this.submit();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to check schedule conflict. Please try again.'
            });
        });
    });
});

// Add this to handle form submission success/error messages
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}'
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}'
    });
@endif

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Real-time schedule updates
function initializeScheduleUpdates() {
    const scheduleTable = document.querySelector('.schedule-table tbody');
    
    // Update schedule status
    setInterval(() => {
        fetch('/nurseAdmin/current-schedules')
            .then(response => response.json())
            .then(schedules => {
                schedules.forEach(schedule => {
                    const row = scheduleTable.querySelector(`tr[data-schedule-id="${schedule.id}"]`);
                    if (row) {
                        const statusBadge = row.querySelector('.status-badge');
                        statusBadge.className = `badge bg-${schedule.status_color}-subtle text-${schedule.status_color} status-badge`;
                        statusBadge.textContent = schedule.status;
                    }
                });
            });
    }, 30000); // Update every 30 seconds
}

// Call this when the page loads
initializeScheduleUpdates();
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scheduleForm = document.getElementById('schedule_form');
    
    scheduleForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            nurse_id: this.querySelector('[name="nurse_id"]').value,
            date: this.querySelector('[name="date"]').value,
            shift: this.querySelector('[name="shift"]').value,
            _token: document.querySelector('meta[name="csrf-token"]').content
        };

        // Check for conflicts first
        fetch('/nurseadmin/check-schedule-conflict', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.conflict) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Schedule Conflict',
                    text: 'This nurse is already scheduled for this shift!',
                    showCancelButton: true,
                    confirmButtonText: 'Schedule Anyway',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            } else {
                this.submit();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to check schedule conflict. Please try again.'
            });
        });
    });
});
</script>
@endpush