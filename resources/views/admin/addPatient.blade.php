@extends('admin.layout')
@section('title', 'Add Patient')
@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <h2 class="text-gradient mb-0">Add New Patient</h2>
            </div>
            <p class="text-muted-light">
                <i class="fa-solid fa-bed-pulse me-2"></i>
                Fill in the details to add a new patient
            </p>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('addNewPatient') }}">
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
                <input type="hidden" name="role" value="patient">

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
                    <a href="{{ route('patientList') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-bed-pulse"></i>
                        Add Patient
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
