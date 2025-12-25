@extends('layouts.app')

@section('title', 'Book Spay/Neuter Service - Pawtel')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="booking-progress mb-4">
                    <div class="progress-steps">
                        <div class="step completed">1. Select Package</div>
                        <div class="step active">2. Pet Details</div>
                        <div class="step">3. Confirmation</div>
                    </div>
                </div>

                <div class="booking-form-card">
                    <h3 class="mb-4">Book Spay/Neuter Service</h3>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="spayBookingForm" action="{{ route('booking.spay.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Package Selection -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-heartbeat text-primary me-2"></i>Select Surgical Package
                            </h5>
                            <div class="row">
                                @forelse($packages as $package)
                                    <div class="col-md-6 mb-3">
                                        <div class="package-card border rounded p-3 {{ old('spay_package_id') == $package->id ? 'border-primary bg-light' : '' }}"
                                            onclick="selectPackage({{ $package->id }})" style="cursor: pointer;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="spay_package_id" 
                                                    value="{{ $package->id }}" id="package_{{ $package->id }}"
                                                    {{ old('spay_package_id') == $package->id ? 'checked' : '' }}
                                                    required>
                                                <label class="form-check-label w-100" for="package_{{ $package->id }}">
                                                    <h6 class="mb-2">
                                                        {{ $package->name }}
                                                        @if($package->type)
                                                            <span class="badge bg-{{ $package->type == 'spay' ? 'danger' : 'primary' }} ms-2">
                                                                {{ ucfirst($package->type) }}
                                                            </span>
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted small mb-2">{{ $package->description }}</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        @if($package->post_care_days)
                                                            <span class="badge bg-info">{{ $package->post_care_days }} days post-care</span>
                                                        @endif
                                                        <strong class="text-primary">৳{{ number_format($package->price, 2) }}</strong>
                                                    </div>
                                                    @if($package->resident_price)
                                                        <small class="text-muted d-block mt-2">
                                                            Resident Rate: ৳{{ number_format($package->resident_price, 2) }}
                                                        </small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            No surgical packages available at this time. Please check back later.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Procedure Date -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Procedure Date
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="preferred_date" class="form-label">Preferred Date *</label>
                                    <input type="date" class="form-control" id="preferred_date" 
                                        name="preferred_date" 
                                        min="{{ date('Y-m-d') }}" 
                                        value="{{ old('preferred_date') }}"
                                        required>
                                    <small class="text-muted">Please note: Your cat must fast for 12 hours before the procedure.</small>
                                </div>
                            </div>
                            <div id="availabilityMessage" class="mt-2"></div>
                        </div>

                        <!-- Pet Information -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-paw text-primary me-2"></i>Pet Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="cat_age" class="form-label">Cat Age (months) *</label>
                                    <input type="number" class="form-control" id="cat_age" 
                                        name="cat_age" 
                                        min="3" 
                                        value="{{ old('cat_age') }}"
                                        required>
                                    <small class="text-muted">Minimum age: 3 months</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="cat_weight" class="form-label">Cat Weight (kg) *</label>
                                    <input type="number" class="form-control" id="cat_weight" 
                                        name="cat_weight" 
                                        min="1" 
                                        step="0.1"
                                        value="{{ old('cat_weight') }}"
                                        required>
                                    <small class="text-muted">Minimum weight: 1 kg</small>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Information -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-stethoscope text-primary me-2"></i>Medical Information
                            </h5>
                            <div class="mb-3">
                                <label for="medical_conditions" class="form-label">Medical Conditions / Notes</label>
                                <textarea class="form-control" id="medical_conditions" 
                                    name="medical_conditions" rows="3"
                                    placeholder="Please mention any existing medical conditions, allergies, or medications your cat is currently taking...">{{ old('medical_conditions') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="special_requests" class="form-label">Special Requests</label>
                                <textarea class="form-control" id="special_requests" 
                                    name="special_requests" rows="2"
                                    placeholder="Any special requests or concerns...">{{ old('special_requests') }}</textarea>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-file-upload text-primary me-2"></i>Documents (Optional)
                            </h5>
                            <p class="text-muted small mb-3">You can upload documents after booking confirmation.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="vaccination_certificate" class="form-label">Vaccination Certificate</label>
                                    <input type="file" class="form-control" id="vaccination_certificate"
                                        name="vaccination_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label for="medical_records" class="form-label">Medical Records</label>
                                    <input type="file" class="form-control" id="medical_records" name="medical_records"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                        </div>

                        <!-- Important Notice -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Important Information</h6>
                            <ul class="mb-0 small">
                                <li>Your cat must fast for 12 hours before the procedure (water is okay)</li>
                                <li>Please arrive 30 minutes before the scheduled time</li>
                                <li>Bring any current medications your cat is taking</li>
                                <li>Recovery time is typically 7-10 days with restricted activity</li>
                            </ul>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="booking-total mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-calculator text-primary me-2"></i>Price Breakdown
                            </h5>
                            <div class="total-breakdown bg-light p-3 rounded">
                                <div id="priceBreakdown">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Surgical Package:</span>
                                        <strong id="packagePrice">৳0.00</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span class="h5 mb-0">Total Amount:</span>
                                        <strong class="h5 mb-0 text-primary" id="totalAmount">৳0.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-check me-2"></i>
                            Confirm Booking
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="booking-summary-sidebar bg-light p-4 rounded mb-4">
                    <h4 class="mb-3">Booking Summary</h4>
                    <div id="sidebarSummary">
                        <p class="text-muted">Select a package to see summary.</p>
                    </div>
                </div>

                <div class="info-card bg-light p-4 rounded mb-4">
                    <h5 class="mb-3">Pre-Surgery Checklist</h5>
                    <ul class="small mb-0">
                        <li>✓ Cat is at least 3 months old</li>
                        <li>✓ Cat weighs at least 1 kg</li>
                        <li>✓ Fast for 12 hours before</li>
                        <li>✓ Bring vaccination records</li>
                    </ul>
                </div>

                <div class="info-card bg-light p-4 rounded">
                    <h5 class="mb-3">Need Help?</h5>
                    <p class="small mb-2">Our team is here to assist you.</p>
                    <a href="tel:+8801733191556" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-phone me-2"></i>Call Us
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .booking-progress {
        margin-bottom: 2rem;
    }
    .progress-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .step {
        flex: 1;
        padding: 0.75rem;
        text-align: center;
        background: #e9ecef;
        color: #6c757d;
        border-radius: 0.5rem;
        margin: 0 0.25rem;
        font-weight: 500;
    }
    .step.completed {
        background: #28a745;
        color: white;
    }
    .step.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .booking-form-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .section-title {
        color: #333;
        font-weight: 600;
        border-bottom: 2px solid #667eea;
        padding-bottom: 0.5rem;
    }
    .package-card:hover {
        border-color: #667eea !important;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
    }
    .package-card.border-primary {
        border-width: 2px !important;
    }
</style>
@endpush

@push('scripts')
<script type="module">
$(document).ready(function() {
    const packages = @json($packages ?? []);
    
    // Get package from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const packageId = urlParams.get('package');
    if (packageId) {
        $('#package_' + packageId).prop('checked', true).trigger('change');
        selectPackage(packageId);
    }
    
    // Calculate initial price
    calculatePrice();
    
    // Update price when package changes
    $('input[name="spay_package_id"]').on('change', function() {
        selectPackage($(this).val());
        calculatePrice();
    });
    
    // Check availability when date changes
    $('#preferred_date').on('change', function() {
        checkAvailability();
    });
    
    function selectPackage(packageId) {
        $('.package-card').removeClass('border-primary bg-light');
        $('.package-card').has(`#package_${packageId}`).addClass('border-primary bg-light');
        calculatePrice();
    }
    
    function calculatePrice() {
        const selectedPackage = $('input[name="spay_package_id"]:checked');
        if (!selectedPackage.length) {
            $('#packagePrice').text('৳0.00');
            $('#totalAmount').text('৳0.00');
            return;
        }
        
        const packageId = selectedPackage.val();
        const package = packages.find(p => p.id == packageId);
        if (!package) return;
        
        // Check if user is resident (simplified - would need API call)
        const isResident = false; // TODO: Check from API
        const packagePrice = isResident && package.resident_price ? parseFloat(package.resident_price) : parseFloat(package.price);
        
        // Update display
        $('#packagePrice').text('৳' + packagePrice.toFixed(2));
        $('#totalAmount').text('৳' + packagePrice.toFixed(2));
        
        // Update sidebar
        updateSidebar(package);
    }
    
    function updateSidebar(package) {
        const date = $('#preferred_date').val();
        const age = $('#cat_age').val();
        const weight = $('#cat_weight').val();
        
        let html = `
            <div class="mb-3">
                <strong>Package:</strong> ${package.name}
            </div>
        `;
        
        if (date) {
            html += `<div class="mb-3"><strong>Date:</strong><br>${new Date(date).toLocaleDateString()}</div>`;
        }
        if (age) {
            html += `<div class="mb-3"><strong>Cat Age:</strong> ${age} months</div>`;
        }
        if (weight) {
            html += `<div class="mb-3"><strong>Cat Weight:</strong> ${weight} kg</div>`;
        }
        
        $('#sidebarSummary').html(html);
    }
    
    function checkAvailability() {
        const date = $('#preferred_date').val();
        const packageId = $('input[name="spay_package_id"]:checked').val();
        
        if (!date || !packageId) {
            $('#availabilityMessage').html('');
            return;
        }
        
        // TODO: Make API call to check availability
        $('#availabilityMessage').html('<small class="text-info"><i class="fas fa-info-circle"></i> Checking availability...</small>');
    }
    
    // Form submission
    $('#spayBookingForm').on('submit', function(e) {
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
    });
});

window.selectPackage = function(packageId) {
    $(`#package_${packageId}`).prop('checked', true).trigger('change');
};
</script>
@endpush

