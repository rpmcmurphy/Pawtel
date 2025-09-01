@extends('layouts.admin')

@section('title', 'Booking Reports')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calendar-alt"></i> Booking Reports
            </h1>
            <div>
                <button type="button" class="btn btn-primary" onclick="exportReport('bookings')">
                    <i class="fas fa-download"></i> Export Data
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
            </div>
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" class="form-control" name="date_from"
                            value="{{ $filters['date_from'] ?? date('Y-m-01') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" class="form-control" name="date_to"
                            value="{{ $filters['date_to'] ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type">
                            <option value="">All Types</option>
                            <option value="hotel" {{ ($filters['type'] ?? '') == 'hotel' ? 'selected' : '' }}>Hotel
                            </option>
                            <option value="spa" {{ ($filters['type'] ?? '') == 'spa' ? 'selected' : '' }}>Spa</option>
                            <option value="spay" {{ ($filters['type'] ?? '') == 'spay' ? 'selected' : '' }}>Spay/Neuter
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="confirmed" {{ ($filters['status'] ?? '') == 'confirmed' ? 'selected' : '' }}>
                                Confirmed</option>
                            <option value="cancelled" {{ ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>
                                Cancelled</option>
                            <option value="completed" {{ ($filters['status'] ?? '') == 'completed' ? 'selected' : '' }}>
                                Completed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-primary d-block w-100" onclick="loadReports()">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4" id="statsCards">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Bookings
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBookings">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Confirmed
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="confirmedBookings">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingBookings">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Revenue
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="bookingRevenue">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Bookings by Type</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="bookingsByTypeChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Bookings by Status</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="bookingsByStatusChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Trend -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Revenue Trend</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueTrendChart" width="400" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="module">
        let typeChart, statusChart, trendChart;

        function loadReports() {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);

            fetch(`{{ route('admin.reports.bookings') }}?${params}&format=json`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateStats(data.data.stats);
                        updateCharts(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load reports');
                });
        }

        function updateStats(stats) {
            document.getElementById('totalBookings').textContent = stats.total_bookings || 0;
            document.getElementById('confirmedBookings').textContent = stats.confirmed_bookings || 0;
            document.getElementById('pendingBookings').textContent = stats.pending_bookings || 0;
            document.getElementById('bookingRevenue').textContent = '$' + (stats.revenue || 0).toFixed(2);
        }

        function updateCharts(data) {
            // Bookings by Type Chart
            if (typeChart) typeChart.destroy();
            const typeCtx = document.getElementById('bookingsByTypeChart').getContext('2d');
            typeChart = new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: data.by_type.map(item => item.type),
                    datasets: [{
                        data: data.by_type.map(item => item.count),
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Bookings by Status Chart
            if (statusChart) statusChart.destroy();
            const statusCtx = document.getElementById('bookingsByStatusChart').getContext('2d');
            statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: data.by_status.map(item => item.status),
                    datasets: [{
                        data: data.by_status.map(item => item.count),
                        backgroundColor: ['#f6c23e', '#1cc88a', '#e74a3b', '#36b9cc']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Revenue Trend Chart
            if (trendChart) trendChart.destroy();
            const trendCtx = document.getElementById('revenueTrendChart').getContext('2d');
            trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: data.revenue_trend.map(item => item.date),
                    datasets: [{
                        label: 'Revenue',
                        data: data.revenue_trend.map(item => item.revenue),
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }

        function exportReport(type) {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);
            params.append('format', 'csv');

            window.location.href = `{{ route('admin.reports.export') }}?${params}&type=${type}`;
        }

        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            loadReports();
        });
    </script>
@endsection
