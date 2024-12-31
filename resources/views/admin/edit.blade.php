@extends('admin.layout')
@section('title', 'Edit User Details')
@section('content')
<div class="container-fluid p-0" style="max-width: 1400px; margin: 0 auto;">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient fs-1 mb-2">Edit User Profile</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-pencil-square me-2"></i>
                Update profile information
            </p>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('details.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="row g-4">
                @csrf
                @method('PUT')
                
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <!-- Profile & Basic Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Profile Information</h5>
                    <div class="text-center mb-4">
                        <img src="{{ asset($user->profile_picture) }}" 
                             alt="Profile Picture" 
                             class="rounded-circle mb-3 shadow-sm" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    
                    <div class="info-group">
                        <div class="info-item">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-person fs-4"></i></span>
                                <input type="text" class="form-control" name="name" value="{{ $user->name }}">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-shield fs-4"></i></span>
                                <select class="form-select" name="role" id="role" onchange="toggleStaffIdField()" required>
                                    @foreach(['admin', 'doctor', 'nurse_admin', 'nurse', 'patient'] as $roleOption)
                                        <option value="{{ $roleOption }}" {{ $user->role == $roleOption ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $roleOption)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="info-item" id="staff-id">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-person-badge fs-4"></i></span>
                                <input type="text" class="form-control @error('staff_id') is-invalid @enderror" 
                                       id="staff_id" name="staff_id" value="{{ old('staff_id', $user->staff_id) }}" 
                                       placeholder="Staff ID">
                            </div>
                        </div>
                        <div class="info-item">
                            <label class="info-label" for="gender"><i class="bi bi-gender-ambiguous me-2"></i>Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Contact Information</h5>
                    <div class="info-group">
                        <div class="info-item">
                            <label class="info-label" for="email"><i class="bi bi-envelope me-2"></i>Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                        </div>
                        <div class="info-item">
                            <label class="info-label" for="contact_number"><i class="bi bi-telephone me-2"></i>Contact</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ $user->contact_number }}">
                        </div>
                        <div class="info-item">
                            <label class="info-label" for="ic_number"><i class="bi bi-card-text me-2"></i>IC Number</label>
                            <input type="text" class="form-control" id="ic_number" name="ic_number" value="{{ $user->ic_number }}">
                        </div>
                        <div class="info-item">
                            <label class="info-label" for="address"><i class="bi bi-geo-alt me-2"></i>Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ $user->address }}</textarea>
                        </div>
                        <div class="info-item mb-0">
                            <label class="info-label" for="emergency_contact"><i class="bi bi-telephone-plus me-2"></i>Emergency</label>
                            <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="{{ $user->emergency_contact }}">
                        </div>
                        <div class="info-item mb-0">
                            <label class="info-label" for="relation"><i class="bi bi-people me-2"></i>Relation</label>
                            <input type="text" class="form-control" id="relation" name="relation" value="{{ $user->relation }}">
                        </div>
                    </div>
                </div>

                <!-- Medical Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Medical Information</h5>
                    <div class="info-group">
                        <div class="info-item">
                            <label class="info-label" for="blood_type"><i class="bi bi-droplet me-2"></i>Blood Type</label>
                            <select class="form-select" id="blood_type" name="blood_type" required>
                                @foreach(['rh+ a', 'rh- a', 'rh+ b', 'rh- b', 'rh+ ab', 'rh- ab', 'rh+ o', 'rh- o'] as $type)
                                    <option value="{{ $type }}" {{ strtolower($user->blood_type) === $type ? 'selected' : '' }}>
                                        {{ strtoupper(str_replace(['rh+ ', 'rh- '], ['', ''], $type)) }}{{ str_contains($type, 'rh-') ? '-' : '+' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="info-item">
                            <label class="form-label"><i class="bi bi-clipboard2-pulse me-2"></i>Medical History</label>
                            <div class="medical-history-group">
                                <div class="row ps-3">
                                    @php
                                        $userMedicalHistory = !empty($user->medical_history) 
                                            ? (is_string($user->medical_history) 
                                                ? array_map('trim', explode(',', $user->medical_history)) 
                                                : (is_array($user->medical_history) ? $user->medical_history : []))
                                            : ['none'];
                                       
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
                        <div class="info-item mb-0">
                            <label class="info-label" for="description"><i class="bi bi-file-text me-2"></i>Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ $user->description }}</textarea>
                        </div>
                        <div class="info-item mb-5 bg-white"></div>
                        <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-save me-2"></i>Save Changes
                                </button>
                            </div>
                    </div>
                </div>
            </div>
        </form>
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
    font-size: 1.8rem;
}
.info-group {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}
.info-item {
    padding: 0.5rem;
    background: var(--background);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.info-label {
    color: var(--text-muted);
    font-size: 0.8rem;
    font-weight: 500;
}
.form-control, .form-select {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.3rem 0.8rem;
    background-color: var(--input-background);
    color: var(--text);
    font-size: 0.9rem;
}
.form-control:focus, .form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
}
.btn-primary {
    background: linear-gradient(135deg,var(--primary),var(--secondary));
    border: none;
    border-radius: 12px;
    padding: 0.8rem 2rem;
    font-weight: 500;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
}
.input-group-text {
    padding: 0.3rem 0.8rem;
}
.input-group-lg > .form-control {
    min-height: auto;
    padding: 0.3rem 0.8rem;
}
textarea.form-control {
    height: 60px;
    min-height: auto;
}
h5.fw-bold {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}
.mb-4 {
    margin-bottom: 0.8rem !important;
}
.mb-3 {
    margin-bottom: 0.5rem !important;
}
.g-4 {
    --bs-gutter-y: 0.8rem;
    --bs-gutter-x: 0.8rem;
}
.rounded-circle.mb-3 {
    width: 100px !important;
    height: 100px !important;
    margin-bottom: 0.5rem !important;
}
.medical-history-group {
    padding: 0.5rem;
}
.medical-history-item {
    margin-bottom: 0.3rem;
    padding: 0.2rem;
}
.form-check-label {
    font-size: 0.9rem;
}
.btn-lg {
    padding: 0.5rem 1.5rem;
    font-size: 1rem;
}
.mt-4 {
    margin-top: 0.8rem !important;
}
.mb-0 {
    margin-bottom: 0 !important;
}
.mt-2 {
    margin-top: 0.5rem !important;
}
.ps-3 {
    padding-left: 1rem !important;
}
</style>

<script>
function toggleStaffIdField() {
    const roleSelect = document.querySelector('select[name="role"]');
    const staffIdField = document.getElementById('staff-id');
    staffIdField.style.display = (roleSelect.value === 'patient') ? 'none' : 'block';
}

function handleMedicalHistoryChange(checkbox) {
    if (checkbox.value === 'none' && checkbox.checked) {
        // If 'none' is checked, uncheck all other options
        document.querySelectorAll('input[name="medical_history[]"]').forEach(cb => {
            if (cb.value !== 'none') cb.checked = false;
        });
    } else if (checkbox.checked) {
        // If any other option is checked, uncheck 'none'
        document.querySelector('input[value="none"]').checked = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleStaffIdField();
});
</script>
@endsection 