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

                    <form id="hotelBookingForm" action="{{ route('booking.hotel.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Hidden fields for booking params -->
                        @if (session('booking_params'))
                            @foreach (session('booking_params') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        @endif

                        <div class="section mb-4">
                            <h5 class="section-title">Booking Details</h5>
                            <div class="booking-summary">
                                <!-- Booking summary will be shown here -->
                            </div>
                        </div>

                        <div class="section mb-4">
                            <h5 class="section-title">Additional Services</h5>
                            <div id="addonServices" class="addon-services">
                                <!-- Addon services will be loaded here -->
                            </div>
                        </div>

                        <div class="section mb-4">
                            <h5 class="section-title">Special Requests</h5>
                            <textarea class="form-control" name="special_requests" rows="3"
                                placeholder="Any special requirements for your cat..."></textarea>
                        </div>

                        <div class="section mb-4">
                            <h5 class="section-title">Documents (Optional)</h5>
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

                        <div class="booking-total mb-4">
                            <div class="total-breakdown">
                                <!-- Price breakdown will be shown here -->
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check me-2"></i>
                            Confirm Booking
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="booking-summary-sidebar">
                    <h4>Booking Summary</h4>
                    <!-- Summary details -->
                </div>
            </div>
        </div>
    </div>
@endsection
