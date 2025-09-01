{{-- resources/views/booking/spa/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Spa & Grooming Services')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="hero-section bg-gradient-primary text-white rounded-lg p-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 mb-3">
                            <i class="fas fa-spa text-info"></i> Spa & Grooming Services
                        </h1>
                        <p class="lead mb-4">
                            Pamper your feline friends with our professional grooming and luxurious spa treatments. 
                            From basic grooming to full spa packages, we ensure your cat looks and feels their best.
                        </p>
                        @auth
                            <a href="{{ route('booking.spa.form') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-calendar-plus"></i> Book Spa Service
                            </a>
                        @else
                            <a href="{{ route('auth.login') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Login to Book
                            </a>
                        @endauth
                    </div>
                    <div class="col-lg-4 text-center">
                        <i class="fas fa-spa fa-8x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Packages -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="section-title text-center mb-5">
                <i class="fas fa-scissors text-primary"></i> Our Spa Packages
            </h2>
            
            <div class="row" id="spaPackages">
                <!-- Loading placeholder -->
                <div class="col-12 text-center" id="loadingPackages">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">Loading spa packages...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual Services -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="mb-4">
                <i class="fas fa-list text-primary"></i> Individual Services
            </h3>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="service-item-card h-100">
                        <div class="service-icon">
                            <i class="fas fa-cut"></i>
                        </div>
                        <h5>Basic Grooming</h5>
                        <p>Essential grooming including brushing, nail trimming, and ear cleaning.</p>
                        <span class="price">From $35</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="service-item-card h-100">
                        <div class="service-icon">
                            <i class="fas fa-shower"></i>
                        </div>
                        <h5>Premium Bath</h5>
                        <p>Luxurious bath with premium shampoos and conditioners for a silky coat.</p>
                        <span class="price">From $45</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="service-item-card h-100">
                        <div class="service-icon">
                            <i class="fas fa-hand-sparkles"></i>
                        </div>
                        <h5>Nail Care</h5>
                        <p>Professional nail trimming and paw care for healthy, comfortable paws.</p>
                        <span class="price">From $20</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="service-item-card h-100">
                        <div class="service-icon">
                            <i class="fas fa-tooth"></i>
                        </div>
                        <h5>Dental Care</h5>
                        <p>Teeth cleaning and oral health maintenance for fresh breath and healthy gums.</p>
                        <span class="price">From $40</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="service-item-card h-100">
                        <div class="service-icon">
                            <i class="fas fa-bug"></i>
                        </div>
                        <h5>Flea Treatment</h5>
                        <p>Safe and effective flea and tick treatment to keep your cat comfortable.</p>
                        <span class="price">From $30</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="service-item-card h-100">
                        <div class="service-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h5>Aromatherapy</h5>
                        <p>Relaxing aromatherapy session to reduce stress and promote wellbeing.</p>
                        <span class="price">From $25</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="mb-4">
                <i class="fas fa-award text-primary"></i> Why Choose Our Spa Services?
            </h3>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-card text-center h-100">
                        <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                        <h5>Professional Staff</h5>
                        <p>Certified grooming professionals with years of experience working with cats.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-card text-center h-100">
                        <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                        <h5>Gentle Care</h5>
                        <p>We understand cat behavior and use gentle, stress-free techniques.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-card text-center h-100">
                        <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                        <h5>Safe Products</h5>
                        <p>Only cat-safe, premium products used for all grooming and spa treatments.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-card text-center h-100">
                        <i class="fas fa-clock fa-3x text-info mb-3"></i>
                        <h5>Flexible Scheduling</h5>
                        <p>Multiple time slots available to fit your busy schedule.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row">
        <div class="col-12">
            <div class="cta-section bg-light rounded-lg p-4 text-center">
                <h3 class="mb-3">Ready to Pamper Your Cat?</h3>
                <p class="mb-4">Book a spa appointment today and give your feline friend the luxury treatment they deserve.</p>
                @auth
                    <a href="{{ route('booking.spa.form') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-plus"></i> Book Spa Service
                    </a>
                @else
                    <a href="{{ route('auth.login') }}" class="btn btn-primary btn-lg me-2">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="{{ route('auth.register') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .service-item-card {
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 0.75rem;
        padding: 2rem;
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .service-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .service-item-card .service-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 2rem;
    }

    .service-item-card .price {
        font-size: 1.25rem;
        font-weight: bold;
        color: #28a745;
    }

    .feature-card {
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        padding: 1.5rem;
        transition: box-shadow 0.2s ease;
    }

    .feature-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .section-title {
        position: relative;
        padding-bottom: 1rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 2px;
    }

    .spa-package-card {
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }

    .spa-package-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .spa-package-card.featured {
        border-color: #28a745;
        box-shadow: 0 5px 25px rgba(40, 167, 69, 0.2);
    }

    .package-price {
        font-size: 2.5rem;
        font-weight: bold;
        color: #28a745;
    }

    .cta-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
</style>
@endpush

@push('scripts')
<script type="module">
$(document).ready(function() {
    loadSpaPackages();
});

function loadSpaPackages() {
    // Load spa packages via API
    fetch('/api/availability/spa-packages')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySpaPackages(data.data);
            } else {
                showPackagesError();
            }
        })
        .catch(error => {
            console.error('Error loading spa packages:', error);
            showPackagesError();
        });
}

function displaySpaPackages(packages) {
    const container = document.getElementById('spaPackages');
    
    if (packages.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center">
                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                <h4 class="text-muted">No spa packages available at this time</h4>
                <p class="text-muted">Please check back later or contact us for custom services.</p>
            </div>
        `;
        return;
    }

    let packagesHtml = '';
    packages.forEach((package, index) => {
        const featured = index === 1 ? 'featured' : ''; // Make middle package featured
        packagesHtml += `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="spa-package-card ${featured}">
                    ${featured ? '<div class="badge bg-success mb-2">Most Popular</div>' : ''}
                    <h4 class="mb-3">${package.name}</h4>
                    <div class="package-price mb-3">$${package.price}</div>
                    <p class="text-muted mb-3">${package.description}</p>
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> ${package.duration_minutes} minutes
                        </small>
                    </div>
                    <div class="d-grid">
                        @auth
                            <a href="{{ route('booking.spa.form') }}?package=${package.id}" 
                               class="btn ${featured ? 'btn-success' : 'btn-outline-primary'}">
                                <i class="fas fa-calendar-plus"></i> Book This Package
                            </a>
                        @else
                            <a href="{{ route('auth.login') }}" 
                               class="btn ${featured ? 'btn-success' : 'btn-outline-primary'}">
                                <i class="fas fa-sign-in-alt"></i> Login to Book
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = packagesHtml;
}

function showPackagesError() {
    const container = document.getElementById('spaPackages');
    container.innerHTML = `
        <div class="col-12 text-center">
            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
            <h4 class="text-muted">Unable to load spa packages</h4>
            <p class="text-muted">Please refresh the page or contact us for assistance.</p>
            <button class="btn btn-outline-primary" onclick="loadSpaPackages()">
                <i class="fas fa-redo"></i> Try Again
            </button>
        </div>
    `;
}
</script>
@endpush