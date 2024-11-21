@extends('admin.layout')
@section('title', 'User Details')
@section('content')
<div class="container-fluid p-0" style="max-width: 1400px; margin: 0 auto;">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient fs-1 mb-2">User Profile Details</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-person-vcard me-2"></i>
                Complete profile information
            </p>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="card">
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Profile & Basic Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Profile Information</h5>
                    <div class="text-center mb-4">
                        <img src="{{ asset($user->profile_picture) }}" 
                             alt="Profile Picture" 
                             class="rounded-circle mb-3 shadow-sm" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    
                    <div class="info-group">
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-person me-2"></i>Name</span>
                            <span class="info-value">{{ $user->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-shield me-2"></i>Role</span>
                            <span class="info-value">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                        </div>
                        @if($user->staff_id)
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-person-badge me-2"></i>Staff ID</span>
                            <span class="info-value">{{ $user->staff_id }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-gender-ambiguous me-2"></i>Gender</span>
                            <span class="info-value">{{ ucfirst($user->gender) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Contact Information</h5>
                    <div class="info-group">
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-envelope me-2"></i>Email</span>
                            <span class="info-value">{{ $user->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-telephone me-2"></i>Contact</span>
                            <span class="info-value">{{ $user->contact_number }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-card-text me-2"></i>IC Number</span>
                            <span class="info-value">{{ $user->ic_number }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-geo-alt me-2"></i>Address</span>
                            <span class="info-value">{{ $user->address }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-telephone-plus me-2"></i>Emergency</span>
                            <span class="info-value">{{ $user->emergency_contact }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-people me-2"></i>Relation</span>
                            <span class="info-value">{{ ucfirst($user->relation) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Medical Info Column -->
                <div class="col-md-4">
                    <h5 class="mb-3 fw-bold">Medical Information</h5>
                    <div class="info-group">
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-droplet me-2"></i>Blood Type</span>
                            <span class="info-value">{{ strtoupper($user->blood_type) }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-clipboard2-pulse me-2"></i>Medical History</span>
                            <span class="info-value">{{ $user->medical_history ? ucfirst($user->medical_history) : 'None' }}</span>
                        </div>
                        @if($user->description)
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-file-text me-2"></i>Description</span>
                            <span class="info-value">{{ $user->description }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
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
.info-group {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}
.info-item {
    padding: 1rem;
    background: var(--background);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.info-label {
    color: var(--text-muted);
    font-size: 0.9rem;
    font-weight: 500;
}
.info-value {
    color: var(--text);
    font-size: 1.1rem;
    font-weight: 500;
}
</style>
@endsection
