<x-panel.layout>
    <!-- ===== DASHBOARD ===== -->
    <section class="content-section active" id="dashboard">
        <div class="page-title">Dashboard</div>
        <div class="page-subtitle">Aap ke website ka overview, ek nazar mein.</div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#6c5ce71a;color:#6c5ce7;"><i
                            class="bi bi-file-earmark-text"></i></div>
                    <div>
                        <div class="stat-value">24</div>
                        <div class="stat-label">Total Pages</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#22c55e1a;color:#22c55e;"><i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="stat-value">1,284</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f59e0b1a;color:#f59e0b;"><i class="bi bi-eye"></i>
                    </div>
                    <div>
                        <div class="stat-value">38.2k</div>
                        <div class="stat-label">Page Views (30d)</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ef44441a;color:#ef4444;"><i
                            class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="stat-value">3</div>
                        <div class="stat-label">Pending Drafts</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-panel">
            <div class="card-panel-title">Recent Activity</div>
            <div class="page-subtitle mb-3">Aakhri updates jo site par hue.</div>
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Page</th>
                        <th>By</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Updated content</td>
                        <td>Home Page</td>
                        <td>Faham</td>
                        <td>2 hours ago</td>
                    </tr>
                    <tr>
                        <td>Published</td>
                        <td>About Us</td>
                        <td>Faham</td>
                        <td>Yesterday</td>
                    </tr>
                    <tr>
                        <td>New user registered</td>
                        <td>—</td>
                        <td>System</td>
                        <td>2 days ago</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- ===== CONTENT / PAGES ===== -->
    <section class="content-section" id="content">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <div class="page-title">Pages / Content</div>
                <div class="page-subtitle mb-0">Website ke pages add, edit ya remove karein.</div>
            </div>
            <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#contentModal">
                <i class="bi bi-plus-lg me-1"></i> Add New
            </button>
        </div>

        <div class="card-panel">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="contentTableBody">
                    <tr>
                        <td>Home Page</td>
                        <td class="text-muted">/</td>
                        <td><span class="badge-status badge-published">Published</span></td>
                        <td>2 hours ago</td>
                        <td class="text-end">
                            <button class="row-action-btn"><i class="bi bi-pencil"></i></button>
                            <button class="row-action-btn delete" onclick="deleteRow(this)"><i
                                    class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>About Us</td>
                        <td class="text-muted">/about</td>
                        <td><span class="badge-status badge-published">Published</span></td>
                        <td>Yesterday</td>
                        <td class="text-end">
                            <button class="row-action-btn"><i class="bi bi-pencil"></i></button>
                            <button class="row-action-btn delete" onclick="deleteRow(this)"><i
                                    class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Services</td>
                        <td class="text-muted">/services</td>
                        <td><span class="badge-status badge-draft">Draft</span></td>
                        <td>3 days ago</td>
                        <td class="text-end">
                            <button class="row-action-btn"><i class="bi bi-pencil"></i></button>
                            <button class="row-action-btn delete" onclick="deleteRow(this)"><i
                                    class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Contact</td>
                        <td class="text-muted">/contact</td>
                        <td><span class="badge-status badge-published">Published</span></td>
                        <td>1 week ago</td>
                        <td class="text-end">
                            <button class="row-action-btn"><i class="bi bi-pencil"></i></button>
                            <button class="row-action-btn delete" onclick="deleteRow(this)"><i
                                    class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- ===== MEDIA ===== -->
    <section class="content-section" id="media">
        <div class="page-title">Media Library</div>
        <div class="page-subtitle">Images aur files jo website par use hoti hain.</div>
        <div class="card-panel text-center py-5 text-muted">
            <i class="bi bi-cloud-upload" style="font-size:2rem;"></i>
            <p class="mt-2 mb-0">Drag & drop files yahan, ya click karke upload karein.</p>
        </div>
    </section>

    <!-- ===== USERS ===== -->
    <section class="content-section" id="users">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <div class="page-title">Users</div>
                <div class="page-subtitle mb-0">Admin panel access rakhne wale users.</div>
            </div>
            <button class="btn btn-accent"><i class="bi bi-plus-lg me-1"></i> Add User</button>
        </div>
        <div class="card-panel">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Muhammad Faham</td>
                        <td class="text-muted">faham@example.com</td>
                        <td>Admin</td>
                        <td><span class="badge-status badge-published">Active</span></td>
                        <td class="text-end">
                            <button class="row-action-btn"><i class="bi bi-pencil"></i></button>
                            <button class="row-action-btn delete" onclick="deleteRow(this)"><i
                                    class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Sara Khan</td>
                        <td class="text-muted">sara@example.com</td>
                        <td>Editor</td>
                        <td><span class="badge-status badge-published">Active</span></td>
                        <td class="text-end">
                            <button class="row-action-btn"><i class="bi bi-pencil"></i></button>
                            <button class="row-action-btn delete" onclick="deleteRow(this)"><i
                                    class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- ===== SETTINGS ===== -->
    <section class="content-section" id="settings">
        <div class="page-title">Settings</div>
        <div class="page-subtitle">Website ki basic settings update karein.</div>
        <div class="card-panel" style="max-width:600px;">
            <form onsubmit="saveSettings(event)">
                <div class="mb-3">
                    <label class="form-label">Site Title</label>
                    <input type="text" class="form-control" value="My Website">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tagline</label>
                    <input type="text" class="form-control" value="Building things that matter.">
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Email</label>
                    <input type="email" class="form-control" value="contact@example.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">Maintenance Mode</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="maintenanceSwitch">
                        <label class="form-check-label" for="maintenanceSwitch">Site ko maintenance mode mein
                            daalein</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-accent">Save Changes</button>
            </form>
        </div>
    </section>
</x-panel.layout>