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
                    <span style="font-size: 10px; color: #777777; font-weight: normal; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px;">CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</span>
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

    @include('lms.footer')

    <!-- Floating WhatsApp Support Button -->
    <a href="https://wa.me/447000000000" target="_blank" class="whatsapp-float-btn" aria-label="Chat with Support on WhatsApp">
        <svg viewBox="0 0 24 24" class="whatsapp-icon" xmlns="http://www.w3.org/2000/svg">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.717-1.456L0 24zm6.59-4.846c1.6.95 3.498 1.45 5.424 1.451 5.513 0 10.002-4.489 10.005-10.003.002-2.67-1.036-5.182-2.924-7.072-1.888-1.89-4.403-2.93-7.079-2.931-5.519 0-10.01 4.49-10.014 10.004-.002 1.933.504 3.82 1.465 5.433l-.963 3.52 3.606-.946zm11.517-7.234c-.303-.152-1.793-.884-2.071-.985-.278-.101-.48-.152-.682.152-.202.304-.783.985-.96 1.187-.178.203-.355.228-.658.076-1.218-.61-2.185-1.066-3.045-2.545-.228-.393.228-.364.65-.183.125.038.25.076.353.127.303.152.329.253.481.557.152.304.076.582-.038.81-.114.228-.682 1.088-.86 1.392-.177.304-.367.33-.67.177-1.23-.615-2.193-1.077-3.056-2.57-.23-.4-.015-.62.196-.827.19-.187.354-.354.48-.53.127-.178.189-.253.253-.405.064-.152.03-.304-.015-.405-.045-.101-.405-1.088-.557-1.443-.152-.354-.304-.304-.43-.304l-.38-.013c-.152-.002-.38.053-.582.253-.202.203-.783.76-1.063 1.342-.28.582-.81 1.747-.81 3.544 0 1.797 1.316 3.544 1.493 3.797.177.253 2.592 3.96 6.28 5.556.88.38 1.56.607 2.09.775.88.28 1.68.24 2.3.15.7-.1 2.07-.84 2.37-1.67.3-.83.3-1.545.21-1.696-.09-.15-.303-.228-.606-.38z"/>
        </svg>
        <span class="whatsapp-tooltip">Chat with Support</span>
    </a>

    @yield('scripts_extra')
</body>
</html>
