@extends('layouts.admin')

@section('title', 'User Bookings - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">User Bookings</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.show', $user_id) }}" class="btn btn-outline-primary">
                <i class="fas fa-user"></i> User Profile
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.bookings', $user_id) }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Booking Type</label>
                    <select class="form-select" name="type">
                        <option value="">All Types</option>
                        <option value="hotel" {{ ($filters['type'] ?? '') === 'hotel' ? 'selected' : '' }}>Hotel</option>
                        <option value="spa" {{ ($filters['type'] ?? '') === 'spa' ? 'selected' : '' }}>Spa</option>
                        <option value="spay" {{ ($filters['type'] ?? '') === 'spay' ? 'selected' : '' }}>Spay/Neuter
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="confirmed" {{ ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' }}>
                            Confirmed</option>
                        <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>
                            Completed</option>
                        <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>
                            Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.users.bookings', $user_id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-calendar-alt"></i> Bookings List
                @if (isset($bookings['pagination']['total']))
                    <small class="text-muted">({{ $bookings['pagination']['total'] }} total)</small>
                @elseif (!empty($bookings) && is_countable($bookings))
                    <small class="text-muted">({{ count($bookings) }} total)</small>
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if (empty($bookings) ||
                    (is_array($bookings) && count($bookings) === 0) ||
                    (isset($bookings['data']) && empty($bookings['data'])))
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No bookings found</h5>
                    <p class="text-muted">This user hasn't made any bookings yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Booking #</th>
                                <th>Type</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookings['data'] ?? $bookings as $booking)
                                <tr>
                                    <td>
                                        <strong>#{{ $booking['booking_number'] ?? $booking['id'] }}</strong>
                                        @if (!empty($booking['is_manual_entry']))
                                            <small class="badge bg-info ms-1">Manual</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($booking['type'] ?? 'N/A') }}</span>
                                        @if (!empty($booking['room_type']['name']))
                                            <br><small class="text-muted">{{ $booking['room_type']['name'] }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($booking['check_in_date']) && !empty($booking['check_out_date']))
                                            <strong>{{ date('M j, Y', strtotime($booking['check_in_date'])) }}</strong>
                                            <br>
                                            <small class="text-muted">to
                                                {{ date('M j, Y', strtotime($booking['check_out_date'])) }}</small>
                                            @if (!empty($booking['total_days']))
                                                <br><small class="text-info">{{ $booking['total_days'] }}
                                                    day{{ $booking['total_days'] > 1 ? 's' : '' }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ number_format($booking['final_amount'] ?? ($booking['total_amount'] ?? 0), 2) }}</strong>
                                        @if (!empty($booking['discount_amount']) && $booking['discount_amount'] > 0)
                                            <br><small
                                                class="text-success">-${{ number_format($booking['discount_amount'], 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = $booking['status'] ?? 'unknown';
                                            $statusClass = match ($status) {
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                        @if ($status === 'confirmed' && !empty($booking['confirmed_at']))
                                            <br><small
                                                class="text-muted">{{ date('M j', strtotime($booking['confirmed_at'])) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ isset($booking['created_at']) ? date('M j, Y', strtotime($booking['created_at'])) : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.bookings.show', $booking['id'] ?? 0) }}">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                </li>
                                                @if (($booking['status'] ?? '') === 'pending')
                                                    <li>
                                                        <button class="dropdown-item"
                                                            onclick="updateBookingStatus({{ $booking['id'] ?? 0 }}, 'confirmed')">
                                                            <i class="fas fa-check"></i> Confirm
                                                        </button>
                                                    </li>
                                                @endif
                                                @if (in_array($booking['status'] ?? '', ['pending', 'confirmed']))
                                                    <li>
                                                        <button class="dropdown-item text-danger"
                                                            onclick="cancelBooking({{ $booking['id'] ?? 0 }})">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No bookings found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if (isset($bookings['pagination']) && $bookings['pagination']['total'] > $bookings['pagination']['per_page'])
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Bookings pagination">
                            <ul class="pagination">
                                @if ($bookings['pagination']['current_page'] > 1)
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ request()->fullUrlWithQuery(['page' => $bookings['pagination']['current_page'] - 1]) }}">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                @for ($i = 1; $i <= $bookings['pagination']['last_page']; $i++)
                                    <li
                                        class="page-item {{ $i == $bookings['pagination']['current_page'] ? 'active' : '' }}">
                                        <a class="page-link"
                                            href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                @if ($bookings['pagination']['current_page'] < $bookings['pagination']['last_page'])
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ request()->fullUrlWithQuery(['page' => $bookings['pagination']['current_page'] + 1]) }}">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        function updateBookingStatus(bookingId, status) {
            const statusText = status.charAt(0).toUpperCase() + status.slice(1);

            Swal.fire({
                title: `${statusText} Booking?`,
                text: `Are you sure you want to ${status} this booking?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Yes, ${statusText}`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to booking management (assuming there's a route for status update)
                    window.location.href = `/admin/bookings/${bookingId}?action=${status}`;
                }
            });
        }

        function cancelBooking(bookingId) {
            Swal.fire({
                title: 'Cancel Booking?',
                text: 'Are you sure you want to cancel this booking?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Cancel',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'No, Keep'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to booking management for cancellation
                    window.location.href = `/admin/bookings/${bookingId}?action=cancel`;
                }
            });
        }
    </script>
@endpush
