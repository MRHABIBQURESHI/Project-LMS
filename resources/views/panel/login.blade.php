<x-panel.auth-layout>
    <x-slot:title>Login - Dreams Propertys Admin</x-slot:title>

    <style>
        /* Custom styles for premium look & feel in Bootstrap */
        .login-card {
            max-width: 1300px;
            width: 95%;
            height: 90vh;
            min-height: 650px;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            position: relative;
        }

        .ambient-light-1 {
            position: absolute;
            top: -20%;
            left: -20%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.08);
            filter: blur(120px);
            pointer-events: none;
            z-index: 1;
        }

        .ambient-light-2 {
            position: absolute;
            bottom: -20%;
            right: -20%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.08);
            filter: blur(120px);
            pointer-events: none;
            z-index: 1;
        }

        .visual-pane {
            position: relative;
            height: 100%;
            overflow: hidden;
        }

        .visual-bg {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            transition: transform 10s ease;
        }

        .visual-pane:hover .visual-bg {
            transform: scale(1.06);
        }

        .visual-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, #090d16 5%, rgba(9, 13, 22, 0.4) 50%, rgba(9, 13, 22, 0.1) 100%),
                linear-gradient(to right, rgba(9, 13, 22, 0.1) 0%, rgba(9, 13, 22, 0.5) 100%);
        }

        .glass-card {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .brand-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .gradient-text-emerald {
            background: linear-gradient(to right, #34d399, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .custom-form-control {
            background-color: rgba(9, 13, 22, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #fff !important;
            padding: 14px 16px 14px 44px;
            border-radius: 14px;
            transition: all 0.3s ease;
        }

        .custom-form-control:focus {
            background-color: rgba(9, 13, 22, 0.7);
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
            outline: 0;
        }

        .custom-form-control::placeholder {
            color: #4b5563;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #6b7280;
            cursor: pointer;
            z-index: 10;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password-btn:hover {
            color: #d1d5db;
        }

        /* Custom Checkbox */
        .custom-checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
            font-size: 0.9rem;
            color: #9ca3af;
        }

        .custom-checkbox-input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .custom-checkbox-box {
            height: 20px;
            width: 20px;
            background-color: rgba(9, 13, 22, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .custom-checkbox-input:checked~.custom-checkbox-box {
            background-color: #10b981;
            border-color: #10b981;
        }

        .custom-checkbox-box i {
            display: none;
            color: #090d16;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .custom-checkbox-input:checked~.custom-checkbox-box i {
            display: block;
        }

        /* Custom Alert */
        .alert-custom-danger {
            background-color: rgba(244, 63, 94, 0.08);
            border: 1px solid rgba(244, 63, 94, 0.2);
            color: #f87171;
            border-radius: 14px;
            font-size: 0.875rem;
        }

        .alert-custom-success {
            background-color: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #34d399;
            border-radius: 14px;
            font-size: 0.875rem;
        }

        /* Submit Button */
        .btn-submit-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            color: #090d16;
            font-weight: 600;
            padding: 14px;
            border-radius: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.15);
            width: 100%;
        }

        .btn-submit-gradient:hover {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.35);
            color: #090d16;
        }

        .btn-submit-gradient:active {
            transform: translateY(0);
        }

        .btn-submit-gradient:disabled {
            background: rgba(16, 185, 129, 0.3);
            cursor: not-allowed;
            color: rgba(9, 13, 22, 0.5);
            transform: none;
            box-shadow: none;
        }
    </style>

    <div class="login-card d-flex flex-column flex-md-row">
        <!-- Ambient Decorative Lighting Elements -->
        <div class="ambient-light-1"></div>
        <div class="ambient-light-2"></div>

        <!-- Left Column: Media Panel (Hidden on Mobile) -->
        <div class="col-md-6 d-none d-md-flex flex-column justify-content-between p-5 visual-pane z-3">
            <div class="visual-bg"></div>
            <div class="visual-overlay"></div>

            <!-- Top Header Branding -->
            <div class="position-relative z-3 d-flex align-items-center gap-3">
                <div class="brand-icon-wrapper">
                    <i class="bi bi-house-heart-fill fs-5 text-dark"></i>
                </div>
                <span class="font-display text-white fs-4 fw-bold">Dreams <span
                        class="gradient-text-emerald">Propertys</span></span>
            </div>

            <!-- Bottom Content Overlay -->
            <div class="position-relative z-3 mt-auto space-y-4">
                <div class="mb-4">
                    <span
                        class="text-uppercase text-emerald-400 fw-bold tracking-wider fs-7 d-block mb-1">Administrative
                        Panel</span>
                    <h1 class="font-display text-white fw-bold lh-sm display-6">
                        Manage Premium Properties Seamlessly.
                    </h1>
                </div>
            </div>
        </div>

        <!-- Right Column: Login Card -->
        <div class="col-12 col-md-6 d-flex flex-column justify-content-between p-4 p-sm-5 p-md-5 z-3">

            <!-- Mobile Logo -->
            <div class="d-flex d-md-none align-items-center gap-2 mb-4">
                <div class="brand-icon-wrapper" style="width: 34px; height: 34px; border-radius: 8px;">
                    <i class="bi bi-house-heart-fill fs-6 text-dark"></i>
                </div>
                <span class="font-display text-white fs-5 fw-bold">Dreams <span
                        class="gradient-text-emerald">Propertys</span></span>
            </div>

            <!-- Login Form block -->
            <div class="my-auto mx-auto w-100" style="max-width: 400px;">
                <div class="mb-4">
                    <h2 class="text-white fw-bold font-display mb-1">Welcome Back</h2>
                    <p class="text-secondary small">Enter your credentials to manage properties.</p>
                </div>

                <!-- General Session Alerts -->
                @if (session('status'))
                    <div class="alert alert-custom-success p-3 border-0 d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if ($errors->has('login_error'))
                    <div class="alert alert-custom-danger p-3 border-0 d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                        <div>{{ $errors->first('login_error') }}</div>
                    </div>
                @endif

                <!-- Login form submits to route panel.login.submit -->
                <form method="POST" action="{{ route('login.submit') }}" class="needs-validation" id="loginForm">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email"
                            class="form-label text-uppercase text-secondary font-semibold small tracking-wider mb-1.5"
                            style="font-size: 11px;">Email Address</label>
                        <div class="position-relative">
                            <span class="input-icon">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                autocomplete="username" placeholder="name@example.com"
                                class="form-control custom-form-control {{ $errors->has('email') ? 'is-invalid' : '' }}">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1.5 d-flex align-items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <label for="password"
                                class="form-label text-uppercase text-secondary font-semibold small tracking-wider mb-0"
                                style="font-size: 11px;">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-decoration-none small"
                                    style="font-size: 12px;">Forgot?</a>
                            @endif
                        </div>
                        <div class="position-relative">
                            <span class="input-icon">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password" placeholder="••••••••"
                                class="form-control custom-form-control {{ $errors->has('password') ? 'is-invalid' : '' }}">

                            <!-- Toggle visibility buttons -->
                            <button type="button" id="togglePassword" class="toggle-password-btn">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                                <i class="bi bi-eye-slash d-none" id="eyeSlashIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1.5 d-flex align-items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Device Checkbox -->
                    <div class="d-flex justify-content-between align-items-center mb-4 pt-1">
                        <label class="custom-checkbox-label">
                            <input type="checkbox" name="remember" class="custom-checkbox-input">
                            <div class="custom-checkbox-box">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <span>Remember device</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn"
                        class="btn btn-submit-gradient d-flex align-items-center justify-content-center gap-2">
                        <span id="btnText">Sign In to Panel</span>
                        <i class="bi bi-arrow-right" id="btnIcon"></i>
                        <span class="spinner-border spinner-border-sm text-dark d-none" id="btnSpinner"
                            role="status"></span>
                    </button>
                </form>
            </div>

            <!-- Footer Section -->
            <div class="text-center mt-4">
                <p class="text-secondary" style="font-size: 11px;">
                    Protected by secure layers. Authorized access only. <a href="#"
                        class="text-secondary text-decoration-underline hover-emerald">Security policy</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Password visibility and loading spinner JavaScript snippet -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');

            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            const btnSpinner = document.getElementById('btnSpinner');

            // Toggle password visibility
            if (togglePasswordBtn && passwordInput) {
                togglePasswordBtn.addEventListener('click', () => {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                    if (isPassword) {
                        eyeIcon.classList.add('d-none');
                        eyeSlashIcon.classList.remove('d-none');
                    } else {
                        eyeIcon.classList.remove('d-none');
                        eyeSlashIcon.classList.add('d-none');
                    }
                });
            }

            // Show spinner on form submit
            if (loginForm && submitBtn && btnSpinner && btnIcon && btnText) {
                loginForm.addEventListener('submit', (event) => {
                    // Check standard client side validation before showing loading state
                    if (loginForm.checkValidity()) {
                        submitBtn.disabled = true;
                        btnIcon.classList.add('d-none');
                        btnText.textContent = "Authenticating...";
                        btnSpinner.classList.remove('d-none');
                    }
                });
            }
        });
    </script>
</x-panel.auth-layout>