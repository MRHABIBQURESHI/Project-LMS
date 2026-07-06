<x-panel.layout>
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div>
            <div class="page-title">Property Listings</div>
            <div class="page-subtitle mb-0">Manage all real estate properties on your website.</div>
        </div>
        <a href="{{ route('panel.properties.create') }}" class="btn btn-accent">
            <i class="bi bi-plus-lg me-1"></i> Call Now
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert"
            style="background-color: #d1e7dd; color: #0f5132;">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card-panel">
        <div class="table-responsive">
            <table class="table admin-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Title & Address</th>
                        <th>Price & Type</th>
                        <th>Details</th>
                        <th>Category</th>
                        <th>Featured</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($properties as $property)
                        <tr>
                            <td>
                                @if($property->image)
                                    <img src="{{ asset($property->image) }}" class="rounded shadow-sm"
                                        style="width: 60px; height: 45px; object-fit: cover;" alt="{{ $property->title }}">
                                @else
                                    <div class="rounded bg-light text-muted d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 60px; height: 45px; font-size: 0.75rem;">
                                        No Image
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $property->title }}</div>
                                <div class="text-muted small"><i
                                        class="bi bi-geo-alt-fill me-1 text-danger"></i>{{ $property->address }}</div>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: var(--accent);">${{ number_format($property->price) }}
                                </div>
                                <span
                                    class="badge rounded-pill bg-opacity-10 text-opacity-100 {{ $property->type == 'Rent' ? 'bg-info text-info' : 'bg-success text-success' }}"
                                    style="font-size: 0.7rem; padding: 0.25em 0.6em;">
                                    For {{ $property->type ?: 'Sale' }}
                                </span>
                            </td>
                            <td>
                                <div class="small text-muted">
                                    <span><strong>Bed:</strong> {{ $property->bedrooms }}</span> |
                                    <span><strong>Bath:</strong> {{ $property->bathrooms }}</span> |
                                    <span><strong>Size:</strong> {{ number_format($property->size) }} sqft</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ $property->category ?: 'Apartment' }}</span>
                            </td>
                            <td>
                                @if($property->is_featured)
                                    <span class="badge-status badge-published"><i
                                            class="bi bi-star-fill me-1"></i>Featured</span>
                                @else
                                    <span class="badge-status badge-draft">Standard</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('panel.properties.edit', $property->id) }}"
                                        class="row-action-btn d-inline-flex align-items-center justify-content-center text-decoration-none"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('panel.properties.destroy', $property->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this property?');"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="row-action-btn delete d-inline-flex align-items-center justify-content-center border-0 bg-transparent"
                                            title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-building-fill-slash" style="font-size: 2.5rem;"></i>
                                <p class="mt-3 mb-0 fw-medium">No properties found.</p>
                                <p class="small text-muted">Get started by creating a new property listing.</p>
                                <a href="{{ route('panel.properties.create') }}" class="btn btn-accent btn-sm mt-2">
                                    <i class="bi bi-plus-lg me-1"></i> Call Now
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-panel.layout>