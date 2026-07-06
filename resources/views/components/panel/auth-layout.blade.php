<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Dreams Propertys') }} - Admin Login</title>

    <!-- Google Fonts: Instrument Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --bs-body-bg: #090d16;
            --bs-body-color: #f8fafc;
            --bs-primary: #10b981;
            --bs-primary-rgb: 16, 185, 129;
            --bs-border-color: rgba(255, 255, 255, 0.08);
            --bs-link-color: #10b981;
            --bs-link-hover-color: #34d399;
        }
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
        }
        .font-display {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom animated grid background */
        .grid-bg {
            background-image: 
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.07) 0px, transparent 50%), 
                radial-gradient(at 100% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.05) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(59, 130, 246, 0.07) 0px, transparent 50%);
        }
    </style>
</head>
<body class="min-vh-100 grid-bg d-flex align-items-center justify-content-center overflow-x-hidden">
    {{ $slot }}
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
