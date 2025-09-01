@extends('layouts.admin')

@section('title', 'Financial Reports')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-pie"></i> Financial Reports
            </h1>
            <div>
                <button type="button" class="btn btn-info" onclick="exportReport('financial')">
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
                    <div class="col-md-3">
                        <label class="form-label">Report Type</label>
                        <select class="form-select" name="type">
                            <option value="">All Revenue</option>
                            <option value="bookings" {{ ($filters['type'] ?? '') == 'bookings' ? 'selected' : '' }}>Bookings
                                Only</option>
                            <option value="sales" {{ ($filters['type'] ?? '') == 'sales' ? 'selected' : '' }}>Sales Only
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-info d-block w-100" onclick="loadReports()">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4" id="statsCards">
            <div class="col-xl-2 col-md-6 mb-4">
                <div class="card border-left-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Revenue
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRevenue">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-6 mb-4">
                <div class="card border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Booking Revenue
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="bookingRevenue">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-bed fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-6 mb-4">
                <div class="card border-left-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Sales Revenue
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="salesRevenue">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                                    Avg Booking Value
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgBookingValue">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calculator fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-dark h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Avg Order Value
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgOrderValue">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                        <h6 class="m-0 font-weight-bold text-primary">Revenue by Source</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueBySourceChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue (Last 12 Months)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyRevenueChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue Breakdown</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="monthlyRevenueTable">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Booking Revenue</th>
                                        <th>Sales Revenue</th>
                                        <th>Total Revenue</th>
                                        <th>Growth %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="module">
        let sourceChart, monthlyChart;

        function loadReports() {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);

            fetch(`{{ route('admin.reports.financial') }}?${params}&format=json`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateStats(data.data.stats);
                        updateCharts(data.data);
                        updateMonthlyTable(data.data.monthly_revenue || []);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load reports');
                });
        }

        function updateStats(stats) {
            document.getElementById('totalRevenue').textContent = '$' + (stats.total_revenue || 0).toFixed(2);
            document.getElementById('bookingRevenue').textContent = '$' + (stats.booking_revenue || 0).toFixed(2);
            document.getElementById('salesRevenue').textContent = '$' + (stats.sales_revenue || 0).toFixed(2);
            document.getElementById('avgBookingValue').textContent = '$' + (stats.avg_booking_value || 0).toFixed(2);
            document.getElementById('avgOrderValue').textContent = '$' + (stats.avg_order_value || 0).toFixed(2);
        }

        function updateCharts(data) {
            // Revenue by Source Chart
            if (sourceChart) sourceChart.destroy();
            const sourceCtx = document.getElementById('revenueBySourceChart').getContext('2d');
            sourceChart = new Chart(sourceCtx, {
                type: 'doughnut',
                data: {
                    labels: data.revenue_by_source.map(item => item.source),
                    datasets: [{
                        data: data.revenue_by_source.map(item => item.amount),
                        backgroundColor: ['#4e73df', '#1cc88a']
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

            // Monthly Revenue Chart
            if (monthlyChart) monthlyChart.destroy();
            const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
            monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: data.monthly_revenue.map(item => item.month),
                    datasets: [{
                        label: 'Booking Revenue',
                        data: data.monthly_revenue.map(item => item.bookings),
                        backgroundColor: '#4e73df'
                    }, {
                        label: 'Sales Revenue',
                        data: data.monthly_revenue.map(item => item.orders),
                        backgroundColor: '#1cc88a'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
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

        function updateMonthlyTable(monthlyData) {
            const tbody = document.querySelector('#monthlyRevenueTable tbody');

            if (monthlyData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No data available</td></tr>';
                return;
            }

            tbody.innerHTML = monthlyData.map((item, index) => {
                const prevTotal = index > 0 ? monthlyData[index - 1].total : 0;
                const growth = prevTotal > 0 ? ((item.total - prevTotal) / prevTotal * 100).toFixed(1) : 0;

                return `
        <tr>
            <td>${item.month}</td>
            <td>$${(item.bookings || 0).toFixed(2)}</td>
            <td>$${(item.orders || 0).toFixed(2)}</td>
            <td>$${(item.total || 0).toFixed(2)}</td>
            <td>
                <span class="badge badge-${growth >= 0 ? 'success' : 'danger'}">
                    ${growth >= 0 ? '+' : ''}${growth}%
                </span>
            </td>
        </tr>
        `;
            }).join('');
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
