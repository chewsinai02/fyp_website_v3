@extends('doctor.layout')
@section('title', 'View Medical Report')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="bg-gradient-primary-to-secondary p-4 mb-4 rounded-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-2 fw-bold">Medical Report Details</h2>
                <p class="text-white-50 fs-5 mb-0">
                    <i class="bi bi-file-earmark-medical me-2"></i>
                    Report for {{ $appointment->patient->name }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('doctor.printReport', $report->id) }}" 
                   class="btn btn-success btn-lg">
                    <i class="bi bi-printer me-2"></i>Print Report
                </a>
                <a href="{{ route('doctor.editReport', $report->id) }}" 
                   class="btn btn-warning btn-lg">
                    <i class="bi bi-pencil me-2"></i>Edit Report
                </a>
                <a href="{{ route('doctor.reportList', $appointment->patient->id) }}" 
                   class="btn btn-light btn-lg">
                    <i class="bi bi-arrow-left me-2"></i>Back to Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="card border-0 shadow-sm hover-shadow mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <!-- Patient Photo -->
                <div class="me-4">
                    @if($appointment->patient->profile_picture)
                        <img src="{{ asset($appointment->patient->profile_picture) }}" 
                             alt="{{ $appointment->patient->name }}" 
                             class="rounded-circle shadow-sm"
                             style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center text-white shadow-sm"
                             style="width: 80px; height: 80px; font-size: 32px;">
                            {{ strtoupper(substr($appointment->patient->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <!-- Patient Details -->
                <div>
                    <h4 class="mb-1 fw-bold text-gradient">{{ $appointment->patient->name }}</h4>
                    <div class="d-flex flex-wrap gap-3">
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-person me-1"></i>{{ ucfirst($appointment->patient->gender) }}
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-calendar me-1"></i>{{ $appointment->patient->getAgeFromIc() }} years
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-telephone me-1"></i>{{ $appointment->patient->contact_number }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Report Basic Info -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">Report Title</label>
                        <p class="fs-5">{{ $report->title }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Report Date</label>
                        <p class="fs-5">{{ date('F j, Y', strtotime($report->report_date)) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vital Signs -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="bi bi-heart-pulse me-2"></i>Vital Signs</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted">Blood Pressure</label>
                            <p class="fs-5">{{ $report->blood_pressure_systolic }}/{{ $report->blood_pressure_diastolic }} mmHg</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted">Heart Rate</label>
                            <p class="fs-5">{{ $report->heart_rate }} bpm</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted">Temperature</label>
                            <p class="fs-5">{{ $report->temperature }}°C</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted">Respiratory Rate</label>
                            <p class="fs-5">{{ $report->respiratory_rate }} /min</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clinical Information -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Clinical Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted">Chief Complaint</label>
                            <p class="fs-5">{{ $report->symptoms }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Present Illness History</label>
                            <p class="fs-5">{{ $report->examination_findings }}</p>
                        </div>
                        @if($report->lab_results)
                        <div class="col-12">
                            <label class="text-muted">Lab Results</label>
                            <p class="fs-5">{{ $report->lab_results }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Diagnosis & Treatment -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="bi bi-bandaid me-2"></i>Diagnosis & Treatment</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted">Diagnosis</label>
                            <p class="fs-5">{{ $report->diagnosis }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Treatment Plan</label>
                            <p class="fs-5">{{ $report->treatment_plan }}</p>
                        </div>
                        @if($report->medications)
                        <div class="col-12">
                            <label class="text-muted">Medications</label>
                            <p class="fs-5">{{ $report->medications }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Attachments -->
        @if(!empty($report->attachments))
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="bi bi-paperclip me-2"></i>Attachments</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($report->attachments as $attachment)
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    @php
                                        $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
                                    @endphp

                                    @if($isImage)
                                        <img src="{{ asset('attachments/' . $attachment) }}" 
                                             class="img-fluid rounded mb-2" 
                                             alt="Attachment">
                                    @else
                                        <div class="text-center p-3 bg-light rounded mb-2">
                                            <i class="bi bi-file-earmark-text display-4"></i>
                                            <p class="mb-0">{{ strtoupper($extension) }} File</p>
                                        </div>
                                    @endif

                                    <a href="{{ asset('attachments/' . $attachment) }}" 
                                       class="btn btn-outline-primary btn-sm w-100" 
                                       target="_blank">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Keep your existing styles */
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
}

.card {
    border-radius: 15px;
    transition: all 0.3s ease;
}

.card-header {
    border-top-left-radius: 15px !important;
    border-top-right-radius: 15px !important;
    border-bottom: none;
}
</style>
@endsection
