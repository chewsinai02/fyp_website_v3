@extends('admin.layout')
@section('title', 'Nurse Details')
@section('content')
<div class="container-fluid p-0" style="max-width: 1400px; margin: 0 auto;">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient fs-1 mb-2">Complete Nurse Profile</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-person-vcard me-2"></i>
                Fill in additional personal details
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.nurseUserdata.store', $userToEdit->id) }}" class="row g-4">
                @csrf
                
                <!-- Basic Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Basic Information</h5>
                    
                    <!-- Name & Role -->
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-person fs-4"></i></span>
                                <input type="text" class="form-control" name="name" value="{{ $userToEdit->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-shield fs-4"></i></span>
                                <select class="form-select" name="role" onchange="toggleStaffIdField()" required>
                                    @foreach(['admin', 'doctor', 'nurse_admin', 'nurse', 'patient'] as $role)
                                        <option value="{{ $role }}" {{ $userToEdit->role == $role ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12" id="staff-id">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-person-badge fs-4"></i></span>
                                <input type="text" class="form-control @error('staff_id') is-invalid @enderror" 
                                       name="staff_id" value="{{ old('staff_id',$userToEdit->staff_id) }}" 
                                       placeholder="Staff ID">
                            </div>
                            @error('staff_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-envelope fs-4"></i></span>
                                <input type="email" class="form-control" value="{{ $userToEdit->email }}" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-telephone fs-4"></i></span>
                                <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                                       name="contact_number" placeholder="Contact Number" required>
                            </div>
                            @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Personal Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Personal Information</h5>
                    
                    <!-- IC & Address -->
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-card-text fs-4"></i></span>
                                <input type="text" class="form-control @error('ic_number') is-invalid @enderror" 
                                       name="ic_number" placeholder="IC Number" required>
                            </div>
                            @error('ic_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-house fs-4"></i></span>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       name="address" placeholder="Address" required>
                            </div>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-telephone-fill fs-4"></i></span>
                                <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                       name="emergency_contact" placeholder="Emergency Contact" required>
                            </div>
                            @error('emergency_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-people fs-4"></i></span>
                                <select class="form-select" name="relation" required>
                                    <option value="" disabled selected>Select Relation</option>
                                    <option value="parent">Parent</option>
                                    <option value="child">Child</option>
                                    <option value="sibling">Sibling</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Medical Information</h5>
                    
                    <!-- Blood Type & Medical History -->
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-droplet fs-4"></i></span>
                                <select class="form-select" name="blood_type" required>
                                    <option value="" disabled selected>Blood Type</option>
                                    @foreach(['rh+a', 'rh-a', 'rh+b', 'rh-b', 'rh+ab', 'rh-ab', 'rh+o', 'rh-o'] as $type)
                                        <option value="{{ $type }}">{{ strtoupper($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label"><i class="bi bi-clipboard2-pulse me-2"></i>Medical History</label>
                            <div class="medical-history-group">
                                <div class="row">
                                    @php
                                        // Convert medical history to array, handling different possible formats
                                        $userMedicalHistory = [];
                                        if (!empty($user->medical_history)) {
                                            if (is_string($user->medical_history)) {
                                                $userMedicalHistory = array_map('trim', explode(',', $user->medical_history));
                                            } elseif (is_array($user->medical_history)) {
                                                $userMedicalHistory = $user->medical_history;
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
                        <div class="col-12" id="description-field">
                            <textarea class="form-control" name="description" rows="3" 
                                     placeholder="Medical history details (optional)"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-check-circle me-2 fs-4"></i>Save Details
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
</style>

<script>
function toggleStaffIdField() {
    const roleSelect = document.querySelector('select[name="role"]');
    const staffIdField = document.getElementById('staff-id');
    staffIdField.style.display = (roleSelect.value === 'patient') ? 'none' : 'block';
}

function toggleDescriptionField() {
    const medicalHistorySelect = document.querySelector('select[name="medical_history"]');
    const descriptionField = document.getElementById('description-field');
    descriptionField.style.display = (medicalHistorySelect.value === 'none') ? 'none' : 'block';
}

document.addEventListener('DOMContentLoaded', function() {
    toggleStaffIdField();
    toggleDescriptionField();
});
</script>
@endsection
