@extends('nurseAdmin.layout')
@section('title', 'Change Password')
@section('content')

<div class="container-fluid p-0" style="max-width: 1200px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="page-icon">
                    <i class="bi bi-shield-lock fs-1 text-primary"></i>
                </div>
            </div>
            <div class="col">
                <h1 class="page-title mb-0">Security Settings</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#" class="text-muted">Settings</a></li>
                        <li class="breadcrumb-item active">Change Password</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Profile Summary -->
        <div class="col-lg-4">
            <div class="card profile-card border-0 h-100">
                <div class="position-relative">
                    <div class="profile-cover" style="height: 100px; background: linear-gradient(45deg, #3b82f6, #06b6d4);"></div>
                    <div class="profile-image-wrapper text-center" style="margin-top: -50px;">
                        <img src="{{ asset($user->profile_picture) }}" 
                             alt="Profile Picture" 
                             class="profile-image"
                             style="width: 100px; height: 100px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    </div>
                </div>
                
                <div class="card-body pt-2">
                    <div class="text-center mb-4">
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <span class="badge bg-primary-subtle text-primary">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                    </div>

                    <div class="profile-info">
                        <div class="info-item d-flex align-items-center p-3 rounded-3 mb-3">
                            <div class="icon-wrapper me-3">
                                <i class="bi bi-envelope text-primary"></i>
                            </div>
                            <div class="info-content">
                                <label class="text-muted small mb-1">Email Address</label>
                                <div class="fw-medium">{{ $user->email }}</div>
                            </div>
                        </div>
                        
                        <div class="info-item d-flex align-items-center p-3 rounded-3">
                            <div class="icon-wrapper me-3">
                                <i class="bi bi-clock-history text-primary"></i>
                            </div>
                            <div class="info-content">
                                <label class="text-muted small mb-1">Last Password Change</label>
                                <div class="fw-medium">
                                    {{ \Carbon\Carbon::parse($user->updated_at)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Password Change Form -->
        <div class="col-lg-8">
            <div class="card border-0 h-100">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">Change Password</h4>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div class="icon me-3">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <div>
                                    <h6 class="alert-heading mb-1">Please check the following errors:</h6>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('nurseadmin.checkCurrentPassword') }}" class="password-form">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label">Current Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-key"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       name="current_password"
                                       placeholder="Enter your current password"
                                       required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       name="new_password"
                                       placeholder="Enter new password"
                                       required>
                            </div>
                            <div class="form-text">
                                Password must be at least 8 characters long and contain letters and numbers
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       name="new_password_confirmation"
                                       placeholder="Confirm new password"
                                       required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-light" onclick="window.history.back()">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-shield-check me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(59, 130, 246, 0.1);
        border-radius: 12px;
        color: #3b82f6;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .profile-card:hover {
        transform: translateY(-5px);
    }

    .info-item {
        background: #f8fafc;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: #f1f5f9;
        transform: translateX(5px);
    }

    .icon-wrapper {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .input-group {
        border-radius: 10px;
        overflow: hidden;
    }

    .input-group-text {
        border: none;
        padding: 0.75rem 1rem;
    }

    .form-control {
        border: none;
        padding: 0.75rem 1rem;
        background: #f8fafc;
    }

    .form-control:focus {
        background: white;
        box-shadow: none;
        border-color: #3b82f6;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 500;
    }

    .btn-primary {
        background: linear-gradient(45deg, #3b82f6, #06b6d4);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #2563eb, #0891b2);
        transform: translateY(-2px);
    }

    .alert {
        border: none;
        border-radius: 12px;
    }

    .badge {
        padding: 0.5em 1em;
        border-radius: 6px;
        font-weight: 500;
    }
</style>

<!-- Success Message Handler -->
@if (session('success'))
<script>
    alert("{{ session('success') }}");
    setTimeout(function() {
        fetch("{{ route('logout') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
        }).then(function() {
            window.location.href = "{{ route('login') }}";
        });
    }, 100);
</script>
@endif

@endsection