@extends('layouts.auth')

@push('styles')
<style>
.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    color: var(--text);
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
}

.input-wrapper {
    position: relative;
}

.input-wrapper i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
    font-size: 18px;
}

.input-wrapper .toggle-password {
    left: auto;
    right: 16px;
    cursor: pointer;
    transition: color 0.2s;
}

.input-wrapper .toggle-password:hover {
    color: var(--primary);
}

.form-control {
    width: 100%;
    padding: 14px 16px 14px 46px;
    font-size: 15px;
    color: var(--text);
    background: var(--background);
    border: 2px solid #E2E8F0;
    border-radius: 12px;
    transition: all 0.2s;
}

.form-control:focus {
    border-color: var(--primary);
    background: white;
    outline: none;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.1);
}

.form-control.is-invalid {
    border-color: #EF4444;
}

.invalid-feedback {
    display: block;
    color: #EF4444;
    font-size: 13px;
    margin-top: 6px;
}

.form-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 8px;
}

.remember-me input[type="checkbox"] {
    width: 18px;
    height: 18px;
    border: 2px solid #E2E8F0;
    border-radius: 4px;
    cursor: pointer;
    accent-color: var(--primary);
}

.remember-me label {
    color: var(--text-light);
    font-size: 14px;
    cursor: pointer;
}

.forgot-link {
    color: var(--primary);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s;
}

.forgot-link:hover {
    color: var(--primary-light);
}

.login-button {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    font-size: 16px;
    font-weight: 600;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.login-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
}

.login-button i {
    font-size: 20px;
}

.divider {
    text-align: center;
    margin: 24px 0;
    position: relative;
}

.divider::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    width: 100%;
    height: 1px;
    background: #E2E8F0;
}

.divider span {
    position: relative;
    background: white;
    padding: 0 16px;
    color: var(--text-light);
    font-size: 14px;
}

.social-buttons {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}

.social-button {
    padding: 12px;
    background: white;
    border: 2px solid #E2E8F0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.social-button:hover {
    border-color: var(--primary);
    background: #F8FAFC;
}

.social-button i {
    font-size: 20px;
    color: var(--text);
}

.register-prompt {
    text-align: center;
    color: var(--text-light);
    font-size: 14px;
}

.register-link {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
}

.register-link:hover {
    text-decoration: underline;
}
</style>
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="brand-area">
            <img src="{{ asset('images/logo.png') }}" alt="MedCare Logo" class="auth-logo">
            <h1>Welcome Back!</h1>
            <p>Sign in your account to continue</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" 
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" 
                           placeholder="Enter your email"
                           required autofocus>
                </div>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" 
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Enter your password"
                           required>
                    <i class="bi bi-eye-slash toggle-password"></i>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" name="remember" id="remember" 
                           {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Remember me</label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit" class="login-button">
                Sign In
                <i class="bi bi-arrow-right-circle"></i>
            </button>

            <p class="register-prompt">
                Don't have an account? 
                <a href="#" class="register-link">Please contact our administrator</a>
            </p>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelector('.toggle-password').addEventListener('click', function() {
    const passwordInput = this.previousElementSibling;
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.classList.toggle('bi-eye');
    this.classList.toggle('bi-eye-slash');
});
</script>
@endpush
@endsection
