@extends('doctor.layout')
@section('title', 'All Appointments')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient fs-1 mb-2">Appointments History</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-calendar3 me-2"></i>
                Managing all appointments including past and completed sessions
            </p>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <form method="GET" action="{{ route('searchAppointments') }}">
                <div class="input-group">
                    <input type="text" 
                           name="queryAppointments" 
                           class="form-control search-input" 
                           placeholder="Search appointments..."
                           required>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 bg-light">Patient</th>
                            <th class="py-3 bg-light">IC Number</th>
                            <th class="py-3 bg-light">Contact</th>
                            <th class="py-3 bg-light">Schedule</th>
                            <th class="py-3 bg-light">Status</th>
                            <th class="py-3 bg-light">Notes</th>
                            <th class="py-3 text-center bg-light">Report</th>
                            <th class="py-3 text-center bg-light">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                            <tr>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <img src="{{ $appointment->patient->profile_picture ? asset($appointment->patient->profile_picture) : asset('images/profile.png') }}" 
                                                 alt="Profile" 
                                                 class="rounded-circle shadow-sm"
                                                 width="45" height="45">
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $appointment->patient->name }}</h6>
                                            <small class="text-muted"></small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $appointment->patient->ic_number }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-telephone-fill text-primary me-2"></i>
                                        {{ $appointment->patient->contact_number }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</span>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'active' => 'success',
                                            'done' => 'primary',
                                            'pass' => 'danger'
                                        ];
                                        $statusClass = $statusClasses[$appointment->status] ?? 'secondary';
                                    @endphp
                                    <span class="status-badge status-{{ $statusClass }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;" 
                                          data-bs-toggle="tooltip" 
                                          title="{{ $appointment->notes }}">
                                        {{ $appointment->notes ?: 'No notes' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="window.location.href='{{ route('doctor.addReport', $appointment->patient->id) }}'"
                                                class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-lg me-1"></i> Add
                                        </button>
                                        <button onclick="window.location.href='{{ route('doctor.reportList', $appointment->patient->id) }}'"
                                                class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="window.location.href='{{ route('doctor.editPatientDetails', $appointment->patient->id) }}'"
                                                class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button onclick="window.location.href='{{ route('doctorAppointment.show', $appointment->id) }}'"
                                                class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form action="{{ route('doctorAppointment.destroy', $appointment->id) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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

.search-container {
    max-width: 300px;
}

.search-input {
    border-radius: 8px 0 0 8px;
    border: 1px solid #e2e8f0;
    padding: 0.6rem 1rem;
}

.search-input:focus {
    border-color: var(--bs-primary);
    box-shadow: none;
}

.card {
    border-radius: 12px;
    overflow: hidden;
}

.table th {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.875rem;
}

.table td {
    padding: 1rem 0.75rem;
    font-size: 0.875rem;
}

.avatar img {
    object-fit: cover;
    border: 2px solid #fff;
}

/* Status badges */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-block;
}

.status-success {
    background-color: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.status-primary {
    background-color: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.status-warning {
    background-color: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.status-danger {
    background-color: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

/* Action buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.75rem;
    border-radius: 6px;
}

/* Hover effects */
.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.2s;
}

.table tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .search-container {
        width: 100%;
        max-width: none;
        margin-top: 1rem;
    }
}
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
@endsection