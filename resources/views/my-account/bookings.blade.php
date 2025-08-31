@extends('layouts.app')

@section('title', 'My Bookings - Pawtel')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>My Bookings</h2>
                    <a href="{{ route('account.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ !$currentStatus ? 'active' : '' }}" 
                           href="{{ route('account.bookings') }}">
                            All Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $currentStatus === 'pending' ? 'active' : '' }}" 
                           href="{{ route('account.bookings', ['status' => 'pending']) }}">
                            Pending
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $currentStatus === 'confirmed' ? 'active' : '' }}" 
                           href="{{ route('account.bookings', ['status' => 'confirmed']) }}">
                            Confirmed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $currentStatus === 'completed' ? 'active' : '' }}" 
                           href="{{ route('account.bookings', ['status' => 'completed']) }}">
                            Completed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $currentStatus === 'cancelled' ? 'active' : '' }}" 
                           href="{{ route('account.bookings', ['status' => 'cancelled']) }}">
                            Cancelled
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="row">
            <div class="col-12">
                @if(isset($bookings['data']) && count($bookings['data']) > 0)
                    @foreach($bookings['data'] as $booking)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <span class="badge badge-{{ 
                                            $booking['status'] === 'confirmed' ? 'success' : 
                                            ($booking['status'] === 'pending' ? 'warning' : 
                                            ($booking['status'] === 'cancelled' ? 'danger' : 'info'))
                                        }}">
                                            {{ ucfirst($booking['status']) }}
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="mb-1">{{ $booking['booking_type'] ?? 'Hotel' }} Booking</h6>
                                        <small class="text-muted">ID: #{{ $booking['booking_number'] ?? $booking['id'] }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-0">
                                            <strong>Check-in:</strong><br>
                                            {{ $booking['check_in_date'] ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-0">
                                            <strong>Total:</strong><br>
                                            ${{ number_format($booking['total_amount'] ?? 0, 2) }}
                                        </p>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <a href="{{ route('account.booking.show', $booking['id']) }}" 
                                           class="btn btn-sm btn-primary">
                                            View Details
                                        </a>
                                        @if($booking['status'] === 'pending' || $booking['status'] === 'confirmed')
                                            <form method="POST" 
                                                  action="{{ route('account.booking.cancel', $booking['id']) }}" 
                                                  class="d-inline-block mt-1">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if(isset($bookings['meta']) && $bookings['meta']['last_page'] > 1)
                        <div class="d-flex justify-content-center">
                            <nav aria-label="Bookings pagination">
                                <ul class="pagination">
                                    @if($bookings['meta']['current_page'] > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $bookings['meta']['current_page'] - 1]) }}">Previous</a>
                                        </li>
                                    @endif
                                    
                                    @for($i = 1; $i <= $bookings['meta']['last_page']; $i++)
                                        <li class="page-item {{ $i === $bookings['meta']['current_page'] ? 'active' : '' }}">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                        </li>
                                    @endfor
                                    
                                    @if($bookings['meta']['current_page'] < $bookings['meta']['last_page'])
                                        <li class="page-item">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $bookings['meta']['current_page'] + 1]) }}">Next</a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @endif
                @else
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No bookings found</h5>
                            <p class="text-muted">You haven't made any bookings yet.</p>
                            <a href="{{ route('booking.hotel.index') }}" class="btn btn-primary">
                                Make Your First Booking
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection