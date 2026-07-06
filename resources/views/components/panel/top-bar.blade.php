<!-- Topbar -->
<div class="topbar">
    <button class="toggle-btn" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>

    <div class="search-box">
        <i class="bi bi-search text-muted"></i>
        <input type="text" placeholder="Search pages, users...">
    </div>

    <div class="ms-auto d-flex align-items-center gap-2">
        <button class="icon-btn"><i class="bi bi-bell"></i><span class="dot"></span></button>
        <div class="dropdown">
            <div class="user-chip" data-bs-toggle="dropdown">
                <div class="avatar">F</div>
                <div class="d-none d-sm-block">
                    <div style="font-size:.85rem;font-weight:600;">Faham</div>
                    <div style="font-size:.72rem;color:var(--text-muted);">Admin</div>
                </div>
                <i class="bi bi-chevron-down text-muted" style="font-size:.7rem;"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </li>
            </ul>
        </div>
    </div>
</div>