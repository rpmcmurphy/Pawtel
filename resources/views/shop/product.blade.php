@extends('layouts.app')

@section('title', $product['name'] . ' - Pawtel Shop')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <!-- Product Images -->
                <div class="product-images">
                    <div class="main-image mb-3">
                        <img src="{{ $product['image_url'] ?? 'https://via.placeholder.com/600x400' }}"
                            alt="{{ $product['name'] }}" class="img-fluid rounded-3" id="mainProductImage">
                    </div>

                    @if (!empty($product['gallery']))
                        <div class="image-thumbnails">
                            <div class="row g-2">
                                @foreach ($product['gallery'] as $image)
                                    <div class="col-3">
                                        <img src="{{ $image['url'] }}" alt="{{ $product['name'] }}"
                                            class="img-fluid rounded-2 thumbnail-img" onclick="changeMainImage(this.src)">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <div class="product-details">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
                            @if ($product['category'])
                                <li class="breadcrumb-item">
                                    <a href="{{ route('shop.category', $product['category']['slug']) }}">
                                        {{ $product['category']['name'] }}
                                    </a>
                                </li>
                            @endif
                            <li class="breadcrumb-item active">{{ $product['name'] }}</li>
                        </ol>
                    </nav>

                    <h1 class="product-title mb-3">{{ $product['name'] }}</h1>

                    @if ($product['brand'])
                        <p class="product-brand text-muted mb-3">
                            <strong>Brand:</strong> {{ $product['brand'] }}
                        </p>
                    @endif

                    <div class="product-price mb-4">
                        <span class="current-price h3 text-primary">
                            ৳{{ number_format($product['price'], 2) }}
                        </span>

                        @if ($product['original_price'] && $product['original_price'] > $product['price'])
                            <span class="original-price text-muted ms-2">
                                <s>৳{{ number_format($product['original_price'], 2) }}</s>
                            </span>
                            <span class="discount-badge ms-2">
                                {{ round((($product['original_price'] - $product['price']) / $product['original_price']) * 100) }}%
                                OFF
                            </span>
                        @endif
                    </div>

                    @if ($product['description'])
                        <div class="product-description mb-4">
                            <h5>Description</h5>
                            <p>{{ $product['description'] }}</p>
                        </div>
                    @endif

                    @if (!empty($product['features']))
                        <div class="product-features mb-4">
                            <h5>Features</h5>
                            <ul>
                                @foreach ($product['features'] as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Add to Cart Section -->
                    @auth
                        <div class="add-to-cart-section">
                            <form id="addToCartForm" class="d-flex align-items-center gap-3 mb-4">
                                <div class="quantity-selector">
                                    <label for="quantity" class="form-label">Quantity:</label>
                                    <div class="input-group" style="width: 120px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(-1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" id="quantity" value="1"
                                            min="1" max="{{ $product['stock_quantity'] ?? 99 }}">
                                        <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary btn-lg add-to-cart-btn"
                                    data-product-id="{{ $product['id'] }}">
                                    <i class="fas fa-cart-plus me-2"></i>
                                    Add to Cart
                                </button>
                            </form>

                            <div class="product-actions">
                                <button class="btn btn-outline-secondary me-2" onclick="addToWishlist({{ $product['id'] }})">
                                    <i class="fas fa-heart me-1"></i>
                                    Add to Wishlist
                                </button>
                                <button class="btn btn-outline-info" onclick="shareProduct()">
                                    <i class="fas fa-share-alt me-1"></i>
                                    Share
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="login-prompt">
                            <p class="text-muted">Please <a href="{{ route('auth.login') }}">login</a> to purchase this
                                product.</p>
                        </div>
                    @endauth

                    <!-- Product Info -->
                    <div class="product-meta mt-4 pt-4 border-top">
                        @if ($product['sku'])
                            <p><strong>SKU:</strong> {{ $product['sku'] }}</p>
                        @endif

                        @if ($product['stock_quantity'])
                            <p><strong>Availability:</strong>
                                <span class="text-success">{{ $product['stock_quantity'] }} in stock</span>
                            </p>
                        @endif

                        @if (!empty($product['tags']))
                            <p><strong>Tags:</strong>
                                @foreach ($product['tags'] as $tag)
                                    <span class="badge bg-light text-dark">{{ $tag }}</span>
                                @endforeach
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if (!empty($relatedProducts['data']))
            <!-- Related Products -->
            <div class="related-products mt-5">
                <h3 class="mb-4">Related Products</h3>
                <div class="row g-4">
                    @foreach ($relatedProducts['data'] as $relatedProduct)
                        <div class="col-md-6 col-lg-3">
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="{{ $relatedProduct['image_url'] ?? 'https://via.placeholder.com/300x200' }}"
                                        alt="{{ $relatedProduct['name'] }}">
                                    <div class="product-actions">
                                        <a href="{{ route('shop.product', $relatedProduct['slug']) }}"
                                            class="btn btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h6 class="product-title">
                                        <a href="{{ route('shop.product', $relatedProduct['slug']) }}">
                                            {{ $relatedProduct['name'] }}
                                        </a>
                                    </h6>
                                    <div class="product-price">
                                        ৳{{ number_format($relatedProduct['price'], 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modules/shop.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Shop.init();
        });

        function changeMainImage(src) {
            document.getElementById('mainProductImage').src = src;
        }

        function changeQuantity(delta) {
            const input = document.getElementById('quantity');
            const newValue = parseInt(input.value) + delta;
            const min = parseInt(input.min);
            const max = parseInt(input.max);

            if (newValue >= min && newValue <= max) {
                input.value = newValue;
            }
        }

        // Update add to cart button to use quantity
        document.addEventListener('click', function(e) {
            if (e.target.matches('.add-to-cart-btn')) {
                const productId = e.target.dataset.productId;
                const quantity = document.getElementById('quantity').value;

                e.target.dataset.quantity = quantity;
            }
        });
    </script>
@endpush
