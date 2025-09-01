@extends('layouts.app')

@section('title', 'Pet Shop - Pawtel')

@section('content')
    <div class="shop-hero py-4 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-5 text-gradient mb-2">Pet Shop</h1>
                    <p class="lead">Quality products for your beloved pets</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="filters-card">
                    <h5 class="mb-3">Filter Products</h5>

                    <form method="GET" action="{{ route('shop.index') }}" id="filterForm">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Search products...">
                        </div>

                        <!-- Categories -->
                        @if (!empty($categories['data']))
                            <div class="mb-3">
                                <label class="form-label">Categories</label>
                                <div class="category-list">
                                    @foreach ($categories['data'] as $category)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="category"
                                                value="{{ $category['slug'] }}" id="cat_{{ $category['id'] }}"
                                                {{ request('category') == $category['slug'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cat_{{ $category['id'] }}">
                                                {{ $category['name'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @if (request('category'))
                                    <a href="{{ route('shop.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                                        Clear Category
                                    </a>
                                @endif
                            </div>
                        @endif

                        <!-- Sort -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" name="sort" id="sort">
                                <option value="">Default</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z
                                </option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A
                                </option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price
                                    Low-High</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price
                                    High-Low</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                @if (!empty($products['data']))
                    <div class="products-header mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted">
                                Showing {{ count($products['data']) }} products
                            </p>
                            <div class="view-toggle">
                                <button class="btn btn-sm btn-outline-secondary active" data-view="grid">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" data-view="list">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="products-grid" id="productsGrid">
                        <div class="row g-4">
                            @foreach ($products['data'] as $product)
                                <div class="col-md-6 col-xl-4">
                                    <div class="product-card">
                                        <div class="product-image">
                                            <img src="{{ $product['image_url'] ?? 'https://via.placeholder.com/300x200' }}"
                                                alt="{{ $product['name'] }}" class="img-fluid">

                                            @if ($product['is_featured'] ?? false)
                                                <span class="product-badge">Featured</span>
                                            @endif

                                            @if ($product['discount_percentage'] > 0)
                                                <span class="product-badge sale-badge">
                                                    -{{ $product['discount_percentage'] }}%
                                                </span>
                                            @endif

                                            <div class="product-actions">
                                                <button class="btn btn-primary add-to-cart-btn"
                                                    data-product-id="{{ $product['id'] }}">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="product-info">
                                            <h5 class="product-title">
                                                <a href="{{ route('shop.product', $product['slug']) }}">
                                                    {{ $product['name'] }}
                                                </a>
                                            </h5>

                                            <div class="product-price">
                                                ৳{{ number_format($product['price'], 2) }}
                                                @if (($product['original_price'] ?? 0) > $product['price'])
                                                    <span
                                                        class="original-price">৳{{ number_format($product['original_price'], 2) }}</span>
                                                @endif
                                            </div>

                                            @if ($product['description'])
                                                <p class="product-description">
                                                    {{ Str::limit($product['description'], 80) }}
                                                </p>
                                            @endif

                                            <div class="product-footer">
                                                <a href="{{ route('shop.product', $product['slug']) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    View Details
                                                </a>

                                                @auth
                                                    <button class="btn btn-primary btn-sm add-to-cart-btn"
                                                        data-product-id="{{ $product['id'] }}">
                                                        Add to Cart
                                                    </button>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if (isset($products['pagination']))
                        <div class="d-flex justify-content-center mt-5">
                            <nav>
                                <ul class="pagination">
                                    <!-- Pagination links would go here -->
                                </ul>
                            </nav>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4>No Products Found</h4>
                        <p class="text-muted">Try adjusting your filters or search terms.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/modules/shop.js') }}"></script>
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            Shop.init();

            // Auto-submit form on filter change
            document.getElementById('filterForm').addEventListener('change', function() {
                this.submit();
            });
        });
    </script>
@endpush
