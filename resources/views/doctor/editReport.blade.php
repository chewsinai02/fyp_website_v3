@extends('doctor.layout')
@section('title', 'Edit Report')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section with Gradient Background -->
    <div class="bg-gradient-primary-to-secondary p-4 mb-4 rounded-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-2 fw-bold">Edit Medical Report</h2>
                <p class="text-white-50 fs-5 mb-0">
                    <i class="bi bi-file-earmark-medical me-2"></i>
                    Updating report for {{ $appointment->patient->name }}
                </p>
            </div>
            <a href="{{ route('doctor.reportList', $appointment->patient->id) }}" 
               class="btn btn-light btn-lg">
                <i class="bi bi-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="card border-0 shadow-sm hover-shadow mb-4" 
         style="border-radius: 15px; transition: all 0.3s ease;">
         <div class="card-body p-4">
            <div class="d-flex align-items-center">
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
                <div>
                    <h4 class="mb-1 fw-bold text-gradient">{{ $appointment->patient->name }}</h4>
                    <div class="d-flex flex-wrap gap-3">
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-person me-1"></i>
                            {{ ucfirst($appointment->patient->gender) }}
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-calendar me-1"></i>
                            {{ $appointment->patient->getAgeFromIc() }} years
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-telephone me-1"></i>
                            {{ $appointment->patient->contact_number }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Form Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form action="{{ route('doctor.updateReport', $report->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="patient_id" value="{{ $appointment->patient->id }}">
                
                <div class="row g-4">
                    <!-- Basic Report Info -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Report Title</label>
                            <input type="text" 
                                   name="title" 
                                   class="form-control" 
                                   required 
                                   value="{{ $report->title }}">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Report Date</label>
                            <input type="date" 
                                   name="report_date" 
                                   class="form-control" 
                                   required 
                                   value="{{ $report->report_date ? date('Y-m-d', strtotime($report->report_date)) : '' }}">
                        </div>
                    </div>

                    <!-- Vital Signs -->
                    <div class="col-12">
                        <h5 class="mb-3">Vital Signs</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Blood Pressure Systolic</label>
                                    <input type="number" 
                                           name="blood_pressure_systolic" 
                                           class="form-control" 
                                           value="{{ $report->blood_pressure_systolic ?? old('blood_pressure_systolic') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Blood Pressure Diastolic</label>
                                    <input type="number" 
                                           name="blood_pressure_diastolic" 
                                           class="form-control"
                                           value="{{ $report->blood_pressure_diastolic ?? old('blood_pressure_diastolic') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Heart Rate (bpm)</label>
                                    <input type="number" 
                                           name="heart_rate" 
                                           class="form-control"
                                           value="{{ $report->heart_rate ?? old('heart_rate') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Temperature (°C)</label>
                                    <input type="number" 
                                           name="temperature" 
                                           class="form-control" 
                                           step="0.1"
                                           value="{{ $report->temperature ?? old('temperature') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Respiratory Rate</label>
                                    <input type="number" 
                                           name="respiratory_rate" 
                                           class="form-control"
                                           value="{{ $report->respiratory_rate }}">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class="form-label">Notes</label>
                                    <input type="text" name="notes"  class="form-control" value="{{ $report->notes }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Measurements -->
                    <div class="col-12">
                        <h5 class="mb-3">Measurements</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Weight (kg)</label>
                                    <input type="number" 
                                           name="weight" 
                                           class="form-control" 
                                           step="0.01" 
                                           value="{{ $report->weight ?? old('weight') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Height (cm)</label>
                                    <input type="number" 
                                           name="height" 
                                           class="form-control" 
                                           step="0.01" 
                                           value="{{ $report->height ?? old('height') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clinical Information -->
                    <div class="col-12">
                        <h5 class="mb-3">Clinical Information</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Chief Complaint</label>
                                    <textarea name="symptoms" 
                                              class="form-control" 
                                              rows="2" 
                                              required>{{ $report->symptoms ?? old('symptoms') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Present Illness History</label>
                                    <textarea name="examination_findings" 
                                              class="form-control" 
                                              rows="3" 
                                              required>{{ $report->examination_findings ?? old('examination_findings') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lab Results -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Lab Results</label>
                            <textarea name="lab_results" 
                                      class="form-control" 
                                      rows="3">{{ $report->lab_results ?? old('lab_results') }}</textarea>
                        </div>
                    </div>

                    <!-- Diagnosis & Treatment -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Diagnosis</label>
                            <input type="text" 
                                   name="diagnosis" 
                                   class="form-control" 
                                   required 
                                   value="{{ $report->diagnosis ?? old('diagnosis') }}">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Treatment Plan</label>
                            <textarea name="treatment_plan" 
                                      class="form-control" 
                                      rows="3" 
                                      required>{{ $report->treatment_plan ?? old('treatment_plan') }}</textarea>
                        </div>
                    </div>

                    <!-- Diseases -->
                    <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-clipboard2-pulse me-2"></i>Diseases</label>
                            <div class="medical-history-group">
                                <div class="row">
                                    @php
                                        // Convert medical history to array, handling different possible formats
                                        $userMedicalHistory = [];
                                            if (!empty($appointment->patient->medical_history)) {
                                            if (is_string($appointment->patient->medical_history)) {
                                                $userMedicalHistory = array_map('trim', explode(',', $appointment->patient->medical_history));
                                            } elseif (is_array($appointment->patient->medical_history)) {
                                                $userMedicalHistory = $appointment->patient->medical_history;
                                            }
                                        } else {
                                            // If medical history is null or empty, set 'none' as selected
                                            $userMedicalHistory = ['none'];
                                        }
                                        
                                        $conditions = ['none', 'allergy', 'diabetes', 'hypertension', 'others'];
                                        $halfCount = ceil(count($conditions) / 2);
                                    @endphp
                                    
                                    <!-- First Column -->
                                    <div class="col-6">
                                        @foreach(array_slice($conditions, 0, $halfCount) as $history)
                                            <div class="form-check medical-history-item">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="medical_history[]" 
                                                       value="{{ $history }}" 
                                                       id="medical_{{ $history }}"
                                                       {{ in_array(strtolower($history), array_map('strtolower', $userMedicalHistory)) ? 'checked' : '' }}
                                                       onchange="handleMedicalHistoryChange(this)">
                                                <label class="form-check-label" for="medical_{{ $history }}">
                                                    {{ ucfirst($history) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Second Column -->
                                    <div class="col-6">
                                        @foreach(array_slice($conditions, $halfCount) as $history)
                                            <div class="form-check medical-history-item">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="medical_history[]" 
                                                       value="{{ $history }}" 
                                                       id="medical_{{ $history }}"
                                                       {{ in_array(strtolower($history), array_map('strtolower', $userMedicalHistory)) ? 'checked' : '' }}
                                                       onchange="handleMedicalHistoryChange(this)">
                                                <label class="form-check-label" for="medical_{{ $history }}">
                                                    {{ ucfirst($history) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Description (Optional)</label>
                                <textarea name="description" 
                                        class="form-control" 
                                        rows="6">{{ $report->description ?? old('description') }}</textarea>
                            </div>
                        </div>

                    <!-- Medications -->
                    <div class="col-12">
                        <h5 class="mb-3">Medications</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Prescribed Medications</label>
                                    <textarea name="medications" 
                                              class="form-control" 
                                              rows="3" 
                                              placeholder="List medications with dosage and frequency">{{ $report->medications ?? old('medications') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Follow-up -->
                    <!-- Follow-up -->
                    <div class="col-12">
                        <h5 class="mb-3">Follow-up</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Follow-up Instructions</label>
                                    <textarea name="follow_up_instructions" 
                                              class="form-control" 
                                              rows="3">{{ $report->follow_up_instructions ?? old('follow_up_instructions') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Follow-up Date</label>
                                    <input type="date" 
                                           name="follow_up_date" 
                                           class="form-control" 
                                           value="{{ $report->follow_up_date ? date('Y-m-d', strtotime($report->follow_up_date)) : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="col-12">
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h5 class="mb-3">Attachments (Optional)</h5>
                                
                                <!-- File Upload Input -->
                                <div class="mb-3">
                                    <input type="file" 
                                        name="attachments[]" 
                                        class="form-control" 
                                        multiple 
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <small class="text-muted">
                                        Accepted files: PDF, DOC, DOCX, JPG, PNG (Max 5MB each)
                                    </small>
                                </div>

                                <!-- Display Existing Attachments (if editing and has attachments) -->
                                @if(isset($report) && !empty($report->attachments))
                                    <div class="row g-3 mt-2" id="attachments-container">
                                        @foreach($report->attachments as $index => $attachment)
                                            <div class="col-md-4 attachment-card" id="attachment-{{ $index }}">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        @php
                                                            $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                                            $fileName = pathinfo($attachment, PATHINFO_FILENAME);
                                                        @endphp

                                                        <!-- Preview -->
                                                        <div class="mb-2">
                                                            @if($isImage)
                                                                <img src="{{ asset('attachments/' . $attachment) }}" 
                                                                    class="img-fluid rounded" 
                                                                    alt="Attachment">
                                                            @else
                                                                <div class="text-center p-3 bg-light rounded">
                                                                    <i class="bi bi-file-earmark-text display-4"></i>
                                                                    <p class="mb-0">{{ strtoupper($extension) }} File</p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- File Name -->
                                                        <p class="mb-2 text-truncate" title="{{ $fileName }}">
                                                            {{ $fileName }}
                                                        </p>

                                                        <!-- Actions -->
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <a href="{{ asset('attachments/' . $attachment) }}" 
                                                            class="btn btn-sm btn-outline-primary" 
                                                            target="_blank">
                                                                <i class="bi bi-eye me-1"></i>View
                                                            </a>
                                                            
                                                            <div class="form-check">
                                                                <input type="checkbox" 
                                                                       class="form-check-input remove-attachment" 
                                                                       name="remove_attachments[]" 
                                                                       value="{{ $attachment }}" 
                                                                       id="remove_{{ $index }}"
                                                                       onchange="toggleAttachment(this)">
                                                                <label class="form-check-label" for="remove_{{ $index }}">
                                                                    Remove
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-save me-2"></i>
                            Update Medical Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.medical-history-group {
    background: var(--background);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.medical-history-item {
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    border-radius: 8px;
    transition: background-color 0.2s;
}

.medical-history-item:last-child {
    margin-bottom: 0;
}

.medical-history-item:hover {
    background: rgba(var(--primary-rgb), 0.1);
}

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
}

.form-control:focus {
    box-shadow: none;
    border-color: var(--bs-primary);
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #1e293b;
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

@if ($errors->any())
    <div class="alert alert-danger mt-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger mt-4">
        {{ session('error') }}
    </div>
@endif
@endsection

@push('scripts')
<script>
function handleMedicalHistoryChange(checkbox) {
    const noneCheckbox = document.getElementById('medical_none');
    const otherCheckboxes = document.querySelectorAll('input[name="medical_history[]"]:not(#medical_none)');

    if (checkbox.id === 'medical_none' && checkbox.checked) {
        // If 'none' is checked, uncheck all other options
        otherCheckboxes.forEach(box => box.checked = false);
    } else if (checkbox.checked) {
        // If any other option is checked, uncheck 'none'
        noneCheckbox.checked = false;
    }

    // If no options are checked, check 'none'
    const anyChecked = Array.from(otherCheckboxes).some(box => box.checked);
    if (!anyChecked) {
        noneCheckbox.checked = true;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="medical_history[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => handleMedicalHistoryChange(checkbox));
    });

    // Initial check
    const anyChecked = Array.from(checkboxes)
        .filter(box => box.id !== 'medical_none')
        .some(box => box.checked);
    if (!anyChecked) {
        document.getElementById('medical_none').checked = true;
    }
});

function toggleAttachment(checkbox) {
    const card = checkbox.closest('.attachment-card');
    if (checkbox.checked) {
        card.style.opacity = '0.5';
        card.style.filter = 'grayscale(100%)';
    } else {
        card.style.opacity = '1';
        card.style.filter = 'none';
    }
}

// Initialize on page load to handle any pre-checked boxes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.remove-attachment').forEach(checkbox => {
        if (checkbox.checked) {
            toggleAttachment(checkbox);
        }
    });
});
</script>
@endpush
