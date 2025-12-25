@extends('layouts.app')

@section('title', 'Complete Hotel Booking - Pawtel')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="booking-progress mb-4">
                    <div class="progress-steps">
                        <div class="step completed">1. Availability</div>
                        <div class="step active">2. Details</div>
                        <div class="step">3. Confirmation</div>
                    </div>
                </div>

                <div class="booking-form-card">
                    <h3 class="mb-4">Complete Your Booking</h3>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <h6><i class="fas fa-exclamation-circle me-2"></i>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form id="hotelBookingForm" action="{{ route('booking.hotel.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Hidden fields for booking params -->
                        @if (session('booking_params'))
                            @foreach (session('booking_params') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        @elseif(old('check_in_date'))
                            <input type="hidden" name="check_in_date" value="{{ old('check_in_date') }}">
                            <input type="hidden" name="check_out_date" value="{{ old('check_out_date') }}">
                            <input type="hidden" name="room_type_id" value="{{ old('room_type_id') }}">
                        @endif

                        <!-- Booking Details Section -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Booking Details
                            </h5>
                            <div class="booking-summary bg-light p-3 rounded">
                                @php
                                    $bookingParams = session('booking_params', []);
                                    $checkIn = $bookingParams['check_in_date'] ?? old('check_in_date');
                                    $checkOut = $bookingParams['check_out_date'] ?? old('check_out_date');
                                    $roomTypeId = $bookingParams['room_type_id'] ?? old('room_type_id');
                                    $roomType = collect($roomTypes)->firstWhere('id', $roomTypeId);
                                @endphp
                                
                                @if($checkIn && $checkOut && $roomType)
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <strong>Check-in:</strong> {{ \Carbon\Carbon::parse($checkIn)->format('M d, Y') }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Check-out:</strong> {{ \Carbon\Carbon::parse($checkOut)->format('M d, Y') }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Room Type:</strong> {{ $roomType->name ?? 'N/A' }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Duration:</strong> 
                                            @php
                                                $days = \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut)) + 1;
                                            @endphp
                                            {{ $days }} {{ $days == 1 ? 'day' : 'days' }}
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">Please select dates and room type from the availability page.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Additional Services Section -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-plus-circle text-primary me-2"></i>Additional Services
                            </h5>
                            <div id="addonServices" class="addon-services">
                                @if(!empty($addonServices))
                                    @foreach($addonServices as $addon)
                                        <div class="form-check mb-3 p-3 border rounded">
                                            <input class="form-check-input addon-checkbox" type="checkbox" 
                                                name="addons[]" value="{{ $addon->id }}" 
                                                id="addon_{{ $addon->id }}"
                                                data-price="{{ $addon->price }}">
                                            <label class="form-check-label w-100" for="addon_{{ $addon->id }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $addon->name }}</strong>
                                                        @if($addon->description)
                                                            <br><small class="text-muted">{{ $addon->description }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-primary">৳{{ number_format($addon->price, 2) }}</span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No additional services available at this time.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Special Requests Section -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-comment-alt text-primary me-2"></i>Special Requests
                            </h5>
                            <textarea class="form-control" name="special_requests" rows="3"
                                placeholder="Any special requirements for your cat (dietary needs, medication, etc.)...">{{ old('special_requests') }}</textarea>
                        </div>

                        <!-- Documents Section -->
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

                        <!-- Price Breakdown -->
                        <div class="booking-total mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-calculator text-primary me-2"></i>Price Breakdown
                            </h5>
                            <div class="total-breakdown bg-light p-3 rounded">
                                <div id="priceBreakdown">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Room Charges:</span>
                                        <strong id="roomCharges">৳0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Additional Services:</span>
                                        <strong id="addonCharges">৳0.00</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <strong id="subtotal">৳0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Discount:</span>
                                        <strong id="discount" class="text-success">-৳0.00</strong>
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
                        @if($checkIn && $checkOut && $roomType)
                            <div class="mb-3">
                                <strong>Room:</strong> {{ $roomType['name'] }}
                            </div>
                            <div class="mb-3">
                                <strong>Check-in:</strong><br>
                                {{ \Carbon\Carbon::parse($checkIn)->format('M d, Y') }}
                            </div>
                            <div class="mb-3">
                                <strong>Check-out:</strong><br>
                                {{ \Carbon\Carbon::parse($checkOut)->format('M d, Y') }}
                            </div>
                            <div class="mb-3">
                                <strong>Duration:</strong> {{ $days ?? 0 }} {{ ($days ?? 0) == 1 ? 'day' : 'days' }}
                            </div>
                        @else
                            <p class="text-muted">Complete the booking form to see summary.</p>
                        @endif
                    </div>
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
    .addon-checkbox:checked + label {
        background-color: #e7f3ff;
    }
</style>
@endpush

@push('scripts')
<script type="module">
$(document).ready(function() {
    const bookingParams = @json(session('booking_params', []));
    const roomTypes = @json($roomTypes);
    const addonServices = @json($addonServices ?? []);
    
    // Calculate initial price
    calculatePrice();
    
    // Update price when addons change
    $('.addon-checkbox').on('change', function() {
        calculatePrice();
    });
    
    function calculatePrice() {
        if (!bookingParams.check_in_date || !bookingParams.check_out_date || !bookingParams.room_type_id) {
            return;
        }
        
        const checkIn = new Date(bookingParams.check_in_date);
        const checkOut = new Date(bookingParams.check_out_date);
        const days = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24)) + 1;
        
        const roomType = roomTypes.find(r => r.id == bookingParams.room_type_id);
        if (!roomType) return;
        
        const baseRate = parseFloat(roomType.base_daily_rate || 0);
        const roomCharges = baseRate * days;
        
        // Calculate addon charges
        let addonCharges = 0;
        $('.addon-checkbox:checked').each(function() {
            const price = parseFloat($(this).data('price') || 0);
            addonCharges += price;
        });
        
        const subtotal = roomCharges + addonCharges;
        
        // Apply discounts based on days
        let discount = 0;
        if (days >= 30) {
            // Monthly discount (custom, admin sets)
            discount = subtotal * 0.1; // 10% placeholder
        } else if (days >= 10) {
            discount = (baseRate - 400) * days; // 400/day for 10+ days
        } else if (days > 7) {
            discount = (baseRate - 450) * days; // 450/day for 7+ days
        }
        
        const total = subtotal - discount;
        
        // Update display
        $('#roomCharges').text('৳' + roomCharges.toFixed(2));
        $('#addonCharges').text('৳' + addonCharges.toFixed(2));
        $('#subtotal').text('৳' + subtotal.toFixed(2));
        $('#discount').text('-৳' + discount.toFixed(2));
        $('#totalAmount').text('৳' + total.toFixed(2));
    }
    
    // Form submission - let it submit normally, just show loading
    $('#hotelBookingForm').on('submit', function(e) {
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        
        // Validate required fields
        const checkIn = $('input[name="check_in_date"]').val();
        const checkOut = $('input[name="check_out_date"]').val();
        const roomType = $('input[name="room_type_id"]').val();
        
        if (!checkIn || !checkOut || !roomType) {
            e.preventDefault();
            alert('Please complete all required fields before submitting.');
            return false;
        }
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
        
        // Form will submit normally
    });
});
</script>
@endpush
