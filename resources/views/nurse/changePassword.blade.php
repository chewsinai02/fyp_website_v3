@extends('nurse.layout')
@section('title', 'Change Password')
@section('content')
<div class="container-fluid p-0" style="max-width: 1400px; margin: 0 auto;">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient fs-1 mb-2">Change Password</h2>
            <p class="text-muted-light fs-5 mb-0">
                <i class="bi bi-shield-lock me-2"></i>
                Update your account security
            </p>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- Profile Info Card -->
        <div class="col-md-4">
            <div class="card h-100 profile-card">
                <div class="card-body p-4">
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
                            <span class="info-label"><i class="bi bi-envelope me-2"></i>Email</span>
                            <span class="info-value">{{ $user->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-shield me-2"></i>Role</span>
                            <span class="info-value">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Password Change Card -->
        <div class="col-md-8">
            <div class="card h-80 p-4 password-card">
                <div class="card-body p-4">
                    <h5 class="mb-3 fw-bold">Change Password</h5>
                    <form method="POST" action="{{ route('nurseadmin.checkCurrentPassword') }}" class="row g-4">
                        @csrf
                        
                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Password Fields -->
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-key fs-4"></i></span>
                                <input type="password" class="form-control" name="current_password" 
                                       placeholder="Current Password" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-lock fs-4"></i></span>
                                <input type="password" class="form-control" name="new_password" 
                                       placeholder="New Password" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-lock-fill fs-4"></i></span>
                                <input type="password" class="form-control" name="new_password_confirmation" 
                                       placeholder="Confirm New Password" required>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-check-circle me-2 fs-4"></i>Update Password
                            </button>
                        </div>
                    </form>
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
.input-group-text { 
    background: var(--background); 
    border-right: none;
    padding: 0.8rem 1.2rem;
}
.input-group .form-control { 
    border-left: none;
    padding: 0.8rem 1.2rem;
    font-size: 1.1rem;
}
.input-group:focus-within .input-group-text { 
    border-color: var(--primary); 
    color: var(--primary);
}
.input-group:focus-within .form-control { 
    border-color: var(--primary);
}

.profile-card {
    background: linear-gradient(145deg, #f6f8ff 0%, #f1f5ff 100%);
    border-left: 4px solid var(--primary);
}

.password-card {
    background: linear-gradient(145deg, #fff5f5 0%, #fff0f0 100%);
    border-left: 4px solid var(--danger);
}

.profile-card .info-item {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(var(--primary-rgb), 0.1);
}

.password-card .input-group-text {
    background: rgba(255, 255, 255, 0.7);
    border-color: rgba(var(--danger-rgb), 0.2);
}

.password-card .form-control {
    border-color: rgba(var(--danger-rgb), 0.2);
    background: rgba(255, 255, 255, 0.7);
}

.password-card .input-group:focus-within .input-group-text {
    border-color: var(--danger);
    color: var(--danger);
}

.password-card .input-group:focus-within .form-control {
    border-color: var(--danger);
}

.password-card .btn-primary {
    background: var(--danger);
    border-color: var(--danger);
}

.password-card .btn-primary:hover {
    background: var(--danger-dark);
    border-color: var(--danger-dark);
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