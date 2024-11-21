@extends('doctor.layout')
@section('title', 'Edit Patient Details')
@section('content')
<div class="container-fluid p-0" style="max-width: 1400px; margin: 0 auto;">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient fs-1 mb-2">Edit Patient Details</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-pencil-square me-2"></i>
                Update patient's personal information
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('doctor.updatePatientDetails', $appointment->patient->id) }}" method="POST" class="row g-4">
                @csrf
                @method('PUT')
                
                <!-- Profile & Basic Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Profile Information</h5>
                    <div class="text-center mb-3">
                        @if($appointment->patient->profile_picture)
                            <img src="{{ asset($appointment->patient->profile_picture) }}" alt="Profile Picture" 
                                 class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;" 
                                 id="profile_preview">
                        @else
                            <img src="" alt="Preview" class="rounded-circle mb-3" 
                                 style="width: 150px; height: 150px; object-fit: cover; display: none;" 
                                 id="profile_preview">
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-person fs-4"></i></span>
                                <input type="text" class="form-control" value="{{ $appointment->patient->name }}" readonly>
                                <input type="hidden" name="name" value="{{ $appointment->patient->name }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-shield fs-4"></i></span>
                                <input type="text" class="form-control" value="{{ ucfirst(str_replace('_', ' ', $appointment->patient->role)) }}" readonly>
                                <input type="hidden" name="role" value="{{ $appointment->patient->role }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-gender-ambiguous fs-4"></i></span>
                                <input type="text" class="form-control" value="{{ ucfirst($appointment->patient->gender) }}" readonly>
                                <input type="hidden" name="gender" value="{{ $appointment->patient->gender }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Contact Information</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-envelope fs-4"></i></span>
                                <input type="email" class="form-control" value="{{ $appointment->patient->email }}" readonly>
                                <input type="hidden" name="email" value="{{ $appointment->patient->email }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-telephone fs-4"></i></span>
                                <input type="text" class="form-control" value="{{ $appointment->patient->contact_number }}" readonly>
                                <input type="hidden" name="contact_number" value="{{ $appointment->patient->contact_number }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-card-text fs-4"></i></span>
                                <input type="text" class="form-control" name="ic_number" 
                                       value="{{ old('ic_number', $appointment->patient->ic_number) }}" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-geo-alt fs-4"></i></span>
                                <input type="text" class="form-control" name="address" 
                                       value="{{ old('address', $appointment->patient->address) }}" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-telephone-plus fs-4"></i></span>
                                <input type="text" class="form-control" name="emergency_contact" 
                                       value="{{ old('emergency_contact', $appointment->patient->emergency_contact) }}" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-people fs-4"></i></span>
                                <select class="form-select" name="relation" readonly>
                                    <option value="" disabled>Select Relation</option>
                                    <option value="parent" {{ $appointment->patient->relation == 'parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="child" {{ $appointment->patient->relation == 'child' ? 'selected' : '' }}>Child</option>
                                    <option value="sibling" {{ $appointment->patient->relation == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Medical Information</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-droplet fs-4"></i></span>
                                <select class="form-select" name="blood_type" required>
                                    <option value="" disabled>Select Blood Type</option>
                                    @foreach(['rh+a', 'rh-a', 'rh+b', 'rh-b', 'rh+ab', 'rh-ab', 'rh+o', 'rh-o'] as $type)
                                        <option value="{{ $type }}" {{ $appointment->patient->blood_type == $type ? 'selected' : '' }}>
                                            {{ strtoupper($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label"><i class="bi bi-clipboard2-pulse me-2"></i>Medical History</label>
                            <div class="medical-history-group">
                                <div class="row">
                                    @php
                                        // Safely handle medical history, defaulting to empty array if null
                                        $userMedicalHistory = [];
                                        
                                        if ($appointment->patient->medical_history) {
                                            if (is_string($appointment->patient->medical_history)) {
                                                // Handle comma-separated string
                                                $userMedicalHistory = array_filter(
                                                    array_map('trim', explode(',', $appointment->patient->medical_history))
                                                );
                                            } elseif (is_array($appointment->patient->medical_history)) {
                                                // Handle array
                                                $userMedicalHistory = $appointment->patient->medical_history;
                                            }
                                        }
                                        
                                        // If no medical history is set, default to 'none'
                                        if (empty($userMedicalHistory)) {
                                            $userMedicalHistory = ['none'];
                                        }
                                        
                                        $conditions = ['none', 'allergy', 'diabetes', 'hypertension', 'others'];
                                        $halfCount = ceil(count($conditions) / 2);
                                    @endphp
                                    
                                    <!-- First Column -->
                                    <div class="col-6">
                                        @foreach(array_slice($conditions, 0, $halfCount) as $condition)
                                            <div class="medical-history-item">
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           class="form-check-input" 
                                                           name="medical_history[]" 
                                                           value="{{ $condition }}" 
                                                           id="medical_{{ $condition }}"
                                                           {{ in_array($condition, $userMedicalHistory) ? 'checked' : '' }}
                                                           onchange="handleMedicalHistoryChange(this)">
                                                    <label class="form-check-label" for="medical_{{ $condition }}">
                                                        {{ ucfirst($condition) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Second Column -->
                                    <div class="col-6">
                                        @foreach(array_slice($conditions, $halfCount) as $condition)
                                            <div class="medical-history-item">
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           class="form-check-input" 
                                                           name="medical_history[]" 
                                                           value="{{ $condition }}" 
                                                           id="medical_{{ $condition }}"
                                                           {{ in_array($condition, $userMedicalHistory) ? 'checked' : '' }}
                                                           onchange="handleMedicalHistoryChange(this)">
                                                    <label class="form-check-label" for="medical_{{ $condition }}">
                                                        {{ ucfirst($condition) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" id="description-field">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-file-text fs-4"></i></span>
                                <input type="text" class="form-control" name="description" 
                                       placeholder="Medical history details (optional)"
                                       value="{{ old('description', $appointment->patient->description) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-check-circle me-2 fs-4"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card { 
    border: none; 
    border-radius: 20px; 
    box-shadow: 0 8px 12px -1px rgba(0,0,0,.1);
    margin-bottom: 2rem;
}
.text-gradient { 
    background: linear-gradient(135deg,var(--primary),var(--secondary)); 
    -webkit-background-clip: text; 
    -webkit-text-fill-color: transparent;
    font-size: 2.5rem;
}
.input-group-text { 
    background: var(--background); 
    border-right: none;
    padding: 0.8rem 1.2rem;
}
.input-group .form-control, 
.input-group .form-select { 
    border-left: none;
    padding: 0.8rem 1.2rem;
    font-size: 1.1rem;
}
.input-group:focus-within .input-group-text { 
    border-color: var(--primary); 
    color: var(--primary) 
}
.input-group:focus-within .form-control, 
.input-group:focus-within .form-select { 
    border-color: var(--primary) 
}
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

.form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

.form-check-label {
    font-size: 1.1rem;
    padding-left: 0.5rem;
}

.form-control[readonly], .form-select[readonly] {
    background-color: var(--background);
    opacity: 0.8
}
</style>

<script>
function toggleStaffIdField() {
    const roleSelect = document.querySelector('select[name="role"]');
    const staffIdField = document.getElementById('staff-id');
    staffIdField.style.display = (roleSelect.value === 'patient') ? 'none' : 'block';
}

function handleMedicalHistoryChange(checkbox) {
    const noneCheckbox = document.getElementById('medical_none');
    const allCheckboxes = document.querySelectorAll('input[name="medical_history[]"]');
    
    if (checkbox.value === 'none' && checkbox.checked) {
        // If 'none' is selected, uncheck all other options
        allCheckboxes.forEach(cb => {
            if (cb.value !== 'none') cb.checked = false;
        });
    } else if (checkbox.checked) {
        // If any other option is selected, uncheck 'none'
        noneCheckbox.checked = false;
    }
    
    // If no checkboxes are selected, automatically check 'none'
    let anyChecked = false;
    allCheckboxes.forEach(cb => {
        if (cb.checked && cb.value !== 'none') anyChecked = true;
    });
    
    if (!anyChecked) {
        noneCheckbox.checked = true;
    }
}

function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('profile_preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleStaffIdField();
});
</script>
@endsection
