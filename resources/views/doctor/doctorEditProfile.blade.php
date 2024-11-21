@extends('doctor.layout')
@section('title', 'Profile Settings')
@section('content')
<div class="container-fluid p-0" style="max-width: 1300px; margin: 0 auto;">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient fs-1 mb-2">Profile Settings</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-person-gear me-2"></i>
                Manage your account information
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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card profile-card h-100">
                <div class="card-body p-4">
                    <h5 class="mb-3 fw-bold">Profile Information</h5>
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            @if($user->profile_picture)
                                <img src="{{ asset($user->profile_picture) }}" 
                                     alt="Profile" 
                                     class="rounded-circle mb-3 shadow-sm" 
                                     id="profile_preview"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/profile.png') }}" 
                                     alt="Profile" 
                                     class="rounded-circle mb-3 shadow-sm" 
                                     id="profile_preview"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @endif
                            <div class="upload-overlay" onclick="document.getElementById('profile_picture').click();">
                                <i class="bi bi-camera-fill"></i>
                            </div>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <i class="bi bi-person-circle text-primary"></i>
                            <div>
                                <small>Name</small>
                                <h6 class="mb-0">{{ $user->name }}</h6>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-envelope-fill text-primary"></i>
                            <div>
                                <small>Email</small>
                                <h6 class="mb-0">{{ $user->email }}</h6>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-shield-fill text-primary"></i>
                            <div>
                                <small>Role</small>
                                <h6 class="mb-0">{{ ucfirst($user->role) }}</h6>
                            </div>
                        </div>
                        
                        @if($user->staff_id)
                        <div class="info-item">
                            <i class="bi bi-person-badge-fill text-primary"></i>
                            <div>
                                <small>Staff ID</small>
                                <h6 class="mb-0">{{ $user->staff_id }}</h6>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('doctorUpdateProfilePicture') }}" enctype="multipart/form-data" class="col-md-8">
            @csrf
            <input type="file" class="d-none" id="profile_picture" name="profile_picture" 
                   accept="image/*" onchange="previewImage(event)">

            <div class="card details-card">
                <div class="card-body p-4">
                    <div class="details-header mb-4">
                        <h5 class="details-title">
                            <i class="bi bi-person-lines-fill me-2"></i>
                            Profile Details
                        </h5>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-telephone fs-4"></i></span>
                                <input type="text" class="form-control" name="contact_number" 
                                       value="{{ old('contact_number', $user->contact_number) }}" 
                                       placeholder="Contact Number" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-geo-alt fs-4"></i></span>
                                <input type="text" class="form-control" name="address" 
                                       value="{{ old('address', $user->address) }}" 
                                       placeholder="Address" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-droplet fs-4"></i></span>
                                <select class="form-select" name="blood_type" readonly>
                                    <option value="" disabled {{ !$user->blood_type ? 'selected' : '' }}>Select Blood Type</option>
                                    @foreach(['RH+ A', 'RH-A', 'RH+B', 'RH-B', 'RH+AB', 'RH-AB', 'RH+O', 'RH-O'] as $type)
                                        <option value="{{ $type }}" {{ $user->blood_type == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-gender-ambiguous fs-4"></i></span>
                                <select class="form-select" name="gender" readonly>
                                    <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
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

                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-file-text fs-4"></i></span>
                                <input type="text" class="form-control" name="description" 
                                       placeholder="Medical history details (optional)"
                                       value="{{ old('description', $user->description) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-telephone-plus fs-4"></i></span>
                                <input type="text" class="form-control" name="emergency_contact" 
                                       value="{{ old('emergency_contact', $user->emergency_contact) }}" 
                                       placeholder="Emergency Contact" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-people fs-4"></i></span>
                                <select class="form-select" name="relation" required>
                                    <option value="" disabled {{ !$user->relation ? 'selected' : '' }}>Select Relation</option>
                                    <option value="parent" {{ $user->relation == 'parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="child" {{ $user->relation == 'child' ? 'selected' : '' }}>Child</option>
                                    <option value="sibling" {{ $user->relation == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Profile Info Styles */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    padding: 0.5rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.info-item:hover {
    background: #f1f5f9;
}

.info-item i {
    font-size: 1.25rem;
}

.info-item small {
    color: #64748b;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-item h6 {
    color: #1e293b;
    font-weight: 500;
}

.medical-history-group {
    background: var(--background);
    border-radius: 12px;
}

.medical-history-item {
    transition: background-color 0.2s;
}

.medical-history-item:last-child {
    margin-bottom: 0;
}

.medical-history-item:hover {
    background: rgba(var(--primary-rgb), 0.1);
}

/* Profile Details Header Styles */
.details-header {
    border-bottom: 2px solid #f1f5f9;
    padding-bottom: 1rem;
}

.details-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.details-title i {
    color: #4a90e2;
}

.details-subtitle {
    color: #718096;
    font-size: 0.95rem;
    margin-bottom: 0;
    padding-left: 1.8rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
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
    }
}

function handleMedicalHistoryChange(checkbox) {
    const noneCheckbox = document.getElementById('medical_none');
    const allCheckboxes = document.querySelectorAll('input[name="medical_history[]"]');
    
    if (checkbox.value === 'none' && checkbox.checked) {
        allCheckboxes.forEach(cb => {
            if (cb.value !== 'none') cb.checked = false;
        });
    } else if (checkbox.checked) {
        noneCheckbox.checked = false;
    }
    
    let anyChecked = false;
    allCheckboxes.forEach(cb => {
        if (cb.checked && cb.value !== 'none') anyChecked = true;
    });
    
    if (!anyChecked) {
        noneCheckbox.checked = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto dismiss alerts after 3 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 3000);
    });
});
</script>
@endsection
