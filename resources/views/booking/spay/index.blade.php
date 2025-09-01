{{-- resources/views/booking/spay/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Spay & Neuter Services')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="hero-section bg-gradient-medical text-white rounded-lg p-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 mb-3">
                            <i class="fas fa-heartbeat text-danger"></i> Spay & Neuter Services
                        </h1>
                        <p class="lead mb-4">
                            Professional spay and neuter procedures performed by experienced veterinarians. 
                            We provide safe, affordable surgical services with comprehensive pre and post-operative care.
                        </p>
                        @auth
                            <a href="{{ route('booking.spay.form') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-calendar-plus"></i> Schedule Procedure
                            </a>
                        @else
                            <a href="{{ route('auth.login') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Login to Book
                            </a>
                        @endauth
                    </div>
                    <div class="col-lg-4 text-center">
                        <i class="fas fa-stethoscope fa-8x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Information Alert -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="alert-heading">Important Information</h4>
                        <p class="mb-2">
                            <strong>Fasting Required:</strong> Your cat must fast for 12 hours before the procedure (water is okay).
                        </p>
                        <p class="mb-2">
                            <strong>Age Requirements:</strong> Cats should be at least 4 months old and weigh at least 2 pounds.
                        </p>
                        <p class="mb-0">
                            <strong>Recovery Time:</strong> Plan for 7-10 days of restricted activity and monitoring.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Packages -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="section-title text-center mb-5">
                <i class="fas fa-medical-kit text-primary"></i> Our Surgical Packages
            </h2>
            
            <div class="row" id="spayPackages">
                <!-- Loading placeholder -->
                <div class="col-12 text-center" id="loadingPackages">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">Loading surgical packages...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Procedure Information -->
    <div class="row mb-5">
        <div class="col-lg-6 mb-4">
            <div class="info-card h-100">
                <div class="info-header bg-pink">
                    <i class="fas fa-venus fa-2x text-white"></i>
                    <h3 class="text-white mt-2">Spay Surgery (Female Cats)</h3>
                </div>
                <div class="info-body">
                    <h5 class="mb-3">What is Spaying?</h5>
                    <p>Spaying is the surgical removal of the ovaries and uterus, preventing heat cycles and eliminating the risk of ovarian and uterine cancers.</p>
                    
                    <h6 class="mt-4 mb-2">Benefits:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i> Prevents unwanted pregnancies</li>
                        <li><i class="fas fa-check text-success me-2"></i> Eliminates heat cycles and yowling</li>
                        <li><i class="fas fa-check text-success me-2"></i> Reduces risk of mammary tumors</li>
                        <li><i class="fas fa-check text-success me-2"></i> Prevents uterine infections</li>
                        <li><i class="fas fa-check text-success me-2"></i> May reduce territorial marking</li>
                    </ul>

                    <div class="price-info mt-4">
                        <span class="price-label">Starting at:</span>
                        <span class="price-value">$120</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="info-card h-100">
                <div class="info-header bg-blue">
                    <i class="fas fa-mars fa-2x text-white"></i>
                    <h3 class="text-white mt-2">Neuter Surgery (Male Cats)</h3>
                </div>
                <div class="info-body">
                    <h5 class="mb-3">What is Neutering?</h5>
                    <p>Neutering is the surgical removal of the testicles, which eliminates the production of male hormones and prevents reproduction.</p>
                    
                    <h6 class="mt-4 mb-2">Benefits:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i> Prevents unwanted breeding</li>
                        <li><i class="fas fa-check text-success me-2"></i> Reduces aggressive behavior</li>
                        <li><i class="fas fa-check text-success me-2"></i> Eliminates risk of testicular cancer</li>
                        <li><i class="fas fa-check text-success me-2"></i> Reduces urine marking and spraying</li>
                        <li><i class="fas fa-check text-success me-2"></i> Less likely to roam and fight</li>
                    </ul>

                    <div class="price-info mt-4">
                        <span class="price-label">Starting at:</span>
                        <span class="price-value">$90</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Timeline -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="text-center mb-5">
                <i class="fas fa-clipboard-list text-primary"></i> Our Surgical Process
            </h3>

            <div class="process-timeline">
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="process-step text-center">
                            <div class="step-number">1</div>
                            <div class="step-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h5>Schedule</h5>
                            <p>Book your appointment online or call us. We'll provide pre-surgery instructions.</p>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="process-step text-center">
                            <div class="step-number">2</div>
                            <div class="step-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <h5>Pre-Surgery</h5>
                            <p>Health examination and pre-surgical assessment to ensure your cat is ready.</p>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="process-step text-center">
                            <div class="step-number">3</div>
                            <div class="step-icon">
                                <i class="fas fa-cut"></i>
                            </div>
                            <h5>Surgery</h5>
                            <p>Safe, professional surgery performed by experienced veterinarians.</p>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="process-step text-center">
                            <div class="step-number">4</div>
                            <div class="step-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <h5>Recovery</h5>
                            <p>Take your cat home with detailed aftercare instructions and follow-up support.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="text-center mb-5">
                <i class="fas fa-question-circle text-primary"></i> Frequently Asked Questions
            </h3>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    What age should my cat be spayed or neutered?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We recommend spaying or neutering cats between 4-6 months of age, before they reach sexual maturity. However, healthy adult cats can also be safely spayed or neutered.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    How long is the recovery period?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Most cats recover within 7-10 days. During this time, it's important to limit their activity and monitor the incision site. We provide detailed aftercare instructions and pain medication.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Is the procedure safe?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, spay and neuter procedures are very safe when performed by experienced veterinarians. We use modern anesthetics and surgical techniques, and complications are rare.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    What's included in the surgical package?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Our packages include the surgical procedure, anesthesia, pain medication, e-collar, and post-operative instructions. Some packages also include pre-surgical bloodwork and follow-up visits.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row">
        <div class="col-12">
            <div class="cta-section bg-light rounded-lg p-4 text-center">
                <h3 class="mb-3">Ready to Schedule Your Cat's Surgery?</h3>
                <p class="mb-4">Take the responsible step towards your cat's health and well-being. Our experienced team is here to help.</p>
                @auth
                    <a href="{{ route('booking.spay.form') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-plus"></i> Schedule Procedure
                    </a>
                @else
                    <a href="{{ route('auth.login') }}" class="btn btn-primary btn-lg me-2">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="{{ route('auth.register') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                @endauth
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-phone"></i> Questions? Call us at (555) 123-4567
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hero-section.bg-gradient-medical {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .info-card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    .info-card:hover {
        transform: translateY(-5px);
    }

    .info-header {
        padding: 2rem;
        text-align: center;
    }

    .info-header.bg-pink {
        background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
    }

    .info-header.bg-blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .info-body {
        padding: 2rem;
    }

    .price-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
    }

    .price-label {
        font-weight: 500;
        color: #6c757d;
    }

    .price-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #28a745;
    }

    .process-timeline {
        position: relative;
    }

    .process-step {
        position: relative;
        z-index: 2;
    }

    .step-number {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.25rem;
        margin: 0 auto 1rem;
    }

    .step-icon {
        width: 80px;
        height: 80px;
        background: #f8f9fa;
        border: 3px solid #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
        color: #667eea;
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

    .spay-package-card {
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }

    .spay-package-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .spay-package-card.featured {
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

    .accordion-button {
        font-weight: 500;
    }

    .accordion-button:not(.collapsed) {
        background-color: #667eea;
        color: white;
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }
</style>
@endpush

@push('scripts')
<script type="module">
$(document).ready(function() {
    loadSpayPackages();
});

function loadSpayPackages() {
    // Load spay/neuter packages via API
    fetch('/api/availability/spay-packages')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySpayPackages(data.data);
            } else {
                showPackagesError();
            }
        })
        .catch(error => {
            console.error('Error loading spay packages:', error);
            showPackagesError();
        });
}

