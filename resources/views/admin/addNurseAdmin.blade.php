@extends('admin.layout')
@section('title', 'Add Nurse Admin')
@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <h2 class="text-gradient mb-0">Add New Nurse Admin</h2>
            </div>
            <p class="text-muted-light">
                <i class="bi bi-person-badge me-2"></i>
                Fill in the details to add a new nurse administrator
            </p>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('addNewNurseAdmin') }}">
                @csrf

                <!-- Name -->
                <div class="form-group mb-4">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input id="name" type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" 
                               required autocomplete="name" autofocus>
                    </div>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Hidden Role Field -->
                <input type="hidden" name="role" value="nurse_admin">

                <!-- Staff ID -->
                <div class="form-group mb-4">
                    <label for="staff_id" class="form-label">Staff ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input id="staff_id" type="text" 
                               class="form-control @error('staff_id') is-invalid @enderror" 
                               name="staff_id" value="{{ old('staff_id') }}" 
                               required>
                    </div>
                    @error('staff_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Gender -->
                <div class="form-group mb-4">
                    <label for="gender" class="form-label">Gender</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                        <select id="gender" class="form-select" name="gender" required>
                            <option value="" disabled selected>Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input id="email" type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" 
                               required autocomplete="email">
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input id="password" type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group mb-4">
                    <label for="password-confirm" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input id="password-confirm" type="password" 
                               class="form-control" name="password_confirmation" 
                               required autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password-confirm')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('nurseAdminList') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-badge me-2"></i>
                        Add Nurse Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-label {
        font-weight: 500;
        color: var(--text);
        margin-bottom: 0.5rem;
    }

    .input-group-text {
        background: var(--background);
        border-right: none;
        color: var(--text-light);
    }

    .input-group .form-control,
    .input-group .form-select {
        border-left: none;
    }

    .input-group .form-control:focus,
    .input-group .form-select:focus {
        border-color: #e2e8f0;
        box-shadow: none;
    }

    .input-group:focus-within .input-group-text {
        border-color: var(--primary);
        color: var(--primary);
    }

    .input-group:focus-within .form-control,
    .input-group:focus-within .form-select {
        border-color: var(--primary);
    }

    .btn-outline-secondary {
        border-color: #e2e8f0;
        color: var(--text-light);
    }

    .btn-outline-secondary:hover {
        background-color: var(--background);
        color: var(--primary);
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 
                    0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .text-gradient {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const button = field.nextElementSibling;
        const icon = button.querySelector('i');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection
