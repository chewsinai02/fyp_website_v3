@extends('doctor.layout')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="bg-gradient-primary-to-secondary p-4 mb-4 rounded-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-2 fw-bold">Patient Details</h2>
                <p class="text-white-50 fs-5 mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    {{ $appointment->patient->name }}'s Personal Information
                </p>
            </div>
            <a href="{{ route('doctor.reportList', $appointment->patient->id) }}" 
               class="btn btn-light btn-lg">
                <i class="bi bi-clipboard2-pulse me-2"></i>View Reports
            </a>
        </div>
    </div>

    <!-- Patient Profile Card -->
    <div class="card border-0 shadow-sm hover-shadow mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-center">
                <div class="me-md-4 mb-3 mb-md-0 text-center">
                    @if($appointment->patient->profile_picture)
                        <img src="{{ asset($appointment->patient->profile_picture) }}" 
                             alt="{{ $appointment->patient->name }}" 
                             class="rounded-circle shadow-sm"
                             style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center text-white shadow-sm"
                             style="width: 120px; height: 120px; font-size: 48px;">
                            {{ strtoupper(substr($appointment->patient->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="text-center text-md-start">
                    <h3 class="fw-bold text-gradient mb-2">{{ $appointment->patient->name }}</h3>
                    <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-person me-1"></i>{{ ucfirst($appointment->patient->gender) }}
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-droplet me-1"></i>{{ $appointment->patient->blood_type }}
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-calendar me-1"></i>{{ $appointment->patient->getAgeFromIc() }} years
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Personal Information -->
                <div class="col-md-6">
                    <div class="detail-card bg-light p-3 rounded-3">
                        <h5 class="mb-3"><i class="bi bi-person-vcard me-2"></i>Personal Information</h5>
                        <div class="detail-item mb-3">
                            <label class="text-muted mb-1">Email Address</label>
                            <p class="mb-0">{{ $appointment->patient->email }}</p>
                        </div>
                        <div class="detail-item mb-3">
                            <label class="text-muted mb-1">IC Number</label>
                            <p class="mb-0">{{ $appointment->patient->ic_number }}</p>
                        </div>
                        <div class="detail-item mb-3">
                            <label class="text-muted mb-1">Contact Number</label>
                            <p class="mb-0">{{ $appointment->patient->contact_number }}</p>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted mb-1">Address</label>
                            <p class="mb-0">{{ $appointment->patient->address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <!-- Medical Information -->
                    <div class="detail-card bg-light p-3 rounded-3 mb-4">
                        <h5 class="mb-3"><i class="bi bi-heart-pulse me-2"></i>Medical Information</h5>
                        <div class="detail-item mb-3">
                            <label class="text-muted mb-1">Medical History</label>
                            <p class="mb-0">{{ $appointment->patient->medical_history ?: 'None' }}</p>
                        </div>
                        <div class="detail-item">
                            <label class="text-muted mb-1">Description</label>
                            <p class="mb-0">{{ $appointment->patient->description ?: 'No description provided' }}</p>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="detail-card bg-light p-3 rounded-3">
                        <h5 class="mb-3"><i class="bi bi-telephone-fill me-2"></i>Emergency Contact</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item mb-3">
                                    <label class="text-muted mb-1">Contact Person</label>
                                    <p class="mb-0">{{ $appointment->patient->emergency_contact }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted mb-1">Relation</label>
                                    <p class="mb-0">{{ ucfirst($appointment->patient->relation) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Styling */
.bg-gradient-primary-to-secondary {
    background: linear-gradient(45deg, #1a237e, #0277bd);
}

.text-gradient {
    background: linear-gradient(45deg, #1a237e, #0277bd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    transition: all 0.3s ease;
}

.detail-card {
    transition: all 0.3s ease;
}

.detail-card:hover {
    background-color: #f8f9fa !important;
}

.detail-item label {
    font-size: 0.875rem;
    font-weight: 500;
}

.detail-item p {
    font-size: 1rem;
    color: #2d3748;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection
