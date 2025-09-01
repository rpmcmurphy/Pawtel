@extends('layouts.admin')

@section('title', 'Admin Dashboard - Pawtel')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Today's Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayBookings">
                                {{ $stats['today_bookings'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Monthly Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthlyRevenue">
                                ৳{{ number_format($stats['monthly_revenue'] ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeUsers">
                                {{ $stats['active_users'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingBookings">
                                {{ $stats['pending_bookings'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Booking Types</h6>
                </div>
                <div class="card-body">
                    <canvas id="bookingTypesChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                </div>
                <div class="card-body">
                    @if (!empty($recentActivity['recent_bookings']))
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentActivity['recent_bookings'] as $booking)
                                        <tr>
                                            <td>{{ $booking['user']['name'] ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($booking['type']) }}</span>
                                            </td>
                                            <td>{{ date('M d, Y', strtotime($booking['created_at'])) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $booking['status'] == 'confirmed' ? 'success' : ($booking['status'] == 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($booking['status']) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent bookings</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                </div>
                <div class="card-body">
                    @if (!empty($recentActivity['recent_orders']))
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentActivity['recent_orders'] as $order)
                                        <tr>
                                            <td>#{{ $order['order_number'] ?? $order['id'] }}</td>
                                            <td>{{ $order['user']['name'] ?? 'N/A' }}</td>
                                            <td>৳{{ number_format($order['total_amount'], 2) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $order['status'] == 'completed' ? 'success' : ($order['status'] == 'processing' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($order['status']) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent orders</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ],
                    datasets: [{
                        label: 'Revenue',
                        data: @json($stats['monthly_revenue_data'] ?? array_fill(0, 12, 0)),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '৳' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Booking Types Pie Chart
            const bookingTypesCtx = document.getElementById('bookingTypesChart').getContext('2d');
            new Chart(bookingTypesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Hotel', 'Spa', 'Spay/Neuter'],
                    datasets: [{
                        data: @json($stats['booking_types_data'] ?? [0, 0, 0]),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 205, 86, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Auto-refresh stats every 30 seconds
            setInterval(refreshStats, 30000);
        });

        function refreshStats() {
            fetch('/admin/stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('todayBookings').textContent = data.data.today_bookings || 0;
                        document.getElementById('monthlyRevenue').textContent = '৳' + (data.data.monthly_revenue || 0)
                            .toFixed(2);
                        document.getElementById('activeUsers').textContent = data.data.active_users || 0;
                        document.getElementById('pendingBookings').textContent = data.data.pending_bookings || 0;
                    }
                })
                .catch(error => console.error('Failed to refresh stats:', error));
        }
    </script>
@endpush
