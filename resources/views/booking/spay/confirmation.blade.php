@extends('layouts.app')

@section('title', 'Spay/Neuter Booking Confirmation - Pawtel')

@section('content')
    <div class="container py-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-center mb-4">
                    <div class="success-icon mb-3">
                        <i class="fas fa-check-circle fa-4x text-success"></i>
                    </div>
                    <h2 class="mb-2">Surgery Booking Confirmed!</h2>
                    <p class="text-muted">Your spay/neuter procedure has been successfully scheduled.</p>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Procedure Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Booking Number:</strong><br>
                                <span class="h5 text-primary">#{{ $booking['booking_number'] ?? $booking['id'] }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Status:</strong><br>
                                <span class="badge bg-{{ $booking['status'] === 'confirmed' ? 'success' : 'warning' }} fs-6">
                                    {{ ucfirst($booking['status']) }}
                                </span>
                            </div>
                            @if(isset($booking['spay_booking']))
                                <div class="col-md-6 mb-3">
                                    <strong>Procedure Date:</strong><br>
                                    {{ \Carbon\Carbon::parse($booking['spay_booking']['procedure_date'])->format('M d, Y') }}
                                </div>
                                @if(isset($booking['spay_booking']['spay_package']))
                                    <div class="col-md-6 mb-3">
                                        <strong>Package:</strong><br>
                                        {{ $booking['spay_booking']['spay_package']['name'] }}
                                        @if($booking['spay_booking']['spay_package']['type'])
                                            <span class="badge bg-{{ $booking['spay_booking']['spay_package']['type'] == 'spay' ? 'danger' : 'primary' }}">
                                                {{ ucfirst($booking['spay_booking']['spay_package']['type']) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($booking['spay_booking']['spay_package']['post_care_days'] > 0)
                                        <div class="col-md-12 mb-3">
                                            <strong>Post-Care Duration:</strong><br>
                                            {{ $booking['spay_booking']['spay_package']['post_care_days'] }} days
                                        </div>
                                    @endif
                                @endif
                                @if(!empty($booking['spay_booking']['pet_name']))
                                    <div class="col-md-6 mb-3">
                                        <strong>Pet Name:</strong><br>
                                        {{ $booking['spay_booking']['pet_name'] }}
                                    </div>
                                @endif
                                @if(!empty($booking['spay_booking']['pet_age']))
                                    <div class="col-md-6 mb-3">
                                        <strong>Pet Age:</strong><br>
                                        {{ $booking['spay_booking']['pet_age'] }} {{ is_numeric($booking['spay_booking']['pet_age']) ? 'months' : '' }}
                                    </div>
                                @endif
                                @if(!empty($booking['spay_booking']['pet_weight']))
                                    <div class="col-md-6 mb-3">
                                        <strong>Pet Weight:</strong><br>
                                        {{ $booking['spay_booking']['pet_weight'] }} kg
                                    </div>
                                @endif
                            @else
                                <div class="col-md-6 mb-3">
                                    <strong>Procedure Date:</strong><br>
                                    {{ \Carbon\Carbon::parse($booking['check_in_date'])->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!empty($booking['addons']))
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Additional Services</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                @foreach($booking['addons'] as $addon)
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        {{ $addon['service']['name'] ?? 'Service' }} 
                                        (Qty: {{ $addon['quantity'] ?? 1 }})
                                        <span class="float-end">৳{{ number_format($addon['total_price'] ?? 0, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Payment Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>৳{{ number_format($booking['total_amount'] ?? 0, 2) }}</strong>
                        </div>
                        @if(($booking['discount_amount'] ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Discount:</span>
                                <strong>-৳{{ number_format($booking['discount_amount'] ?? 0, 2) }}</strong>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="h5 mb-0">Total Amount:</span>
                            <strong class="h5 mb-0 text-primary">৳{{ number_format($booking['final_amount'] ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>

                @if(!empty($booking['special_requests']))
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-comment-alt me-2"></i>Special Requests</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $booking['special_requests'] }}</p>
                        </div>
                    </div>
                @endif

                <div class="alert alert-warning">
                    <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Important Pre-Surgery Instructions</h6>
                    <ul class="mb-0">
                        <li><strong>Fasting Required:</strong> Your cat must fast for 12 hours before the procedure (water is okay).</li>
                        <li><strong>Arrival Time:</strong> Please arrive 30 minutes before the scheduled procedure time.</li>
                        <li><strong>Bring:</strong> Any current medications your cat is taking and vaccination records.</li>
                        <li><strong>Recovery:</strong> Plan for 7-10 days of restricted activity and monitoring after the procedure.</li>
                    </ul>
                </div>

                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>What's Next?</h6>
                    <ul class="mb-0">
                        <li>A confirmation email has been sent to your registered email address.</li>
                        <li>You can upload required documents from your booking dashboard.</li>
                        <li>Our team will contact you 24 hours before the procedure to confirm.</li>
                        <li>If you need to reschedule or cancel, please contact us at least 48 hours in advance.</li>
                    </ul>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="{{ route('account.bookings') }}" class="btn btn-warning">
                        <i class="fas fa-list me-2"></i>View My Bookings
                    </a>
                    <a href="{{ route('booking.spay.index') }}" class="btn btn-outline-warning">
                        <i class="fas fa-plus me-2"></i>Book Another Procedure
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

