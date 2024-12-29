@extends('admin.layout')
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
                            <img id="profile_preview" 
                                 src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/profile.png') }}" 
                                 data-default-image="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/profile.png') }}"
                                 alt="Profile Picture" 
                                 class="rounded-circle mb-3 shadow-sm" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="upload-overlay" 
                                 role="button" 
                                 tabindex="0" 
                                 onclick="document.getElementById('profile_picture').click();"
                                 title="Upload Profile Picture">
                                <i class="bi bi-camera-fill" aria-hidden="true"></i>
                                <span class="visually-hidden">Upload Profile Picture</span>
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

        <form method="POST" 
              action="{{ route('adminUpdateProfilePicture') }}" 
              enctype="multipart/form-data" 
              id="uploadForm" 
              class="col-md-8">
            @csrf
            <input type="file" 
                   class="d-none" 
                   id="profile_picture" 
                   name="profile_picture" 
                   accept="image/*" 
                   onchange="previewImage(event)">

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
                                <span class="input-group-text" aria-hidden="true"><i class="bi bi-telephone fs-4"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="contact_number"
                                       name="contact_number" 
                                       value="{{ old('contact_number', $user->contact_number) }}" 
                                       placeholder="Contact Number"
                                       aria-label="Contact Number" 
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text" aria-hidden="true"><i class="bi bi-geo-alt fs-4"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="address"
                                       name="address" 
                                       value="{{ old('address', $user->address) }}" 
                                       placeholder="Address"
                                       aria-label="Address" 
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text" aria-hidden="true"><i class="bi bi-droplet fs-4"></i></span>
                                <select class="form-select" 
                                        id="blood_type"
                                        name="blood_type" 
                                        aria-label="Blood Type"
                                        required>
                                    <option value="rh+ a" {{ $user->blood_type == 'rh+ a' ? 'selected' : '' }}>A+</option>
                                    <option value="rh- a" {{ $user->blood_type == 'rh- a' ? 'selected' : '' }}>A-</option>
                                    <option value="rh+ b" {{ $user->blood_type == 'rh+ b' ? 'selected' : '' }}>B+</option>
                                    <option value="rh- b" {{ $user->blood_type == 'rh- b' ? 'selected' : '' }}>B-</option>
                                    <option value="rh+ o" {{ $user->blood_type == 'rh+ o' ? 'selected' : '' }}>O+</option>
                                    <option value="rh- o" {{ $user->blood_type == 'rh- o' ? 'selected' : '' }}>O-</option>
                                    <option value="rh+ ab" {{ $user->blood_type == 'rh+ ab' ? 'selected' : '' }}>AB+</option>
                                    <option value="rh- ab" {{ $user->blood_type == 'rh- ab' ? 'selected' : '' }}>AB-</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text" aria-hidden="true"><i class="bi bi-gender-ambiguous fs-4"></i></span>
                                <select class="form-select" 
                                        id="gender"
                                        name="gender" 
                                        aria-label="Gender"
                                        required>
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
                                        $userMedicalHistory = [];
                                        if (!empty($user->medical_history)) {
                                            if (is_string($user->medical_history)) {
                                                $userMedicalHistory = array_map('trim', explode(',', $user->medical_history));
                                            } elseif (is_array($user->medical_history)) {
                                                $userMedicalHistory = $user->medical_history;
                                            }
                                        } else {
                                            $userMedicalHistory = ['none'];
                                        }
                                        
                                        $conditions = ['none', 'allergy', 'diabetes', 'hypertension', 'others'];
                                        $halfCount = ceil(count($conditions) / 2);
                                    @endphp
                                    
                                    <div class="col-6">
                                        @foreach(array_slice($conditions, 0, $halfCount) as $history)
                                            <div class="form-check medical-history-item">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="medical_history[]" 
                                                       value="{{ $history }}" 
                                                       id="medical_{{ $history }}"
                                                       {{ in_array(strtolower($history), array_map('strtolower', $userMedicalHistory)) ? 'checked' : '' }}
                                                       onchange="handleMedicalHistoryChange(this)"
                                                       aria-label="{{ ucfirst($history) }}">
                                                <label class="form-check-label" for="medical_{{ $history }}">
                                                    {{ ucfirst($history) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="col-6">
                                        @foreach(array_slice($conditions, $halfCount) as $history)
                                            <div class="form-check medical-history-item">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="medical_history[]" 
                                                       value="{{ $history }}" 
                                                       id="medical_{{ $history }}"
                                                       {{ in_array(strtolower($history), array_map('strtolower', $userMedicalHistory)) ? 'checked' : '' }}
                                                       onchange="handleMedicalHistoryChange(this)"
                                                       aria-label="{{ ucfirst($history) }}">
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
                                <span class="input-group-text" aria-hidden="true"><i class="bi bi-file-text fs-4"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="description"
                                       name="description" 
                                       value="{{ old('description', $user->description) }}"
                                       placeholder="Medical history details (optional)"
                                       aria-label="Medical History Details">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text" aria-hidden="true"><i class="bi bi-telephone-plus fs-4"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="emergency_contact"
                                       name="emergency_contact" 
                                       value="{{ old('emergency_contact', $user->emergency_contact) }}" 
                                       placeholder="Emergency Contact"
                                       aria-label="Emergency Contact" 
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text" aria-hidden="true"><i class="bi bi-people fs-4"></i></span>
                                <select class="form-select" 
                                        id="relation"
                                        name="relation" 
                                        aria-label="Relation"
                                        required>
                                    <option value="" disabled {{ !$user->relation ? 'selected' : '' }}>Select Relation</option>
                                    <option value="parent" {{ $user->relation == 'parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="child" {{ $user->relation == 'child' ? 'selected' : '' }}>Child</option>
                                    <option value="sibling" {{ $user->relation == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                </select>
                            </div>
                        </div>

                        <!-- Loading and Error Messages -->
                        <div id="loading" class="d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2">Uploading image...</span>
                        </div>

                        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>

                        <div class="col-12 d-flex justify-content-end mt-4">
                            <button type="submit" 
                                    class="btn btn-primary btn-lg px-5"
                                    id="submit-button">
                                <i class="bi bi-check-circle me-2" aria-hidden="true"></i>
                                Save Changes
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

.upload-overlay {
    position: absolute;
    bottom: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    padding: 8px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    -webkit-user-select: none;
    user-select: none;
}

.upload-overlay:hover, 
.upload-overlay:focus {
    background: rgba(0, 0, 0, 0.7);
}

.upload-overlay:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

/* Add loading styles */
.loading {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 1rem;
}
</style>

<!-- Add Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-storage.js"></script>

<script>
// Initialize Firebase
const app = firebase.initializeApp({
    apiKey: "AIzaSyAiElkmNSl0K-N0Rz4kuqKAXrr6Eg7oo64",
    authDomain: "fyptestv2-37c45.firebaseapp.com",
    projectId: "fyptestv2-37c45",
    storageBucket: "fyptestv2-37c45.firebasestorage.app",
    messagingSenderId: "500961952253",
    appId: "1:500961952253:web:a846193490974d3667d994"
});

// Simplified image upload function
async function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('profile_preview');
    
    if (!file) {
        preview.src = preview.getAttribute('data-default-image') || '{{ asset("images/profile.png") }}';
        return;
    }

    // Basic validation
    if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        event.target.value = '';
        return;
    }

    if (!file.type.match('image.*')) {
        alert('Please select an image file');
        event.target.value = '';
        return;
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        preview.src = e.target.result;
    };
    reader.readAsDataURL(file);

    try {
        // Create file reference
        const timestamp = Date.now();
        const filename = `profile_${timestamp}_${file.name}`;
        const storageRef = firebase.storage().ref(`assets/images/${filename}`);

        // Upload file
        const uploadTask = storageRef.put(file);

        // Show upload progress
        const progressBar = document.createElement('div');
        progressBar.className = 'progress mt-2';
        progressBar.innerHTML = `
            <div class="progress-bar" role="progressbar" style="width: 0%" 
                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        `;
        preview.parentElement.appendChild(progressBar);

        uploadTask.on('state_changed', 
            // Progress
            (snapshot) => {
                const progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                progressBar.querySelector('.progress-bar').style.width = progress + '%';
            },
            // Error
            (error) => {
                console.error('Upload failed:', error);
                alert('Upload failed: ' + error.message);
                progressBar.remove();
            },
            // Success
            async () => {
                try {
                    const downloadURL = await uploadTask.snapshot.ref.getDownloadURL();
                    
                    // Add URL to hidden input
                    let urlInput = document.getElementById('profile_picture_url');
                    if (!urlInput) {
                        urlInput = document.createElement('input');
                        urlInput.type = 'hidden';
                        urlInput.id = 'profile_picture_url';
                        urlInput.name = 'profile_picture_url';
                        document.getElementById('uploadForm').appendChild(urlInput);
                    }
                    urlInput.value = downloadURL;
                    
                    // Remove progress bar after success
                    setTimeout(() => progressBar.remove(), 1000);
                } catch (error) {
                    console.error('Failed to get download URL:', error);
                    alert('Failed to get download URL: ' + error.message);
                    progressBar.remove();
                }
            }
        );
    } catch (error) {
        console.error('Upload error:', error);
        alert('Upload failed: ' + error.message);
    }
}

// Update form submission
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const urlInput = document.getElementById('profile_picture_url');
    const fileInput = document.getElementById('profile_picture');
    
    if (urlInput && urlInput.value) {
        // Clear file input if we have a URL
        fileInput.value = '';
    }
});

// Auto dismiss alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endsection
