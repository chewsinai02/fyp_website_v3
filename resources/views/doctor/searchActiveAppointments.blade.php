@extends('doctor.layout')
@section('title', 'Search Appointments')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient fs-1 mb-2">Search Results</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-search me-2"></i>
                Found {{ $appointments->count() }} active appointment{{ $appointments->count() !== 1 ? 's' : '' }}
            </p>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <form method="GET" action="{{ route('searchActiveAppointments') }}">
                <div class="input-group">
                    <input type="text" 
                           name="queryActiveAppointments" 
                           class="form-control search-input" 
                           placeholder="Search appointments..."
                           value="{{ request('queryActiveAppointments') }}"
                           required>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($appointments->isEmpty())
        <div class="empty-state text-center py-5">
            <i class="bi bi-calendar-x display-1 text-muted mb-4"></i>
            <h4 class="text-muted">No Appointments Found</h4>
            <p class="text-muted-light">Try adjusting your search criteria</p>
        </div>
    @else
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
                                                <small class="text-muted">Patient ID: {{ $appointment->patient->id }}</small>
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
                                        <span class="badge bg-{{ $appointment->status === 'active' ? 'success' : 'secondary' }}-subtle 
                                                     text-{{ $appointment->status === 'active' ? 'success' : 'secondary' }} rounded-pill px-3">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 150px;">
                                            {{ $appointment->notes ?: 'No notes' }}
                                        </span>
                                    </td>
                                    <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button onclick="window.location.href='{{ route('doctor.addReport', $appointment->id) }}'"
                                                class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-lg me-1"></i>
                                        </button>
                                        <button onclick="window.location.href='{{ route('doctor.reportList', $appointment->id) }}'"
                                                class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button onclick="window.location.href='{{ route('doctor.editPatientDetails', $appointment->id) }}'"
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
                                                    onclick="return confirm('Are you sure you want to delete this appointment?')">
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
    @endif
</div>

<style>
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

.btn-primary {
    border-radius: 0 8px 8px 0;
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

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
}

.empty-state {
    color: #64748b;
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
@endsection
