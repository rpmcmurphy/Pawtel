@extends('layouts.app')

@section('title', 'Pawtel - Premium Cat Services')

@section('content')
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h2 class="hero-subtitle">OUR SERVICES</h2>
                        <h1 class="hero-title">PREMIUM CARE AT AN AFFORDABLE RANGE</h1>
                        <p class="hero-description lead">
                            Pawtel offers premium cat boarding, spa services, and healthcare with
                            the highest standards of safety and comfort for your feline friends.
                        </p>
                        <div class="hero-buttons">
                            <a href="{{ route('booking.hotel.index') }}" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Book Now
                            </a>
                            <a href="{{ route('shop.index') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Shop Now
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="https://images.unsplash.com/photo-1513360371669-4adf3dd7dff8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            alt="Happy cat" class="img-fluid rounded-4">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <section class="services-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Our Premium Services</h2>
                    <p class="section-subtitle">Everything your feline friend needs under one roof</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <h4 class="service-title">Cat Hotel</h4>
                        <p class="service-description">Comfortable boarding with individual rooms and 24/7 care.</p>
                        <a href="{{ route('booking.hotel.index') }}" class="btn btn-outline-primary btn-sm">Learn More</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-spa"></i>
                        </div>
                        <h4 class="service-title">Spa & Grooming</h4>
                        <p class="service-description">Professional grooming and spa treatments for your cat.</p>
                        <a href="{{ route('booking.spa.index') }}" class="btn btn-outline-primary btn-sm">Learn More</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h4 class="service-title">Spay/Neuter</h4>
                        <p class="service-description">Safe surgical procedures with post-operative care.</p>
                        <a href="{{ route('booking.spay.index') }}" class="btn btn-outline-primary btn-sm">Learn More</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h4 class="service-title">Pet Supplies</h4>
                        <p class="service-description">Quality food, toys, and accessories for your pets.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary btn-sm">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (!empty($featuredProducts['data']))
        <!-- Featured Products -->
        <section class="featured-products py-5 bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <h2 class="section-title">Featured Products</h2>
                        <p class="section-subtitle">Popular items for your furry friends</p>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach ($featuredProducts['data'] as $product)
                        <div class="col-md-6 col-lg-3">
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="{{ $product['image_url'] ?? 'https://cdn2.thecatapi.com/images/ebv.jpg' }}"
                                        alt="{{ $product['name'] }}">
                                    @if ($product['is_featured'] ?? false)
                                        <span class="product-badge">Featured</span>
                                    @endif
                                </div>
                                <div class="product-info">
                                    <h5 class="product-title">{{ $product['name'] }}</h5>
                                    <div class="product-price">
                                        ৳{{ number_format($product['price'], 2) }}
                                        @if (($product['compare_price'] ?? 0) > $product['price'])
                                            <span
                                                class="original-price">৳{{ number_format($product['compare_price'], 2) }}</span>
                                        @endif
                                    </div>
                                    <p class="product-description">{{ Str::limit($product['description'], 80) }}</p>
                                    <a href="{{ route('shop.product', $product['slug']) }}"
                                        class="btn btn-primary btn-sm">View Product</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">View All Products</a>
                </div>
            </div>
        </section>
    @endif

    <!-- CTA Section -->
    <section class="cta-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <div class="cta-content">
                        <h2 class="cta-title">Ready to Give Your Cat the Best Care?</h2>
                        <p class="cta-description">Book our premium services today and see the difference quality care
                            makes.</p>
                        <div class="cta-buttons">
                            <a href="{{ route('booking.hotel.index') }}" class="btn btn-primary btn-lg me-3">
                                Book Services
                            </a>
                            <a href="tel:+8801733191556" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-phone me-2"></i>
                                Call Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
