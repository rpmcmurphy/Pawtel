@php
    $isEditing = isset($product) && $product;
    $method = $method ?? ($isEditing ? 'PUT' : 'POST');
    $action = $action ?? ($isEditing ? route('admin.products.update', $product['id']) : route('admin.products.store'));
    $product = $product ?? [];
@endphp

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            {{ $isEditing ? 'Edit Product' : 'Create New Product' }}
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="productForm">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="row">
                <!-- Basic Information -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Basic Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">Product Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name"
                                            value="{{ old('name', $product['name'] ?? '') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sku" class="form-label">SKU</label>
                                        <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                            id="sku" name="sku"
                                            value="{{ old('sku', $product['sku'] ?? '') }}">
                                        @error('sku')
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
                                <label for="category_id" class="form-label required">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['id'] }}"
                                            {{ old('category_id', $product['category_id'] ?? '') == $category['id'] ? 'selected' : '' }}>
                                            {{ $category['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Specifications -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Product Specifications</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addSpec">
                                <i class="fas fa-plus"></i> Add Specification
                            </button>
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
                                        <div class="col-md-5">
                                            <input type="text" class="form-control"
                                                name="specifications[{{ $index }}][key]"
                                                value="{{ $spec['key'] ?? '' }}"
                                                placeholder="e.g., Weight, Material, Brand">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control"
                                                name="specifications[{{ $index }}][value]"
                                                value="{{ $spec['value'] ?? '' }}"
                                                placeholder="e.g., 2kg, Cotton, PetPro">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-spec"
                                                {{ $index === 0 && count($specifications) === 1 ? 'style=display:none' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Settings -->
                <div class="col-lg-4">
                    <!-- Pricing -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Pricing & Inventory</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                        name="price" step="0.01" min="0"
                                        value="{{ old('price', $product['price'] ?? '') }}" required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Compare at Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                        class="form-control @error('compare_price') is-invalid @enderror"
                                        name="compare_price" step="0.01" min="0"
                                        value="{{ old('compare_price', $product['compare_price'] ?? '') }}">
                                </div>
                                @error('compare_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Original price for sale items</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Stock Quantity</label>
                                <input type="number"
                                    class="form-control @error('stock_quantity') is-invalid @enderror"
                                    name="stock_quantity" min="0"
                                    value="{{ old('stock_quantity', $product['stock_quantity'] ?? '0') }}" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Low Stock Threshold</label>
                                <input type="number"
                                    class="form-control @error('low_stock_threshold') is-invalid @enderror"
                                    name="low_stock_threshold" min="0"
                                    value="{{ old('low_stock_threshold', $product['low_stock_threshold'] ?? '5') }}">
                                @error('low_stock_threshold')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status & Settings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Product Settings</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label required">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="active"
                                        {{ old('status', $product['status'] ?? 'active') == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive"
                                        {{ old('status', $product['status'] ?? '') == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                    <option value="out_of_stock"
                                        {{ old('status', $product['status'] ?? '') == 'out_of_stock' ? 'selected' : '' }}>
                                        Out of Stock
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox"
                                    class="form-check-input @error('featured') is-invalid @enderror" id="featured"
                                    name="featured" value="1"
                                    {{ old('featured', $product['featured'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">
                                    Featured Product
                                </label>
                                @error('featured')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Product Images</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="images" class="form-label">Upload Images</label>
                                <input type="file"
                                    class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                                    id="images" name="images[]" accept="image/*" multiple>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">You can select multiple images</small>
                            </div>

                            @if ($isEditing && !empty($product['images']))
                                <div class="mb-3">
                                    <label class="form-label">Current Images</label>
                                    <div class="row">
                                        @foreach ($product['images'] as $image)
                                            <div class="col-6 mb-2">
                                                <img src="{{ asset('storage/' . $image) }}" class="img-thumbnail"
                                                    style="height: 100px; object-fit: cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ $isEditing ? 'Update Product' : 'Create Product' }}
                        </button>
                        <button type="submit" name="save_and_continue" value="1"
                            class="btn btn-outline-primary">
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
    </div>
</div>

@push('scripts')
    <script type="module">
        $(document).ready(function() {
            let specIndex = {{ count($specifications ?? []) }};

            // Add specification row
            $('#addSpec').click(function() {
                const newRow = `
            <div class="row mb-3 specification-row">
                <div class="col-md-5">
                    <input type="text" class="form-control" 
                           name="specifications[${specIndex}][key]" 
                           placeholder="e.g., Weight, Material, Brand">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" 
                           name="specifications[${specIndex}][value]" 
                           placeholder="e.g., 2kg, Cotton, PetPro">
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
                const requiredFields = ['name', 'category_id', 'price', 'stock_quantity'];

                requiredFields.forEach(function(field) {
                    const input = $(`[name="${field}"]`);
                    if (!input.val()) {
                        input.addClass('is-invalid');
                        if (!input.next('.invalid-feedback').length) {
                            input.after(
                                '<div class="invalid-feedback">This field is required.</div>');
                        }
                        isValid = false;
                    } else {
                        input.removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $('.is-invalid:first').offset().top - 100
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
    </style>
@endpush
