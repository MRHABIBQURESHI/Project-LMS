@extends('lms.layout')

@section('title', 'Reset Password - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')
@section('body_class', 'login-body')
@section('main_class', '')

@section('header')
<!-- Hide default header matching design -->
@endsection

@section('content')
<div class="login-split-container">
    
    <!-- Left Panel: Glassmorphism Card -->
    <div class="login-left-panel">
        <div class="login-card">
            
            <div style="margin-bottom: 25px; text-align: left;">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-height: 48px; object-fit: contain;">
            </div>
            <h1 class="login-title" style="font-size: 24px;">New Password</h1>

            @if (!empty($success))
                <div class="gov-success-banner" style="padding: 12px; margin-bottom: 25px;">
                    <div class="gov-success-title">Password Updated</div>
                    <p style="font-size: 13px; margin-bottom: 0;">Your password has been successfully reset. Proceed to login.</p>
                </div>

                <a href="{{ route('lms.login') }}" class="gov-button" style="width: 100%; display:block; text-align:center; text-decoration:none;">Go to Sign In</a>

            @else

                <p style="font-size: 14px; margin-bottom: 20px; color: #555;">Choose a new secure password of at least 6 characters.</p>

                @if (!empty($error))
                    <div class="gov-error-banner" style="padding: 10px 15px; margin-bottom: 20px;">
                        <p style="font-size: 13px; margin-bottom: 0; color:#d4351c; font-weight: 600;">{{ $error }}</p>
                    </div>
                @endif

                @if ($valid_request)
                    <form action="{{ route('lms.reset-password', ['email' => $email, 'token' => $token]) }}" method="POST" novalidate>
                         @csrf
                         <div class="gov-form-group">
                             <label class="gov-label" for="password">New Password</label>
                             <div class="pw-wrapper">
                                 <input class="gov-input" id="password" name="password" type="password" required placeholder="Min 6 characters">
                                 <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('password', this)" aria-label="Show password">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                 </button>
                             </div>
                         </div>

                         <div class="gov-form-group" style="margin-bottom: 25px;">
                             <label class="gov-label" for="confirm_password">Confirm New Password</label>
                             <div class="pw-wrapper">
                                 <input class="gov-input" id="confirm_password" name="confirm_password" type="password" required placeholder="Re-type password">
                                 <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('confirm_password', this)" aria-label="Show password">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                 </button>
                             </div>
                         </div>

                         <button type="submit" class="gov-button">Update and Save Password</button>
                    </form>
                @endif

            @endif

        </div>
    </div>

    <!-- Right Panel: Decorative Illustration (Hidden on mobile) -->
    <div class="login-right-panel">
        <img class="login-illustration-img" src="{{ asset('assets/images/logo.png') }}" alt="CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD Logo">
    </div>

</div>
@endsection

@section('scripts_extra')
<script>
    function togglePasswordVisibility(inputId, buttonEl) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        if (type === 'password') {
            buttonEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            buttonEl.setAttribute('aria-label', 'Show password');
        } else {
            buttonEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            buttonEl.setAttribute('aria-label', 'Hide password');
        }
    }
</script>
@endsection
