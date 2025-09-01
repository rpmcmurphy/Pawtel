@extends('layouts.admin')

@section('title', 'Manage Products - Admin')
@section('page-title', 'Products Management')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Products Management</h2>
        <div class="page-actions">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add Product
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filter Panel -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of
                            Stock</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" name="category_id" id="category_id">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category['id'] ?? '' }}"
                                {{ request('category_id') == ($category['id'] ?? null) ? 'selected' : '' }}>
                                {{ $category['name'] ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="featured" class="form-label">Featured</label>
                    <select class="form-select" name="featured" id="featured">
                        <option value="">All Products</option>
                        <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured Only</option>
                        <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Not Featured</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            @if (!empty($products['data']))
                <div class="table-responsive">
                    <table class="table table-hover" id="productsTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products['data'] as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $product['image_url'] ?? 'https://via.placeholder.com/50x50' }}"
                                                alt="{{ $product['name'] }}" class="rounded me-3"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">{{ $product['name'] }}</h6>
                                                <small class="text-muted">{{ $product['sku'] ?? 'No SKU' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $product['category']['name'] ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>৳{{ number_format($product['price'], 2) }}</strong>
                                            @if (($product['compare_price'] ?? 0) > ($product['price'] ?? 0))
                                                <br>
                                                <small class="text-muted">
                                                    <s>৳{{ number_format($product['compare_price'], 2) }}</s>
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $product['stock_quantity'] > 0 ? 'success' : 'danger' }}">
                                            {{ $product['stock_quantity'] }} in stock
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $product['status'] == 'active' ? 'success' : ($product['status'] == 'inactive' ? 'secondary' : 'warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $product['status'])) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if (!empty($product['is_featured']))
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.products.show', $product['id']) }}"
                                                class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product['id']) }}"
                                                class="btn btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-outline-danger"
                                                onclick="deleteProduct({{ $product['id'] }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                    <h5>No Products Found</h5>
                    <p class="text-muted">No products match your current filters.</p>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add First Product</a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        $(document).ready(function() {
            $('#productsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                responsive: true
            });
        });

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                fetch(`/admin/products/${productId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete product: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to delete product');
                    });
            }
        }
    </script>
@endpush
