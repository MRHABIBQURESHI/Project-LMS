<x-panel.layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="page-title">Add New Property</div>
            <div class="page-subtitle mb-0">Create a new real estate property listing.</div>
        </div>
        <a href="{{ route('panel.properties.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="background-color: #f8d7da; color: #842029;">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the following errors:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-panel">
        <form action="{{ route('panel.properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <!-- Title -->
                <div class="col-md-8">
                    <label for="title" class="form-label fw-medium">Property Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g., Luxury 3 Bed Apartment in Downtown" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Price -->
                <div class="col-md-4">
                    <label for="price" class="form-label fw-medium">Price ($) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" placeholder="450000" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="col-md-12">
                    <label for="address" class="form-label fw-medium">Address <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" placeholder="e.g., 123 Main St, New York, NY" required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Category -->
                <div class="col-md-3">
                    <label for="category" class="form-label fw-medium">Category</label>
                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                        <option value="Apartment" {{ old('category') == 'Apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="House" {{ old('category') == 'House' ? 'selected' : '' }}>House</option>
                        <option value="Condo" {{ old('category') == 'Condo' ? 'selected' : '' }}>Condo</option>
                        <option value="Villa" {{ old('category') == 'Villa' ? 'selected' : '' }}>Villa</option>
                        <option value="Land" {{ old('category') == 'Land' ? 'selected' : '' }}>Land</option>
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Type -->
                <div class="col-md-3">
                    <label for="type" class="form-label fw-medium">Type</label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                        <option value="Sale" {{ old('type') == 'Sale' ? 'selected' : '' }}>For Sale</option>
                        <option value="Rent" {{ old('type') == 'Rent' ? 'selected' : '' }}>For Rent</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Size -->
                <div class="col-md-2">
                    <label for="size" class="form-label fw-medium">Size (sqft) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('size') is-invalid @enderror" id="size" name="size" value="{{ old('size') }}" placeholder="1500" required>
                    @error('size')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Bedrooms -->
                <div class="col-md-2">
                    <label for="bedrooms" class="form-label fw-medium">Bedrooms <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('bedrooms') is-invalid @enderror" id="bedrooms" name="bedrooms" value="{{ old('bedrooms', 0) }}" min="0" required>
                    @error('bedrooms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Bathrooms -->
                <div class="col-md-2">
                    <label for="bathrooms" class="form-label fw-medium">Bathrooms <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('bathrooms') is-invalid @enderror" id="bathrooms" name="bathrooms" value="{{ old('bathrooms', 0) }}" min="0" required>
                    @error('bathrooms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Garages -->
                <div class="col-md-2">
                    <label for="garages" class="form-label fw-medium">Garages</label>
                    <input type="number" class="form-control @error('garages') is-invalid @enderror" id="garages" name="garages" value="{{ old('garages', 0) }}" min="0">
                    @error('garages')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div class="col-md-6">
                    <label for="image" class="form-label fw-medium">Property Banner Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                    <div class="form-text text-muted">Upload a clear photo (JPG, PNG, WebP). Max 4MB.</div>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Featured Status -->
                <div class="col-md-4 d-flex align-items-center pt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium ms-1" for="is_featured">Feature this Property</label>
                        <div class="form-text text-muted">Featured properties appear on the homepage banner.</div>
                    </div>
                </div>

                <!-- Description -->
                <div class="col-md-12">
                    <label for="description" class="form-label fw-medium">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" placeholder="Detailed property features, rules, neighborhood description...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 pt-3 border-top mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-accent px-4">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Save Property
                    </button>
                    <a href="{{ route('panel.properties.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</x-panel.layout>
