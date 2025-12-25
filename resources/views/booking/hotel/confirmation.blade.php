@extends('layouts.app')

@section('title', 'Booking Confirmation - Pawtel')

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
                    <h2 class="mb-2">Booking Confirmed!</h2>
                    <p class="text-muted">Your hotel booking has been successfully created.</p>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-hotel me-2"></i>Booking Details</h5>
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
                            <div class="col-md-6 mb-3">
                                <strong>Check-in Date:</strong><br>
                                {{ \Carbon\Carbon::parse($booking['check_in_date'])->format('M d, Y') }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Check-out Date:</strong><br>
                                {{ \Carbon\Carbon::parse($booking['check_out_date'])->format('M d, Y') }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Duration:</strong><br>
                                {{ $booking['total_days'] ?? 0 }} {{ ($booking['total_days'] ?? 0) == 1 ? 'day' : 'days' }}
                            </div>
                            @if(isset($booking['room_type']))
                                <div class="col-md-6 mb-3">
                                    <strong>Room Type:</strong><br>
                                    {{ $booking['room_type']['name'] }}
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

                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>What's Next?</h6>
                    <ul class="mb-0">
                        <li>A confirmation email has been sent to your registered email address.</li>
                        <li>You can upload required documents from your booking dashboard.</li>
                        <li>Our team will review your booking and confirm the room assignment.</li>
                        <li>You will receive another confirmation email once your booking is approved.</li>
                    </ul>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="{{ route('account.bookings') }}" class="btn btn-primary">
                        <i class="fas fa-list me-2"></i>View My Bookings
                    </a>
                    <a href="{{ route('booking.hotel.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Book Another Stay
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

