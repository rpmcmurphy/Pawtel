@extends('layouts.admin')

@section('title', 'Edit Booking - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Edit Booking #{{ $booking['booking_number'] ?? $booking['id'] }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.show', $booking['id']) }}">View</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.bookings.show', $booking['data']['id']) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Booking
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Bookings
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="{{ route('admin.bookings.update', $booking['id']) }}" id="editBookingForm">
                @csrf
                @method('PUT')

                <!-- Booking Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Booking Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Booking Number:</strong></label>
                                    <p class="form-control-plaintext">
                                        #{{ $booking['booking_number'] ?? $booking['id'] }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Customer:</strong></label>
                                    <p class="form-control-plaintext">
                                        {{ $booking['customer']['name'] ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $booking['customer']['email'] ?? '' }}</small>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Type:</strong></label>
                                    <p class="form-control-plaintext">
                                        <span
                                            class="badge bg-{{ ($booking['type'] ?? '') === 'hotel' ? 'primary' : (($booking['type'] ?? '') === 'spa' ? 'success' : 'warning') }}">
                                            {{ ucfirst($booking['type'] ?? 'N/A') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Check-in:</strong></label>
                                    <p class="form-control-plaintext">
                                        {{ isset($booking['check_in_date']) ? \Carbon\Carbon::parse($booking['check_in_date'])->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Check-out:</strong></label>
                                    <p class="form-control-plaintext">
                                        {{ isset($booking['check_out_date']) ? \Carbon\Carbon::parse($booking['check_out_date'])->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Created:</strong></label>
                                    <p class="form-control-plaintext">
                                        {{ isset($booking['created_at']) ? \Carbon\Carbon::parse($booking['created_at'])->format('M d, Y g:i A') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editable Fields -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Edit Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status"
                                        id="status">
                                        <option value="pending"
                                            {{ ($booking['status'] ?? old('status')) == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="confirmed"
                                            {{ ($booking['status'] ?? old('status')) == 'confirmed' ? 'selected' : '' }}>
                                            Confirmed</option>
                                        <option value="completed"
                                            {{ ($booking['status'] ?? old('status')) == 'completed' ? 'selected' : '' }}>
                                            Completed</option>
                                        <option value="cancelled"
                                            {{ ($booking['status'] ?? old('status')) == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="final_amount" class="form-label">Final Amount (৳)</label>
                                    <input type="number" class="form-control @error('final_amount') is-invalid @enderror"
                                        name="final_amount" id="final_amount" step="0.01" min="0"
                                        value="{{ $booking['final_amount'] ?? ($booking['total_amount'] ?? old('final_amount')) }}">
                                    @error('final_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="special_requests" class="form-label">Special Requests / Admin Notes</label>
                            <textarea class="form-control @error('special_requests') is-invalid @enderror" name="special_requests"
                                id="special_requests" rows="4" placeholder="Enter any special requests or admin notes...">{{ $booking['special_requests'] ?? old('special_requests') }}</textarea>
                            @error('special_requests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Booking
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Booking Summary Sidebar -->
        <div class="col-md-4">
            <!-- Original Amount Breakdown -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Amount Breakdown</h6>
                </div>
                <div class="card-body">
                    @if (($booking['type'] ?? '') == 'hotel' && isset($booking['room_type']))
                        <div class="d-flex justify-content-between mb-2">
                            <span>Room: {{ $booking['room_type']['name'] ?? 'N/A' }}</span>
                            <span>৳{{ number_format(($booking['total_amount'] ?? 0) - ($booking['addons']->sum('total_price') ?? 0), 2) }}</span>
                        </div>
                    @elseif(($booking['type'] ?? '') == 'spa' && isset($booking['spa_booking']))
                        <div class="d-flex justify-content-between mb-2">
                            <span>Spa Package: {{ $booking['spa_booking']['spa_package']['name'] ?? 'N/A' }}</span>
                            <span>৳{{ number_format($booking['spa_booking']['spa_package']['price'] ?? 0, 2) }}</span>
                        </div>
                    @elseif(($booking['type'] ?? '') == 'spay' && isset($booking['spay_booking']))
                        <div class="d-flex justify-content-between mb-2">
                            <span>Spay Package: {{ $booking['spay_booking']['spay_package']['name'] ?? 'N/A' }}</span>
                            <span>৳{{ number_format($booking['spay_booking']['spay_package']['price'] ?? 0, 2) }}</span>
                        </div>
                    @endif

                    @if (isset($booking['addons']) && is_array($booking['addons']) && count($booking['addons']) > 0)
                        @foreach ($booking['addons'] as $addon)
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ $addon['service']['name'] ?? 'Addon' }}
                                    (x{{ $addon['quantity'] ?? 1 }})</span>
                                <span>৳{{ number_format($addon['total_price'] ?? 0, 2) }}</span>
                            </div>
                        @endforeach
                    @endif

                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Original Total:</strong>
                        <strong>৳{{ number_format($booking['total_amount'] ?? 0, 2) }}</strong>
                    </div>
                    
                    @if (isset($booking['discount_amount']) && $booking['discount_amount'] > 0)
                        <div class="d-flex justify-content-between text-danger">
                            <span>Discount:</span>
                            <span>-৳{{ number_format($booking['discount_amount'], 2) }}</span>
                        </div>
                    @endif

                    @if (isset($booking['final_amount']) && $booking['final_amount'] != ($booking['total_amount'] ?? 0))
                        <div class="d-flex justify-content-between text-success">
                            <strong>Final Amount:</strong>
                            <strong>৳{{ number_format($booking['final_amount'], 2) }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Booking Timeline -->
            @if (isset($booking['data']['status_history']) && count($booking['data']['status_history']) > 0)
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Status History</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach ($booking['data']['status_history'] as $history)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">{{ ucfirst($history['status']) }}</h6>
                                        <p class="timeline-date">
                                            {{ \Carbon\Carbon::parse($history['created_at'])->format('M d, Y g:i A') }}</p>
                                        @if (isset($history['notes']))
                                            <p class="timeline-text">{{ $history['notes'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -35px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: -31px;
            top: 15px;
            width: 2px;
            height: calc(100% + 5px);
            background-color: #e9ecef;
        }

        .timeline-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .timeline-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .timeline-text {
            font-size: 13px;
            margin-bottom: 0;
        }
    </style>
@endpush

@push('scripts')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            const form = document.getElementById('editBookingForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const status = document.getElementById('status').value;

                    if (!status) {
                        e.preventDefault();
                        alert('Please select a status.');
                        return false;
                    }

                    // Confirm if changing to cancelled
                    if (status === 'cancelled') {
                        if (!confirm('Are you sure you want to cancel this booking?')) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            }

            // Auto-calculate final amount suggestions
            const finalAmountInput = document.getElementById('final_amount');
            if (finalAmountInput) {
                const originalAmount = {{ $booking['total_amount'] ?? 0 }};

                finalAmountInput.addEventListener('focus', function() {
                    if (!this.value && originalAmount > 0) {
                        this.value = originalAmount;
                    }
                });
            }
        });
    </script>
@endpush
