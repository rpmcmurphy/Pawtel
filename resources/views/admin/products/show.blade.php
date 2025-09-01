@extends('layouts.admin')

@section('title', 'Product Details - Admin')
@section('page-title', 'Product Details')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Product Details</h2>
        <div class="page-actions">
            <a href="{{ route('admin.products.edit', $product['id']) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Edit Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Products
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ $product['image_url'] ?? 'https://via.placeholder.com/300x300' }}" 
                                 alt="{{ $product['name'] }}" 
                                 class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h3>{{ $product['name'] }}</h3>
                            
                            <div class="mb-3">
                                <span class="badge bg-{{ $product['status'] == 'active' ? 'success' : ($product['status'] == 'inactive' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst(str_replace('_', ' ', $product['status'])) }}
                                </span>
                                @if (!empty($product['featured']))
                                    <span class="badge bg-warning">Featured</span>
                                @endif
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Price:</strong> ${{ number_format($product['price'], 2) }}
                                </div>
                                <div class="col-sm-6">
                                    @if (!empty($product['original_price']) && $product['original_price'] > $product['price'])
                                        <strong>Original Price:</strong> 
                                        <del class="text-muted">${{ number_format($product['original_price'], 2) }}</del>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>SKU:</strong> {{ $product['sku'] ?? 'N/A' }}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Stock:</strong> 
                                    <span class="badge bg-{{ $product['stock_quantity'] > 0 ? 'success' : 'danger' }}">
                                        {{ $product['stock_quantity'] }} in stock
                                    </span>
                                </div>
                            </div>

                            @if (!empty($product['brand']))
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Brand:</strong> {{ $product['brand'] }}
                                </div>
                            </div>
                            @endif

                            @if (!empty($product['weight']) || !empty($product['dimensions']))
                            <div class="row mb-3">
                                @if (!empty($product['weight']))
                                <div class="col-sm-6">
                                    <strong>Weight:</strong> {{ $product['weight'] }}
                                </div>
                                @endif
                                @if (!empty($product['dimensions']))
                                <div class="col-sm-6">
                                    <strong>Dimensions:</strong> {{ $product['dimensions'] }}
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    @if (!empty($product['description']))
                    <div class="mt-4">
                        <h5>Description</h5>
                        <p class="text-muted">{{ $product['description'] }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Category:</strong><br>
                        <span class="badge bg-light text-dark">
                            {{ $product['category']['name'] ?? 'No Category' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ isset($product['created_at']) ? \Carbon\Carbon::parse($product['created_at'])->format('M j, Y g:i A') : 'N/A' }}
                    </div>

                    <div class="mb-3">
                        <strong>Updated:</strong><br>
                        {{ isset($product['updated_at']) ? \Carbon\Carbon::parse($product['updated_at'])->format('M j, Y g:i A') : 'N/A' }}
                    </div>

                    @if (!empty($product['meta_title']) || !empty($product['meta_description']))
                    <div class="mt-4">
                        <h6>SEO Information</h6>
                        @if (!empty($product['meta_title']))
                        <div class="mb-2">
                            <strong>Meta Title:</strong><br>
                            <small class="text-muted">{{ $product['meta_title'] }}</small>
                        </div>
                        @endif
                        @if (!empty($product['meta_description']))
                        <div class="mb-2">
                            <strong>Meta Description:</strong><br>
                            <small class="text-muted">{{ $product['meta_description'] }}</small>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.edit', $product['id']) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Product
                        </a>
                        
                        <form action="{{ route('admin.products.status', $product['id']) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="{{ $product['status'] == 'active' ? 'inactive' : 'active' }}">
                            <button type="submit" class="btn btn-outline-{{ $product['status'] == 'active' ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-{{ $product['status'] == 'active' ? 'pause' : 'play' }} me-2"></i>
                                {{ $product['status'] == 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>

                        <button class="btn btn-outline-danger" onclick="deleteProduct({{ $product['id'] }})">
                            <i class="fas fa-trash me-2"></i>Delete Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="module">
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.products.index') }}/${productId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush