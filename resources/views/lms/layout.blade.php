<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    @yield('head_extra')
</head>
<body class="@yield('body_class')">

    @section('header')
    <header class="gov-header">
        <div class="gov-header-container">
            <a href="{{ route('lms.home') }}" style="display:flex; align-items:center; gap:12px; text-decoration:none;">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-height: 40px; object-fit: contain;">
                <div style="display: flex; flex-direction: column;">
                    <span class="gov-header-title" style="line-height: 1.1;">CPD UK LONDON REGISTRY</span>
                    <span style="font-size: 10px; color: rgba(255, 255, 255, 0.75); font-weight: normal; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px;">CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</span>
                </div>
            </a>
            <div class="gov-header-nav">
                @if (session()->has('user_id'))
                    <a href="{{ route('lms.dashboard') }}" class="nav-btn-primary">Dashboard</a>
                    <a href="{{ route('lms.logout') }}" class="nav-btn-outline">Sign out</a>
                @else
                    <a href="{{ route('lms.register') }}" class="nav-btn-outline">Sign up</a>
                    <a href="{{ route('lms.login') }}" class="nav-btn-primary">Sign in</a>
                @endif
            </div>
        </div>
    </header>
    @show

    <main class="@yield('main_class', 'gov-width-container')">
        @yield('content')
    </main>

    @yield('scripts_extra')
</body>
</html>
