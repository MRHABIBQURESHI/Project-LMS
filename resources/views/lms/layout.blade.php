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
                <span class="gov-header-title">CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</span>
            </a>
            <div class="gov-header-nav">
                @if (session()->has('user_id'))
                    <a href="{{ route('lms.dashboard') }}">Dashboard</a>
                    <a href="{{ route('lms.logout') }}">Sign out</a>
                @else
                    <a href="{{ route('lms.login') }}">Sign in</a>
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
