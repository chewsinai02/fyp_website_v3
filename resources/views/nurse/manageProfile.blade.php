@extends('nurse.layout')
@section('title', 'My Profile')
@section('content')
<div class="container-fluid vh-100 p-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-gradient mb-2">My Profile</h2>
            <p class="text-muted-light">
                <i class="bi bi-person-circle me-2"></i>
                Manage your profile and account settings
            </p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center p-4">
                    <div class="position-relative mb-4 mx-auto" style="width: 120px;">
                        <img src="{{ asset($user->profile_picture) }}" 
                             alt="Profile Picture" 
                             class="rounded-circle border border-2 border-light shadow-sm"
                             style="width: 120px; height: 120px; object-fit: cover;">
                        <div class="position-absolute bottom-0 end-0">
                            <span class="badge rounded-circle bg-success p-2">
                                <i class="bi bi-check-lg"></i>
                            </span>
                        </div>
                    </div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                        <i class="bi bi-shield-fill me-1"></i>{{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Settings Card -->
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-body p-4">
                    <h5 class="mb-4">Account Settings</h5>
                    
                    <!-- Change Password -->
                    <a href="{{ route('nurse.changePassword') }}" 
                       class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3 text-decoration-none settings-link">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-key-fill fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-0">Change Password</h6>
                                <small class="text-muted">Update your account password</small>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <!-- Edit Profile -->
                    <a href="{{ route('nurse.editProfile') }}" 
                       class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3 text-decoration-none settings-link">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-fill-gear fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-0">Edit Profile</h6>
                                <small class="text-muted">Update your personal information</small>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <!-- Logout -->
                    <a href="#" onclick="confirmLogout(event)" 
                       class="d-flex align-items-center justify-content-between p-3 rounded-3 text-decoration-none settings-link text-danger">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-box-arrow-right fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-0">Logout</h6>
                                <small class="text-muted">Sign out of your account</small>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,.1);
}
.text-gradient {
    background: linear-gradient(135deg,var(--primary),var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.settings-link {
    color: var(--text);
    transition: all 0.3s ease;
}
.settings-link:hover {
    background-color: var(--background);
    transform: translateX(5px);
}
.settings-link.text-danger:hover {
    background-color: #fff5f5;
}
.badge.bg-primary-subtle {
  background-color: #818CF8 !important;   /* Indigo */
  color: #312E81 !important;  
}
</style>

<script>
function confirmLogout(event) {
    event.preventDefault();
    if (confirm("Are you sure you want to log out?")) {
        document.getElementById("logout-form").submit();
    }
}
</script>
@endsection