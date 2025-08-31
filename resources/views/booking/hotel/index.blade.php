@extends('layouts.app')

@section('title', 'Cat Hotel Booking - Pawtel')

@section('content')
    <div class="booking-hero py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-4 text-gradient mb-3">Cat Hotel Booking</h1>
                    <p class="lead">Premium boarding facilities for your beloved cats</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="booking-form-card">
                    <h3 class="mb-4">Check Availability</h3>

                    <form id="availabilityForm" action="{{ route('booking.hotel.availability') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="check_in_date" class="form-label">Check-in Date</label>
                                    <input type="date" class="form-control" id="check_in_date" name="check_in_date"
                                        min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="check_out_date" class="form-label">Check-out Date</label>
                                    <input type="date" class="form-control" id="check_out_date" name="check_out_date"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="room_type_id" class="form-label">Room Type</label>
                            <select class="form-select" id="room_type_id" name="room_type_id" required>
                                <option value="">Select Room Type</option>
                                <!-- Will be populated via API -->
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Check Availability
                        </button>
                    </form>

                    <div id="availabilityResults" class="mt-4" style="display: none;">
                        <!-- Results will be shown here -->
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="info-card">
                    <h4 class="mb-3">Our Features</h4>
                    <ul class="feature-list">
                        <li><i class="fas fa-check text-success me-2"></i>24/7 Professional Care</li>
                        <li><i class="fas fa-check text-success me-2"></i>Individual Climate-Controlled Rooms</li>
                        <li><i class="fas fa-check text-success me-2"></i>Daily Exercise & Playtime</li>
                        <li><i class="fas fa-check text-success me-2"></i>Premium Food & Treats</li>
                        <li><i class="fas fa-check text-success me-2"></i>Medical Care Available</li>
                        <li><i class="fas fa-check text-success me-2"></i>Daily Photo Updates</li>
                    </ul>
                </div>

                <div class="contact-card mt-4">
                    <h4 class="mb-3">Need Help?</h4>
                    <p>Call us for immediate assistance</p>
                    <a href="tel:+8801733191556" class="btn btn-outline-primary">
                        <i class="fas fa-phone me-2"></i>
                        +8801733191556
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modules/booking.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Booking.initHotelBooking();
        });
    </script>
@endpush
