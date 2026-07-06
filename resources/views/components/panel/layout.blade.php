<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} Admin Panel</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/panel.css') }}">
</head>

<body>
    <div class="overlay" id="overlay" onclick="closeMobileSidebar()"></div>

    <x-panel.side-bar />
    <!-- ===== MAIN AREA ===== -->
    <div class="main-area" id="mainArea">
        <x-panel.top-bar />
        <div class="page-content">
            {{ $slot }}
        </div>
    </div>

    <!-- ===== ADD CONTENT MODAL ===== -->
    <div class="modal fade" id="contentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form onsubmit="addContentRow(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="newTitle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" id="newSlug" placeholder="/example-page" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="newStatus">
                                <option value="Published">Published</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" rows="3"
                                placeholder="Page content yahan likhein..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-accent">Save Page</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/panel.js') }}"></script>
</body>

</html>