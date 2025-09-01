@php
    $isEditing = isset($product) && $product;
    $method = $method ?? ($isEditing ? 'PUT' : 'POST');
    $action = $action ?? ($isEditing ? route('admin.products.update', $product['id']) : route('admin.products.store'));
    $product = $product ?? [];
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="productForm">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    name="name" value="{{ old('name', $product['name'] ?? '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                    name="sku" value="{{ old('sku', $product['sku'] ?? '') }}">
                @error('sku')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                    name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category['id'] }}"
                            {{ old('category_id', $product['category']['id'] ?? '') == $category['id'] ? 'selected' : '' }}>
                            {{ $category['name'] }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price"
                    name="price" step="0.01" min="0" value="{{ old('price', $product['price'] ?? '') }}"
                    required>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="stock_quantity" class="form-label">Stock Quantity</label>
                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                    id="stock_quantity" name="stock_quantity" min="0"
                    value="{{ old('stock_quantity', $product['stock_quantity'] ?? 0) }}" required>
                @error('stock_quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                    required>
                    <option value="active" {{ old('status', $product['status'] ?? '') == 'active' ? 'selected' : '' }}>
                        Active</option>
                    <option value="inactive"
                        {{ old('status', $product['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="out_of_stock"
                        {{ old('status', $product['status'] ?? '') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock
                    </option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="4">{{ old('description', $product['description'] ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Product Image</label>
        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image"
            accept="image/*">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input @error('featured') is-invalid @enderror" id="featured"
            name="featured" value="1" {{ old('featured', $product['featured'] ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="featured">
            Featured Product
        </label>
        @error('featured')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Basic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label required">Product Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name', $product['name'] ?? '') }}"
                                placeholder="Enter product name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">SKU</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                name="sku" value="{{ old('sku', $product['sku'] ?? '') }}"
                                placeholder="e.g., FOOD-DOG-001" required>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" name="category_id"
                                required>
                                <option value="">Select Category</option>
                                <option value="1"
                                    {{ old('category_id', $product['category_id'] ?? '') == '1' ? 'selected' : '' }}>
                                    Pet Food
                                </option>
                                <option value="2"
                                    {{ old('category_id', $product['category_id'] ?? '') == '2' ? 'selected' : '' }}>
                                    Toys & Accessories
                                </option>
                                <option value="3"
                                    {{ old('category_id', $product['category_id'] ?? '') == '3' ? 'selected' : '' }}>
                                    Health & Grooming
                                </option>
                                <option value="4"
                                    {{ old('category_id', $product['category_id'] ?? '') == '4' ? 'selected' : '' }}>
                                    Beds & Furniture
                                </option>
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Short Description</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" name="short_description"
                                rows="3" placeholder="Brief description for product listings">{{ old('short_description', $product['short_description'] ?? '') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Full Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="6"
                                placeholder="Detailed product description">{{ old('description', $product['description'] ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Specifications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Specifications
                    </h5>
                </div>
                <div class="card-body">
                    <div id="specifications">
                        @php
                            $specifications = old('specifications', $product['specifications'] ?? []);
                            if (empty($specifications)) {
                                $specifications = [['key' => '', 'value' => '']];
                            }
                        @endphp

                        @foreach ($specifications as $index => $spec)
                            <div class="row mb-3 specification-row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control"
                                        name="specifications[{{ $index }}][key]"
                                        value="{{ $spec['key'] ?? '' }}" placeholder="e.g., Weight, Material, Size">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"
                                        name="specifications[{{ $index }}][value]"
                                        value="{{ $spec['value'] ?? '' }}" placeholder="e.g., 2kg, Cotton, Large">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-spec"
                                        {{ $index === 0 ? 'style=display:none' : '' }}>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addSpec">
                        <i class="fas fa-plus"></i> Add Specification
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Pricing & Inventory -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-dollar-sign"></i> Pricing & Inventory
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                name="price" step="0.01" min="0"
                                value="{{ old('price', $product['price'] ?? '') }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Compare At Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number"
                                class="form-control @error('compare_at_price') is-invalid @enderror"
                                name="compare_at_price" step="0.01" min="0"
                                value="{{ old('compare_at_price', $product['compare_at_price'] ?? '') }}">
                            @error('compare_at_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">Original price for sale items</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                            name="stock_quantity" min="0"
                            value="{{ old('stock_quantity', $product['stock_quantity'] ?? '0') }}">
                        @error('stock_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Low Stock Alert</label>
                        <input type="number" class="form-control @error('low_stock_alert') is-invalid @enderror"
                            name="low_stock_alert" min="0"
                            value="{{ old('low_stock_alert', $product['low_stock_alert'] ?? '5') }}">
                        @error('low_stock_alert')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Alert when stock falls below this number</small>
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-images"></i> Product Images
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Main Image</label>
                        <input type="file" class="form-control @error('main_image') is-invalid @enderror"
                            name="main_image" accept="image/*">
                        @error('main_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($isEditing && !empty($product['main_image']))
                            <div class="mt-2">
                                <img src="{{ $product['main_image'] }}" alt="Current main image"
                                    class="img-thumbnail" style="max-width: 150px;">
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gallery Images</label>
                        <input type="file" class="form-control @error('gallery_images') is-invalid @enderror"
                            name="gallery_images[]" accept="image/*" multiple>
                        @error('gallery_images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">You can select multiple images</small>

                        @if ($isEditing && !empty($product['gallery_images']))
                            <div class="mt-2">
                                <div class="row">
                                    @foreach ($product['gallery_images'] as $image)
                                        <div class="col-6 mb-2">
                                            <img src="{{ $image }}" alt="Gallery image"
                                                class="img-thumbnail w-100">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-toggle-on"></i> Status & Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status">
                            <option value="active"
                                {{ old('status', $product['status'] ?? 'active') === 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive"
                                {{ old('status', $product['status'] ?? '') === 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                            <option value="draft"
                                {{ old('status', $product['status'] ?? '') === 'draft' ? 'selected' : '' }}>
                                Draft
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="featured" value="1"
                            {{ old('featured', $product['featured'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">Featured Product</label>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="track_stock" value="1"
                            {{ old('track_stock', $product['track_stock'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label">Track Stock Quantity</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="allow_backorder" value="1"
                            {{ old('allow_backorder', $product['allow_backorder'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">Allow Backorders</label>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-search"></i> SEO
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                            name="meta_title" value="{{ old('meta_title', $product['meta_title'] ?? '') }}"
                            maxlength="60">
                        @error('meta_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Recommended: 50-60 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control @error('meta_description') is-invalid @enderror" name="meta_description"
                            rows="3" maxlength="160">{{ old('meta_description', $product['meta_description'] ?? '') }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Recommended: 150-160 characters</small>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Form Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $isEditing ? 'Update Product' : 'Create Product' }}
                </button>
                <button type="submit" name="save_and_continue" value="1" class="btn btn-outline-primary">
                    <i class="fas fa-save"></i>
                    {{ $isEditing ? 'Update & Continue Editing' : 'Save & Continue Editing' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
    <script type="module">
        $(document).ready(function() {
            let specIndex = {{ count($specifications ?? []) }};

            // Add specification row
            $('#addSpec').click(function() {
                const newRow = `
            <div class="row mb-3 specification-row">
                <div class="col-md-4">
                    <input type="text" class="form-control" 
                           name="specifications[${specIndex}][key]" 
                           placeholder="e.g., Weight, Material, Size">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" 
                           name="specifications[${specIndex}][value]" 
                           placeholder="e.g., 2kg, Cotton, Large">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-spec">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
                $('#specifications').append(newRow);
                specIndex++;
            });

            // Remove specification row
            $(document).on('click', '.remove-spec', function() {
                $(this).closest('.specification-row').remove();
            });

            // Auto-generate SKU from name
            $('input[name="name"]').on('blur', function() {
                const name = $(this).val();
                const skuInput = $('input[name="sku"]');

                if (name && !skuInput.val()) {
                    const sku = name.toUpperCase()
                        .replace(/[^A-Z0-9\s]/g, '')
                        .replace(/\s+/g, '-')
                        .substring(0, 20);
                    skuInput.val(sku);
                }
            });

            // Form validation
            $('#productForm').on('submit', function(e) {
                let isValid = true;
                const requiredFields = ['name', 'sku', 'category_id', 'price'];

                requiredFields.forEach(function(field) {
                    const input = $(`[name="${field}"]`);
                    if (!input.val().trim()) {
                        input.addClass('is-invalid');
                        isValid = false;
                    } else {
                        input.removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 100
                    }, 500);
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .required::after {
            content: ' *';
            color: #dc3545;
        }

        .specification-row {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 1rem;
            margin-bottom: 1rem !important;
        }

        .specification-row:last-child {
            border-bottom: none;
        }
    </style>
@endpush
