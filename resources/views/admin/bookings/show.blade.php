@extends('layouts.admin')

@section('title', 'View Booking - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Booking #{{ $booking['booking_number'] ?? $booking['id'] }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.bookings.edit', $booking['id']) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Booking Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Booking Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Booking Number:</strong> #{{ $booking['booking_number'] ?? $booking['id'] }}</p>
                            <p><strong>Type:</strong>
                                <span
                                    class="badge bg-{{ $booking['type'] === 'hotel' ? 'primary' : ($booking['type'] === 'spa' ? 'success' : 'warning') }}">
                                    {{ ucfirst($booking['type']) }}
                                </span>
                            </p>
                            <p><strong>Status:</strong>
                                <span
                                    class="badge bg-{{ $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : ($booking['status'] === 'completed' ? 'info' : 'danger')) }}">
                                    {{ ucfirst($booking['status']) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Check-in:</strong>
                                {{ \Carbon\Carbon::parse($booking['check_in_date'])->format('M d, Y') }}</p>
                            <p><strong>Check-out:</strong>
                                {{ \Carbon\Carbon::parse($booking['check_out_date'])->format('M d, Y') }}</p>
                            <p><strong>Total Days:</strong> {{ $booking['total_days'] ?? 0 }}</p>
                        </div>
                    </div>

                    @if (!empty($booking['special_requests']))
                        <div class="mt-3">
                            <strong>Special Requests:</strong>
                            <p class="text-muted">{{ $booking['special_requests'] }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    @if (!empty($booking['user']))
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $booking['user']['name'] }}</p>
                                <p><strong>Email:</strong> {{ $booking['user']['email'] }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> {{ $booking['user']['phone'] ?? 'N/A' }}</p>
                                <p><strong>Member Since:</strong>
                                    {{ \Carbon\Carbon::parse($booking['user']['created_at'])->format('M Y') }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Customer information not available</p>
                    @endif
                </div>
            </div>

            <!-- Service Details -->
            @if ($booking['type'] === 'hotel' && !empty($booking['room_type']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Room Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Room Type:</strong> {{ $booking['room_type']['name'] ?? 'N/A' }}</p>
                        <p><strong>Capacity:</strong> {{ $booking['room_type']['capacity'] ?? 'N/A' }}</p>
                        <p><strong>Price per night:</strong> ৳{{ number_format($booking['room_type']['price'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            @endif

            @if ($booking['type'] === 'spa' && !empty($booking['spa_booking']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Spa Service Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Package:</strong> {{ $booking['spa_booking']['spa_package']['name'] ?? 'N/A' }}</p>
                        <p><strong>Duration:</strong> {{ $booking['spa_booking']['spa_package']['duration'] ?? 'N/A' }}
                            minutes</p>
                        <p><strong>Price:</strong>
                            ৳{{ number_format($booking['spa_booking']['spa_package']['price'] ?? 0, 2) }}</p>
                    </div>
                </div>
            @endif

            @if ($booking['type'] === 'spay' && !empty($booking['spay_booking']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Spay/Neuter Service Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Package:</strong> {{ $booking['spay_booking']['spay_package']['name'] ?? 'N/A' }}</p>
                        <p><strong>Price:</strong>
                            ৳{{ number_format($booking['spay_booking']['spay_package']['price'] ?? 0, 2) }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Pricing -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pricing</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>Total Amount:</span>
                        <span>৳{{ number_format($booking['total_amount'] ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Discount:</span>
                        <span>৳{{ number_format($booking['discount_amount'] ?? 0, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Final Amount:</strong>
                        <strong class="text-success">৳{{ number_format($booking['final_amount'] ?? 0, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if ($booking['status'] === 'pending')
                            <button data-confirm-booking data-id="{{ $booking['id'] }}" class="btn btn-success">
                                <i class="fas fa-check"></i> Confirm Booking
                            </button>
                        @endif

                        @if (in_array($booking['status'], ['pending', 'confirmed']))
                            <button data-cancel-booking data-id="{{ $booking['id'] }}" class="btn btn-danger">
                                <i class="fas fa-times"></i> Cancel Booking
                            </button>
                        @endif

                        <a href="{{ route('admin.bookings.edit', $booking['id']) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Booking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        function confirmBooking(bookingId) {
            if (confirm('Are you sure you want to confirm this booking?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('admin.bookings.confirm', ['id' => '___ID___']) }}".replace('___ID___', bookingId);

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        document.querySelectorAll('[data-confirm-booking]').forEach(btn => {
            btn.addEventListener('click', () => confirmBooking(btn.dataset.id));
        });

        function cancelBooking(bookingId) {
            const reason = prompt('Please enter cancellation reason:');
            if (reason && reason.trim() !== '') {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('admin.bookings.cancel', ['id' => '___ID___']) }}".replace('___ID___', bookingId);

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = reason;
                form.appendChild(reasonInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        document.querySelectorAll('[data-cancel-booking]').forEach(btn => {
            btn.addEventListener('click', () => cancelBooking(btn.dataset.id));
        });
    </script>
@endpush
