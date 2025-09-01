@extends('layouts.admin')

@section('title', 'Sales Reports')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-shopping-cart"></i> Sales Reports
            </h1>
            <div>
                <button type="button" class="btn btn-success" onclick="exportReport('sales')">
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
                        <label class="form-label">Product Category</label>
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <option value="food" {{ ($filters['category'] ?? '') == 'food' ? 'selected' : '' }}>Food
                            </option>
                            <option value="treats" {{ ($filters['category'] ?? '') == 'treats' ? 'selected' : '' }}>Treats
                            </option>
                            <option value="toys" {{ ($filters['category'] ?? '') == 'toys' ? 'selected' : '' }}>Toys
                            </option>
                            <option value="accessories"
                                {{ ($filters['category'] ?? '') == 'accessories' ? 'selected' : '' }}>Accessories</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-success d-block w-100" onclick="loadReports()">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4" id="statsCards">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Orders
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalOrders">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Completed
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="completedOrders">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingOrders">Loading...</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
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
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="salesRevenue">Loading...</div>
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
                        <h6 class="m-0 font-weight-bold text-primary">Orders by Status</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="ordersByStatusChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Revenue Trend</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueTrendChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="topProductsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Units Sold</th>
                                        <th>Total Revenue</th>
                                        <th>Avg. Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="text-center">Loading...</td>
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
        let statusChart, trendChart;

        function loadReports() {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);

            fetch(`{{ route('admin.reports.sales') }}?${params}&format=json`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateStats(data.data.stats);
                        updateCharts(data.data);
                        updateTopProducts(data.data.top_products || []);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load reports');
                });
        }

        function updateStats(stats) {
            document.getElementById('totalOrders').textContent = stats.total_orders || 0;
            document.getElementById('completedOrders').textContent = stats.completed_orders || 0;
            document.getElementById('pendingOrders').textContent = stats.pending_orders || 0;
            document.getElementById('salesRevenue').textContent = ' + (stats.revenue || 0).toFixed(2);
        }

        function updateCharts(data) {
            // Orders by Status Chart
            if (statusChart) statusChart.destroy();
            const statusCtx = document.getElementById('ordersByStatusChart').getContext('2d');
            statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: data.by_status.map(item => item.status),
                    datasets: [{
                        data: data.by_status.map(item => item.count),
                        backgroundColor: ['#f6c23e', '#1cc88a', '#36b9cc', '#e74a3b']
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
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
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
                                    return ' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateTopProducts(products) {
            const tbody = document.querySelector('#topProductsTable tbody');

            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No products found</td></tr>';
                return;
            }

            tbody.innerHTML = products.map(product => `
        <tr>
            <td>${product.name}</td>
            <td>${product.total_sold || 0}</td>
            <td>${(product.total_revenue || 0).toFixed(2)}</td>
            <td>${(product.price || 0).toFixed(2)}</td>
        </tr>
    `).join('');
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
