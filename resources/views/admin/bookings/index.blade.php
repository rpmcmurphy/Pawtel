@extends('layouts.admin')

@section('title', 'Manage Bookings - Admin')
@section('page-title', 'Bookings Management')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Bookings Management</h2>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="exportBookings()">
                <i class="fas fa-download me-2"></i>
                Export
            </button>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filter Panel -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Booking Type</label>
                    <select class="form-select" name="type" id="type">
                        <option value="">All Types</option>
                        <option value="hotel" {{ request('type') == 'hotel' ? 'selected' : '' }}>Hotel</option>
                        <option value="spa" {{ request('type') == 'spa' ? 'selected' : '' }}>Spa</option>
                        <option value="spay" {{ request('type') == 'spay' ? 'selected' : '' }}>Spay/Neuter</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-body">
            @if (!empty($bookings['data']))
                <div class="table-responsive">
                    <table class="table table-hover" id="bookingsTable">
                        <thead>
                            <tr>
                                <th>Booking #</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings['data'] as $booking)
                                <tr>
                                    <td>
                                        <strong>#{{ $booking['booking_number'] ?? $booking['id'] }}</strong>
                                    </td>
                                    <td>
                                        {{ $booking['user']['name'] ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $booking['user']['email'] ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($booking['type']) }}</span>
                                    </td>
                                    <td>
                                        {{ date('M d, Y', strtotime($booking['check_in_date'])) }}
                                        @if ($booking['check_out_date'] != $booking['check_in_date'])
                                            <br><small class="text-muted">to
                                                {{ date('M d, Y', strtotime($booking['check_out_date'])) }}</small>
                                        @endif
                                    </td>
                                    <td>৳{{ number_format($booking['final_amount'], 2) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $booking['status'] == 'confirmed'
                                                ? 'success'
                                                : ($booking['status'] == 'pending'
                                                    ? 'warning'
                                                    : ($booking['status'] == 'completed'
                                                        ? 'primary'
                                                        : 'secondary')) }}">
                                            {{ ucfirst($booking['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ date('M d, Y', strtotime($booking['created_at'])) }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.bookings.show', $booking['id']) }}"
                                                class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if ($booking['status'] == 'pending')
                                                <button class="btn btn-outline-success"
                                                    onclick="confirmBooking({{ $booking['id'] }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif

                                            @if (in_array($booking['status'], ['pending', 'confirmed']))
                                                <button class="btn btn-outline-danger"
                                                    onclick="cancelBooking({{ $booking['id'] }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5>No Bookings Found</h5>
                    <p class="text-muted">No bookings match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        $(document).ready(function() {
            $('#bookingsTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                responsive: true
            });
        });

        function confirmBooking(bookingId) {
            if (confirm('Are you sure you want to confirm this booking?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/bookings/${bookingId}/confirm`;

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function cancelBooking(bookingId) {
            const reason = prompt('Please enter cancellation reason:');
            if (reason && reason.trim() !== '') {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/bookings/${bookingId}/cancel`;

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Add reason
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = reason;
                form.appendChild(reasonInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function exportBookings() {
            const params = new URLSearchParams(window.location.search);
            window.open(`/admin/reports/export?type=bookings&${params.toString()}`);
        }
    </script>
@endpush
