@php
    $isDashboard = request()->routeIs('panel.dashboard');
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-dot">DP</div>
        <div class="brand-text">Dreams Properties</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        
        <a class="nav-link-item {{ $isDashboard && request()->query('section', 'dashboard') === 'dashboard' ? 'active' : '' }}" 
           href="{{ $isDashboard ? 'javascript:void(0)' : url('panel?section=dashboard') }}" 
           data-section="dashboard" 
           onclick="{{ $isDashboard ? "showSection('dashboard', this)" : "" }}">
            <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>

        <!-- Properties CRUD (Always separate view) -->
        <a class="nav-link-item {{ request()->routeIs('panel.properties.*') ? 'active' : '' }}" 
           href="{{ route('panel.properties.index') }}">
            <i class="bi bi-building-fill"></i><span>Properties</span>
        </a>

        <a class="nav-link-item {{ $isDashboard && request()->query('section') === 'content' ? 'active' : '' }}" 
           href="{{ $isDashboard ? 'javascript:void(0)' : url('panel?section=content') }}" 
           data-section="content" 
           onclick="{{ $isDashboard ? "showSection('content', this)" : "" }}">
            <i class="bi bi-file-earmark-text"></i><span>Pages / Content</span>
        </a>

        <a class="nav-link-item {{ $isDashboard && request()->query('section') === 'media' ? 'active' : '' }}" 
           href="{{ $isDashboard ? 'javascript:void(0)' : url('panel?section=media') }}" 
           data-section="media" 
           onclick="{{ $isDashboard ? "showSection('media', this)" : "" }}">
            <i class="bi bi-images"></i><span>Media Library</span>
        </a>

        <div class="nav-section-label">Manage</div>
        
        <a class="nav-link-item {{ $isDashboard && request()->query('section') === 'users' ? 'active' : '' }}" 
           href="{{ $isDashboard ? 'javascript:void(0)' : url('panel?section=users') }}" 
           data-section="users" 
           onclick="{{ $isDashboard ? "showSection('users', this)" : "" }}">
            <i class="bi bi-people"></i><span>Users</span>
        </a>

        <a class="nav-link-item {{ $isDashboard && request()->query('section') === 'settings' ? 'active' : '' }}" 
           href="{{ $isDashboard ? 'javascript:void(0)' : url('panel?section=settings') }}" 
           data-section="settings" 
           onclick="{{ $isDashboard ? "showSection('settings', this)" : "" }}">
            <i class="bi bi-gear"></i><span>Settings</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('home') }}" class="nav-link-item" style="color: #a5f3fc;"><i class="bi bi-globe"></i><span>Visit Site</span></a>
        <a class="nav-link-item"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
    </div>
</aside>