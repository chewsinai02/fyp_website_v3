@extends('admin.layout')
@section('title', 'Edit Profile')
@section('content')
<div class="container-fluid p-0" style="max-width: 1400px; margin: 0 auto;">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient fs-1 mb-2">Edit Personal Details</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-pencil-square me-2"></i>
                Update your personal information
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
            <form action="{{ route('details.update',$user->id) }}" method="POST" enctype="multipart/form-data" class="row g-4">
                @csrf
                @method('PUT')
                
                <!-- Profile & Basic Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Profile Information</h5>
                    <div class="text-center mb-3">
                        @if($user->profile_picture)
                            <img src="{{ asset($user->profile_picture) }}" alt="Profile Picture" 
                                 class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;" 
                                 id="profile_preview">
                        @else
                            <img src="" alt="Preview" class="rounded-circle mb-3" 
                                 style="width: 150px; height: 150px; object-fit: cover; display: none;" 
                                 id="profile_preview">
                        @endif
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="bi bi-camera fs-4"></i></span>
                            <input type="file" class="form-control" name="profile_picture" 
                                   onchange="previewImage(event)">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-person fs-4"></i></span>
                                <input type="text" class="form-control" name="name" 
                                       value="{{ old('name', $user->name) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-shield fs-4"></i></span>
                                <select class="form-select" name="role" onchange="toggleStaffIdField()" required>
                                    @foreach(['admin', 'doctor', 'nurse_admin', 'nurse', 'patient'] as $role)
                                        <option value="{{ $role }}" {{ $user->role == $role ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12" id="staff-id">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-person-badge fs-4"></i></span>
                                <input type="text" class="form-control" name="staff_id" 
                                       value="{{ old('staff_id',$user->staff_id) }}" 
                                       placeholder="Staff ID">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-gender-ambiguous fs-4"></i></span>
                                <select class="form-select" name="gender">
                                    <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
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
                                <input type="email" class="form-control" name="email" 
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-telephone fs-4"></i></span>
                                <input type="text" class="form-control" name="contact_number" 
                                       value="{{ old('contact_number', $user->contact_number) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-card-text fs-4"></i></span>
                                <input type="text" class="form-control" name="ic_number" 
                                       value="{{ old('ic_number', $user->ic_number) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-geo-alt fs-4"></i></span>
                                <input type="text" class="form-control" name="address" 
                                       value="{{ old('address', $user->address) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-telephone-plus fs-4"></i></span>
                                <input type="text" class="form-control" name="emergency_contact" 
                                       value="{{ old('emergency_contact', $user->emergency_contact) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-people fs-4"></i></span>
                                <select class="form-select" name="relation" required>
                                    <option value="" disabled>Select Relation</option>
                                    <option value="parent" {{ $user->relation == 'parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="child" {{ $user->relation == 'child' ? 'selected' : '' }}>Child</option>
                                    <option value="sibling" {{ $user->relation == 'sibling' ? 'selected' : '' }}>Sibling</option>
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
                                        <option value="{{ $type }}" {{ $user->blood_type == $type ? 'selected' : '' }}>
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
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-file-text fs-4"></i></span>
                                <input type="text" class="form-control" name="description" 
                                       placeholder="Medical history details (optional)"
                                       value="{{ old('description', $user->description) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-check-circle me-2 fs-4"></i>Update Profile
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
