{{-- resources/views/admin/bookings/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Create Manual Booking - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Create Manual Booking</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                    <li class="breadcrumb-item active">Create Booking</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.bookings.store') }}" id="manualBookingForm">
        @csrf
        
        <div class="row">
            <!-- Booking Type Selection -->
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clipboard-list"></i> Booking Type
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input booking-type" type="radio" name="booking_type" 
                                           value="hotel" id="typeHotel" required>
                                    <label class="form-check-label" for="typeHotel">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-hotel fa-2x text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Hotel Booking</h6>
                                                <small class="text-muted">Pet boarding and accommodation</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input booking-type" type="radio" name="booking_type" 
                                           value="spa" id="typeSpa" required>
                                    <label class="form-check-label" for="typeSpa">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-spa fa-2x text-success me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Spa Booking</h6>
                                                <small class="text-muted">Grooming and spa services</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input booking-type" type="radio" name="booking_type" 
                                           value="spay_neuter" id="typeSpayNeuter" required>
                                    <label class="form-check-label" for="typeSpayNeuter">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-heartbeat fa-2x text-danger me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Spay/Neuter</h6>
                                                <small class="text-muted">Medical procedures</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user"></i> Customer Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Search Existing Customer</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="customerSearch" 
                                       placeholder="Search by name, email, or phone">
                                <button type="button" class="btn btn-outline-primary" id="searchCustomer">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <input type="hidden" name="customer_id" id="selectedCustomerId">
                        </div>

                        <div class="alert alert-info" id="customerInfo" style="display: none;">
                            <div id="customerDetails"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="clearCustomer">
                                Select Different Customer
                            </button>
                        </div>

                        <div id="newCustomerForm">
                            <hr>
                            <h6>Or Create New Customer</h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="customer_first_name" 
                                           placeholder="Customer's first name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="customer_last_name" 
                                           placeholder="Customer's last name">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="customer_email" 
                                       placeholder="customer@example.com">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="customer_phone" 
                                       placeholder="(555) 123-4567">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pet Information -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-paw"></i> Pet Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">Pet Name</label>
                            <input type="text" class="form-control" name="pet_name" 
                                   placeholder="Pet's name" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pet Type</label>
                                <select class="form-select" name="pet_type">
                                    <option value="">Select Pet Type</option>
                                    <option value="dog">Dog</option>
                                    <option value="cat">Cat</option>
                                    <option value="bird">Bird</option>
                                    <option value="rabbit">Rabbit</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Breed</label>
                                <input type="text" class="form-control" name="pet_breed" 
                                       placeholder="Pet's breed">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Age</label>
                                <input type="number" class="form-control" name="pet_age" 
                                       placeholder="Age in years" min="0" max="30">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Weight</label>
                                <input type="number" class="form-control" name="pet_weight" 
                                       placeholder="Weight in lbs" min="0" step="0.1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Gender</label>
                                <select class="form-select" name="pet_gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Special Instructions</label>
                            <textarea class="form-control" name="pet_instructions" rows="3" 
                                      placeholder="Any special care instructions, medical conditions, etc."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details - Hotel -->
            <div class="col-12" id="hotelBookingDetails" style="display: none;">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-hotel"></i> Hotel Booking Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label required">Check-in Date</label>
                                <input type="date" class="form-control" name="checkin_date" 
                                       min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label required">Check-out Date</label>
                                <input type="date" class="form-control" name="checkout_date" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Room Type</label>
                                <select class="form-select" name="room_type">
                                    <option value="">Select Room Type</option>
                                    <option value="standard">Standard Room</option>
                                    <option value="deluxe">Deluxe Room</option>
                                    <option value="suite">Suite</option>
                                    <option value="vip">VIP Suite</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Number of Pets</label>
                                <input type="number" class="form-control" name="number_of_pets" 
                                       value="1" min="1" max="5">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details - Spa -->
            <div class="col-12" id="spaBookingDetails" style="display: none;">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-spa"></i> Spa Booking Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label required">Service Date</label>
                                <input type="date" class="form-control" name="service_date" 
                                       min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required">Service Time</label>
                                <input type="time" class="form-control" name="service_time">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Duration (hours)</label>
                                <select class="form-select" name="service_duration">
                                    <option value="1">1 hour</option>
                                    <option value="2">2 hours</option>
                                    <option value="3">3 hours</option>
                                    <option value="4">4 hours</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Services</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="services[]" 
                                               value="basic_grooming" id="basicGrooming">
                                        <label class="form-check-label" for="basicGrooming">
                                            Basic Grooming
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="services[]" 
                                               value="premium_grooming" id="premiumGrooming">
                                        <label class="form-check-label" for="premiumGrooming">
                                            Premium Grooming
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="services[]" 
                                               value="nail_trimming" id="nailTrimming">
                                        <label class="form-check-label" for="nailTrimming">
                                            Nail Trimming
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="services[]" 
                                               value="teeth_cleaning" id="teethCleaning">
                                        <label class="form-check-label" for="teethCleaning">
                                            Teeth Cleaning
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="services[]" 
                                               value="flea_treatment" id="fleaTreatment">
                                        <label class="form-check-label" for="fleaTreatment">
                                            Flea Treatment
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="services[]" 
                                               value="aromatherapy" id="aromatherapy">
                                        <label class="form-check-label" for="aromatherapy">
                                            Aromatherapy Bath
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details - Spay/Neuter -->
            <div class="col-12" id="spayNeuterBookingDetails" style="display: none;">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-heartbeat"></i> Spay/Neuter Booking Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label required">Procedure Date</label>
                                <input type="date" class="form-control" name="procedure_date" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Procedure Type</label>
                                <select class="form-select" name="procedure_type">
                                    <option value="">Select Procedure</option>
                                    <option value="spay">Spay (Female)</option>
                                    <option value="neuter">Neuter (Male)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Veterinarian</label>
                                <select class="form-select" name="veterinarian">
                                    <option value="">Select Veterinarian</option>
                                    <option value="dr_smith">Dr. Smith</option>
                                    <option value="dr_johnson">Dr. Johnson</option>
                                    <option value="dr_williams">Dr. Williams</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pre-procedure Instructions</label>
                            <textarea class="form-control" name="pre_instructions" rows="3" 
                                      placeholder="Any special instructions before the procedure"></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Important:</strong> Pet must fast for 12 hours before the procedure. 
                            Please inform the customer about pre-procedure requirements.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment & Notes -->
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-credit-card"></i> Payment & Additional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="total_amount" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Payment Status</label>
                                <select class="form-select" name="payment_status">
                                    <option value="pending">Pending</option>
                                    <option value="partial">Partially Paid</option>
                                    <option value="paid">Fully Paid</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" name="payment_method">
                                    <option value="">Select Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Credit/Debit Card</option>
                                    <option value="check">Check</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Booking Status</label>
                                <select class="form-select" name="booking_status">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Admin Notes</label>
                            <textarea class="form-control" name="admin_notes" rows="3" 
                                      placeholder="Internal notes about this booking (not visible to customer)"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Customer Notes</label>
                            <textarea class="form-control" name="customer_notes" rows="3" 
                                      placeholder="Notes visible to customer"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Booking
                    </button>
                    <button type="submit" name="send_confirmation" value="1" class="btn btn-success">
                        <i class="fas fa-envelope"></i> Create & Send Confirmation
                    </button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script type="module">
$(document).ready(function() {
    // Show/hide booking details based on type selection
    $('.booking-type').change(function() {
        const bookingType = $(this).val();
        
        // Hide all booking detail sections
        $('#hotelBookingDetails, #spaBookingDetails, #spayNeuterBookingDetails').hide();
        
        // Show relevant section
        if (bookingType === 'hotel') {
            $('#hotelBookingDetails').show();
            $('input[name="checkin_date"], input[name="checkout_date"]').attr('required', true);
        } else if (bookingType === 'spa') {
            $('#spaBookingDetails').show();
            $('input[name="service_date"], input[name="service_time"]').attr('required', true);
        } else if (bookingType === 'spay_neuter') {
            $('#spayNeuterBookingDetails').show();
            $('input[name="procedure_date"]').attr('required', true);
        }
    });

    // Customer search functionality
    $('#searchCustomer').click(function() {
        const searchTerm = $('#customerSearch').val();
        if (!searchTerm.trim()) {
            alert('Please enter a search term');
            return;
        }
        
        // Simulate customer search (replace with actual API call)
        searchCustomers(searchTerm);
    });

    $('#clearCustomer').click(function() {
        $('#selectedCustomerId').val('');
        $('#customerInfo').hide();
        $('#newCustomerForm').show();
        $('#customerSearch').val('');
    });

    // Auto-calculate checkout date
    $('input[name="checkin_date"]').change(function() {
        const checkinDate = new Date($(this).val());
        const checkoutDate = new Date(checkinDate);
        checkoutDate.setDate(checkoutDate.getDate() + 1);
        
        $('input[name="checkout_date"]').attr('min', checkoutDate.toISOString().split('T')[0]);
        
        if (!$('input[name="checkout_date"]').val()) {
            $('input[name="checkout_date"]').val(checkoutDate.toISOString().split('T')[0]);
        }
    });

    // Form validation
    $('#manualBookingForm').on('submit', function(e) {
        let isValid = true;
        const bookingType = $('input[name="booking_type"]:checked').val();
        
        if (!bookingType) {
            alert('Please select a booking type');
            isValid = false;
        }

        // Check customer information
        const customerId = $('#selectedCustomerId').val();
        const customerEmail = $('input[name="customer_email"]').val();
        const customerFirstName = $('input[name="customer_first_name"]').val();
        
        if (!customerId && !customerEmail && !customerFirstName) {
            alert('Please select an existing customer or provide new customer information');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function searchCustomers(searchTerm) {
        // This would typically make an AJAX call to search customers
        // For now, simulate a found customer
        setTimeout(function() {
            const mockCustomer = {
                id: 1,
                name: 'John Doe',
                email: 'john@example.com',
                phone: '(555) 123-4567'
            };
            
            $('#selectedCustomerId').val(mockCustomer.id);
            $('#customerDetails').html(`
                <strong>${mockCustomer.name}</strong><br>
                <small>Email: ${mockCustomer.email}</small><br>
                <small>Phone: ${mockCustomer.phone}</small>
            `);
            $('#customerInfo').show();
            $('#newCustomerForm').hide();
        }, 500);
    }
});
</script>
@endpush

@push('styles')
<style>
    .form-check-label {
        cursor: pointer;
        width: 100%;
        padding: 1rem;
        border: 2px solid #dee2e6;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }
    
    .form-check-input:checked + .form-check-label {
        border-color: #0d6efd;
        background-color: #f8f9ff;
    }
    
    .required::after {
        content: ' *';
        color: #dc3545;
    }
    
    .booking-type {
        display: none;
    }
</style>
@endpush