function displaySpayPackages(packages) {
    const container = document.getElementById('spayPackages');
    
    if (packages.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center">
                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                <h4 class="text-muted">No surgical packages available at this time</h4>
                <p class="text-muted">Please contact us to schedule a consultation.</p>
            </div>
        `;
        return;
    }

    let packagesHtml = '';
    packages.forEach((package, index) => {
        const featured = index === 1 ? 'featured' : ''; // Make middle package featured
        const packageType = package.name.toLowerCase().includes('spay') ? 'spay' : 'neuter';
        const iconClass = packageType === 'spay' ? 'fas fa-venus text-pink' : 'fas fa-mars text-blue';
        
        packagesHtml += `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="spay-package-card ${featured}">
                    ${featured ? '<div class="badge bg-success mb-2">Recommended</div>' : ''}
                    <div class="mb-3">
                        <i class="${iconClass} fa-3x"></i>
                    </div>
                    <h4 class="mb-3">${package.name}</h4>
                    <div class="package-price mb-3">${package.price}</div>
                    <p class="text-muted mb-3">${package.description}</p>
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> ${package.duration_minutes} minutes procedure
                        </small>
                    </div>
                    <div class="d-grid">
                        @auth
                            <a href="{{ route('booking.spay.form') }}?package=${package.id}" 
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
    const container = document.getElementById('spayPackages');
    container.innerHTML = `
        <div class="col-12 text-center">
            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
            <h4 class="text-muted">Unable to load surgical packages</h4>
            <p class="text-muted">Please refresh the page or contact us for assistance.</p>
            <button class="btn btn-outline-primary" onclick="loadSpayPackages()">
                <i class="fas fa-redo"></i> Try Again
            </button>
        </div>
    `;
}
</script>
@endpush