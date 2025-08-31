@extends('layouts.app')

@section('title', 'My Account - Pawtel')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Welcome, {{ $user['name'] }}!</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-calendar-alt fa-2x text-primary mb-3"></i>
                        <h5>My Bookings</h5>
                        <p class="text-muted">View and manage your bookings</p>
                        <a href="{{ route('account.bookings') }}" class="btn btn-primary">View Bookings</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-shopping-bag fa-2x text-success mb-3"></i>
                        <h5>My Orders</h5>
                        <p class="text-muted">Track your shop orders</p>
                        <a href="{{ route('account.orders') }}" class="btn btn-success">View Orders</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-user fa-2x text-info mb-3"></i>
                        <h5>My Profile</h5>
                        <p class="text-muted">Update your information</p>
                        <a href="{{ route('auth.profile') }}" class="btn btn-info">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Bookings</h5>
                    </div>
                    <div class="card-body">
                        @if (!empty($recentBookings['data']))
                            <div class="list-group list-group-flush">
                                @foreach (array_slice($recentBookings['data'], 0, 5) as $booking)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ ucfirst($booking['type']) }} Booking</strong><br>
                                            <small
                                                class="text-muted">{{ date('M d, Y', strtotime($booking['check_in_date'])) }}</small>
                                        </div>
                                        <span
                                            class="badge bg-{{ $booking['status'] == 'confirmed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($booking['status']) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('account.bookings') }}" class="btn btn-outline-primary btn-sm">View
                                    All</a>
                            </div>
                        @else
                            <p class="text-muted">No recent bookings</p>
                            <a href="{{ route('booking.hotel.index') }}" class="btn btn-primary">Make a Booking</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        @if (!empty($recentOrders['data']))
                            <div class="list-group list-group-flush">
                                @foreach (array_slice($recentOrders['data'], 0, 5) as $order)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Order #{{ $order['id'] }}</strong><br>
                                            <small
                                                class="text-muted">৳{{ number_format($order['total_amount'], 2) }}</small>
                                        </div>
                                        <span
                                            class="badge bg-{{ $order['status'] == 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($order['status']) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('account.orders') }}" class="btn btn-outline-primary btn-sm">View All</a>
                            </div>
                        @else
                            <p class="text-muted">No recent orders</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary">Shop Now</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
