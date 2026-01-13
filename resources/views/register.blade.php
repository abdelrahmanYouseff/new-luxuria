<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Create your Luxuria UAE account to start renting premium luxury cars in the United Arab Emirates. Join our exclusive community today." />
    <title>Register - Luxuria UAE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body style="background:#f8f9fa; margin:0; padding:0; font-family:'Inter', -apple-system, BlinkMacSystemFont, sans-serif;">
<div class="classic-login-container">
    <!-- Elegant Background -->
    <div class="login-background">
        <div class="bg-pattern"></div>
        <div class="bg-overlay"></div>
    </div>

    <!-- Main Login Section -->
    <div class="login-wrapper">
        <!-- Left Side - Branding -->
        <div class="branding-section">
            <div class="brand-content">
                <div class="logo-container">
                    <div class="luxury-logo">
                        <img src="/images_car/new-logo3.png" alt="Luxuria UAE" class="brand-logo">
                    </div>
                </div>
                <h1 class="company-name">LUXURIA</h1>
                <p class="company-tagline">Experience Luxury on Wheels</p>
                <div class="brand-features">
                    <div class="feature-item">
                        <i class="bi bi-shield-check"></i>
                        <span>Premium Service</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-award"></i>
                        <span>Luxury Fleet</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-geo-alt"></i>
                        <span>UAE Wide</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="form-section">
            <div class="form-container">
                <div class="form-header">
                    <h2>Create Account</h2>
                    <p>Join us and start your luxury journey</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="elegant-form">
                    @csrf

                    <!-- Name Field -->
                    <div class="form-field">
                        <label for="name" class="field-label">Full Name</label>
                        <div class="input-wrapper">
                            <i class="bi bi-person"></i>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="elegant-input"
                                   placeholder="Enter your full name"
                                   value="{{ old('name') }}"
                                   required
                                   autofocus>
                        </div>
                        @error('name')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="form-field">
                        <label for="email" class="field-label">Email Address</label>
                        <div class="input-wrapper">
                            <i class="bi bi-envelope"></i>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="elegant-input"
                                   placeholder="Enter your email address"
                                   value="{{ old('email') }}"
                                   required>
                        </div>
                        @error('email')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div class="form-field">
                        <label for="phone" class="field-label">Phone Number</label>
                        <div class="input-wrapper">
                            <i class="bi bi-telephone"></i>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   class="elegant-input"
                                   placeholder="0501234567"
                                   value="{{ old('phone') }}">
                        </div>
                        @error('phone')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Emirate Field -->
                    <div class="form-field">
                        <label for="emirate" class="field-label">Emirate</label>
                        <div class="input-wrapper">
                            <i class="bi bi-geo-alt"></i>
                            <select id="emirate" name="emirate" class="elegant-input" required>
                                <option value="" disabled selected>Select your emirate</option>
                                <option value="Abu Dhabi" {{ old('emirate') == 'Abu Dhabi' ? 'selected' : '' }}>Abu Dhabi</option>
                                <option value="Dubai" {{ old('emirate') == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                                <option value="Sharjah" {{ old('emirate') == 'Sharjah' ? 'selected' : '' }}>Sharjah</option>
                                <option value="Ajman" {{ old('emirate') == 'Ajman' ? 'selected' : '' }}>Ajman</option>
                                <option value="Umm Al Quwain" {{ old('emirate') == 'Umm Al Quwain' ? 'selected' : '' }}>Umm Al Quwain</option>
                                <option value="Ras Al Khaimah" {{ old('emirate') == 'Ras Al Khaimah' ? 'selected' : '' }}>Ras Al Khaimah</option>
                                <option value="Fujairah" {{ old('emirate') == 'Fujairah' ? 'selected' : '' }}>Fujairah</option>
                            </select>
                        </div>
                        @error('emirate')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address Field -->
                    <div class="form-field">
                        <label for="address" class="field-label">Address</label>
                        <div class="input-wrapper">
                            <i class="bi bi-house"></i>
                            <textarea id="address"
                                      name="address"
                                      class="elegant-input"
                                      placeholder="Enter your full address"
                                      rows="3"
                                      required>{{ old('address') }}</textarea>
                        </div>
                        @error('address')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-field">
                        <label for="password" class="field-label">Password</label>
                        <div class="input-wrapper">
                            <i class="bi bi-lock"></i>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="elegant-input"
                                   placeholder="Create a strong password"
                                   required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="eye-icon-password"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-field">
                        <label for="password_confirmation" class="field-label">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="bi bi-lock"></i>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="elegant-input"
                                   placeholder="Confirm your password"
                                   required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                <i class="bi bi-eye" id="eye-icon-confirmation"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn">
                        <span class="btn-text">Create Account</span>
                        <div class="btn-loader" style="display: none;">
                            <div class="spinner"></div>
                        </div>
                    </button>

                    <!-- Login Link -->
                    <div class="register-link">
                        <p>Already have an account?
                            <a href="{{ route('login') }}">Sign in here</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Classic Login Container */
.classic-login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Background */
.login-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.bg-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image:
        radial-gradient(circle at 25% 25%, rgba(26, 26, 26, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 75% 75%, rgba(45, 45, 45, 0.05) 0%, transparent 50%);
    background-size: 400px 400px, 600px 600px;
}

.bg-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.95) 100%);
}

/* Main Wrapper */
.login-wrapper {
    position: relative;
    z-index: 10;
    display: flex;
    width: 100%;
    max-width: 1200px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    animation: slideIn 0.8s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Branding Section */
.branding-section {
    flex: 1;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    padding: 60px 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.branding-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.brand-content {
    text-align: center;
    color: white;
    position: relative;
    z-index: 2;
}

.logo-container {
    margin-bottom: 30px;
}

.luxury-logo {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.brand-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: brightness(0) invert(1); /* Make logo white */
}

.company-name {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 10px;
    letter-spacing: 3px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.company-tagline {
    font-size: 1.1rem;
    font-weight: 300;
    margin-bottom: 40px;
    opacity: 0.9;
}

.brand-features {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 1rem;
    font-weight: 500;
}

.feature-item i {
    font-size: 1.2rem;
    opacity: 0.8;
}

/* Form Section */
.form-section {
    flex: 1.2;
    padding: 60px 50px;
    background: white;
    overflow-y: auto;
    max-height: 100vh;
}

.form-container {
    max-width: 450px;
    margin: 0 auto;
}

.form-header {
    text-align: center;
    margin-bottom: 40px;
}

.form-header h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 10px;
}

.form-header p {
    color: #666;
    font-size: 1rem;
    font-weight: 400;
}

/* Form Fields */
.form-field {
    margin-bottom: 25px;
}

.field-label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border: 2px solid transparent;
    border-radius: 12px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.input-wrapper:focus-within {
    border-color: #1a1a1a;
    background: white;
    box-shadow: 0 0 0 4px rgba(26, 26, 26, 0.1);
}

.input-wrapper i {
    padding: 0 15px;
    color: #1a1a1a;
    font-size: 1.1rem;
}

.elegant-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 16px 15px;
    font-size: 1rem;
    color: #333;
    outline: none;
    resize: none;
}

.elegant-input::placeholder {
    color: #999;
    font-weight: 400;
}

.elegant-input:focus {
    outline: none;
}

.password-toggle {
    background: none;
    border: none;
    padding: 0 15px;
    color: #666;
    cursor: pointer;
    transition: color 0.3s ease;
    font-size: 1.1rem;
}

.password-toggle:hover {
    color: #1a1a1a;
}

.field-error {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 8px;
    padding-left: 15px;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
    border: none;
    border-radius: 12px;
    padding: 16px;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
    margin-top: 20px;
}

.submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.submit-btn:hover::before {
    left: 100%;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(26, 26, 26, 0.4);
}

.btn-loader {
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Register Link */
.register-link {
    text-align: center;
    margin-top: 30px;
}

.register-link p {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

.register-link a {
    color: #1a1a1a;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.register-link a:hover {
    color: #2d2d2d;
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .login-wrapper {
        flex-direction: column;
        margin: 20px;
        max-width: none;
    }

    .branding-section {
        padding: 40px 30px;
    }

    .form-section {
        padding: 40px 30px;
    }

    .company-name {
        font-size: 2rem;
    }

    .luxury-logo {
        width: 60px;
        height: 60px;
    }

    .luxury-logo i {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    .branding-section {
        padding: 30px 20px;
    }

    .form-section {
        padding: 30px 20px;
    }

    .company-name {
        font-size: 1.8rem;
    }

    .form-header h2 {
        font-size: 1.5rem;
    }
}
</style>

<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const eyeIcon = document.getElementById('eye-icon-' + fieldId.replace('password_confirmation', 'confirmation'));

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.className = 'bi bi-eye-slash';
    } else {
        passwordInput.type = 'password';
        eyeIcon.className = 'bi bi-eye';
    }
}

// Form submission with loading state
document.querySelector('.elegant-form').addEventListener('submit', function(e) {
    const submitBtn = document.querySelector('.submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');

    // Show loading state
    btnText.style.display = 'none';
    btnLoader.style.display = 'flex';
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.8';
});

// Add smooth focus effects to inputs
document.querySelectorAll('.elegant-input').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
        this.parentElement.style.boxShadow = '0 0 0 4px rgba(26, 26, 26, 0.15)';
    });

    input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
        this.parentElement.style.boxShadow = '0 0 0 4px rgba(26, 26, 26, 0.1)';
    });
});

// Add subtle animations to form elements
document.addEventListener('DOMContentLoaded', function() {
    const formElements = document.querySelectorAll('.form-field, .submit-btn, .register-link');

    formElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';

        setTimeout(() => {
            element.style.transition = 'all 0.6s ease';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 100 + (index * 100));
    });
});

// Add hover effects to interactive elements
document.querySelectorAll('.register-link a').forEach(link => {
    link.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-1px)';
    });

    link.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
