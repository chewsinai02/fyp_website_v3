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
    display: none;
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
}

#reset-email.is-invalid {
    border-color: #dc3545;
}

#reset-email.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
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

.swal2-show-input-spacing .swal2-html-container {
    margin: 1em 1.6em;
}

.swal2-popup input.form-control {
    margin-bottom: 0.5rem;
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
                <a href="mailto:chewsinai2002@gmail.com" class="register-link">Please contact our administrator</a>
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

document.querySelector('.forgot-link').addEventListener('click', function(e) {
    e.preventDefault();
    
    let userEmail = ''; // Store email for later use
    
    Swal.fire({
        title: 'Reset Password',
        html: `
            <div class="mb-3">
                <input type="email" id="reset-email" class="form-control" placeholder="Enter your email" required>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Send Reset Link',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                const email = document.getElementById('reset-email').value.trim();
                userEmail = email; // Store email
                
                if (!email) {
                    throw new Error('Please enter your email address');
                }

                const response = await fetch('{{ route("password.email") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Something went wrong');
                }
                
                return true;
            } catch (error) {
                Swal.showValidationMessage(error.message);
                return false;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show OTP input dialog
            Swal.fire({
                title: 'Enter OTP',
                text: 'Please check your email for the OTP code',
                html: `
                    <div class="mb-3">
                        <input type="text" id="otp-input" class="form-control" placeholder="Enter 6-digit OTP" maxlength="6" required>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Verify OTP',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                didOpen: () => {
                    document.getElementById('otp-input').focus();
                },
                preConfirm: async () => {
                    try {
                        const otp = document.getElementById('otp-input').value;
                        
                        if (!otp) {
                            throw new Error('Please enter the OTP');
                        }

                        const response = await fetch('{{ route("password.verify") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ email: userEmail, otp })
                        });

                        const data = await response.json();
                        console.log('OTP Verification Response:', data);
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Invalid OTP');
                        }
                        
                        return true;
                    } catch (error) {
                        console.error('OTP Verification Error:', error);
                        Swal.showValidationMessage(error.message);
                        return false;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show new password input dialog
                    Swal.fire({
                        title: 'Set New Password',
                        html: `
                            <div style="margin-bottom: 1rem;">
                                <input type="password" id="new-password" class="form-control" placeholder="New password" required>
                            </div>
                            <div>
                                <input type="password" id="confirm-password" class="form-control" placeholder="Confirm password" required>
                            </div>
                        `,
                        customClass: {
                            popup: 'swal2-show-input-spacing'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Reset Password',
                        showLoaderOnConfirm: true,
                        allowOutsideClick: false,
                        didOpen: () => {
                            document.getElementById('new-password').focus();
                        },
                        preConfirm: async () => {
                            try {
                                const password = document.getElementById('new-password').value;
                                const password_confirmation = document.getElementById('confirm-password').value;

                                if (!password) {
                                    throw new Error('Please enter a new password');
                                }

                                if (password.length < 6) {
                                    throw new Error('Password must be at least 6 characters long');
                                }

                                if (password !== password_confirmation) {
                                    throw new Error('Passwords do not match');
                                }
                                
                                const response = await fetch('{{ route("password.update") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ 
                                        email: userEmail, 
                                        password,
                                        password_confirmation
                                    })
                                });

                                const data = await response.json();
                                console.log('Password Reset Response:', data);
                                
                                if (!response.ok) {
                                    throw new Error(data.message || 'Failed to reset password');
                                }
                                
                                return true;
                            } catch (error) {
                                console.error('Password Reset Error:', error);
                                Swal.showValidationMessage(error.message);
                                return false;
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Password has been reset successfully',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    });
                }
            });
        }
    });
});
</script>
@endpush
@endsection
