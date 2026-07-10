@extends('lms.layout')

@section('title', 'Forgot Password - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')
@section('body_class', 'login-body')
@section('main_class', '')

@section('header')
<!-- Hide default header matching design -->
@endsection

@section('head_extra')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Global alert override with SweetAlert2
    window.alert = function(message) {
        var isSuccess = /success|complete|confirmed|verified|approved/i.test(message);
        Swal.fire({
            icon: isSuccess ? 'success' : 'warning',
            title: isSuccess ? 'Confirmation' : 'Registry Notice',
            text: message,
            confirmButtonColor: '#002F6C'
        });
    };
</script>
@endsection

@section('content')
<div class="login-split-container">
    
    <!-- Left Panel: Glassmorphism Card -->
    <div class="login-left-panel">
        <div class="login-card">
            
            <!-- Mobile Logo Branding Header -->
            <div class="mobile-logo-header">
                <img src="{{ asset('assets/images/logo.png') }}" alt="CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD Logo">
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                <a href="{{ route('lms.home') }}" style="font-size:13px; font-weight:600; color:#002F6C; text-decoration:none; display:flex; align-items:center; gap:5px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Back to Home
                </a>
            </div>

            <h1 class="login-title" style="font-size: 24px;">Reset Password</h1>
            <p style="font-size: 14px; margin-bottom: 20px; color: #555;">Enter your registered email address and we will generate a password reset verification link.</p>

            @if (!empty($success))
                <div class="gov-success-banner" style="padding: 12px; margin-bottom: 20px;">
                    <p style="font-size: 13px; margin-bottom: 0; color:#00703c; font-weight: 600;">{{ $success }}</p>
                    @if (!empty($reset_link))
                        <p style="margin-top: 15px; font-size: 13px; margin-bottom: 0;">
                            [Local Test Link]:<br>
                            <a href="{{ $reset_link }}" class="gov-button" style="padding: 8px 16px; font-size: 13px; margin-top: 8px; border-radius: 4px; display: inline-block;">Reset Password Now</a>
                        </p>
                    @endif
                </div>
            @endif

            @if (!empty($error))
                <div class="gov-error-banner" style="padding: 10px 15px; margin-bottom: 20px;">
                    <p style="font-size: 13px; margin-bottom: 0; color:#d4351c; font-weight: 600;">{{ $error }}</p>
                </div>
            @endif

            <form action="{{ route('lms.forgot-password') }}" method="POST" novalidate>
                @csrf
                <div class="gov-form-group" style="margin-bottom: 25px;">
                    <label class="gov-label" for="email">Student Email</label>
                    <input class="gov-input" id="email" name="email" type="email" autocomplete="email" required placeholder="Enter email" value="{{ request()->input('email', '') }}">
                </div>

                <button type="submit" class="gov-button">Generate Verification Link</button>
            </form>

        </div>
    </div>

    <!-- Right Panel: Decorative Illustration (Hidden on mobile) -->
    <div class="login-right-panel">
        <img class="login-illustration-img" src="{{ asset('assets/images/logo.png') }}" alt="CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD Logo">
    </div>

</div>
@endsection
