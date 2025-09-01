@extends('layouts.admin')

@section('title', 'Reports - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Reports & Analytics</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
@endsection

@section('content')
    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Bookings (This Month)
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
                                Total Revenue (This Month)
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

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeUsers">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Pending Bookings
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
    </div>

    <!-- Report Categories -->
    <div class="row">
        <!-- Booking Reports -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt"></i> Booking Reports
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Analyze booking trends, occupancy rates, and booking patterns.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Booking trends over time</li>
                        <li><i class="fas fa-check text-success"></i> Occupancy rates by room type</li>
                        <li><i class="fas fa-check text-success"></i> Booking sources analysis</li>
                        <li><i class="fas fa-check text-success"></i> Cancellation rates</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.reports.bookings') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-chart-line"></i> View Booking Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Sales Reports -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart"></i> Sales Reports
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Track product sales, revenue trends, and customer behavior.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Product performance</li>
                        <li><i class="fas fa-check text-success"></i> Revenue by category</li>
                        <li><i class="fas fa-check text-success"></i> Top selling products</li>
                        <li><i class="fas fa-check text-success"></i> Customer purchase patterns</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-chart-bar"></i> View Sales Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Financial Reports -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Financial Reports
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Comprehensive financial analysis and profit tracking.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Revenue breakdowns</li>
                        <li><i class="fas fa-check text-success"></i> Profit margins</li>
                        <li><i class="fas fa-check text-success"></i> Payment methods analysis</li>
                        <li><i class="fas fa-check text-success"></i> Tax reports</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.reports.financial') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-area"></i> View Financial Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Report Generation -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-download"></i> Quick Report Export
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.export') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select" name="type" required>
                                <option value="">Select Report Type</option>
                                <option value="bookings">Bookings Report</option>
                                <option value="sales">Sales Report</option>
                                <option value="financial">Financial Report</option>
                                <option value="users">Users Report</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-control" name="date_from" 
                                   value="{{ date('Y-m-01') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date To</label>
                            <input type="date" class="form-control" name="date_to" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Format</label>
                            <select class="form-select" name="format" required>
                                <option value="csv">CSV</option>
                                <option value="excel">Excel</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-download"></i> Generate & Download
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Recent Report Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Report Type</th>
                                    <th>Generated By</th>
                                    <th>Date Range</th>
                                    <th>Generated At</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentActivity">
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Loading recent activity...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .text-xs {
        font-size: 0.7rem;
    }
    .font-weight-bold {
        font-weight: 700 !important;
    }
    .text-gray-800 {
        color: #5a5c69 !important;
    }
    .text-gray-300 {
        color: #dddfeb !important;
    }
</style>
@endpush

@push('scripts')
<script type="module">
    $(document).ready(function() {
        // Load dashboard stats
        loadDashboardStats();
        
        // Load recent activity
        loadRecentActivity();
    });

    function loadDashboardStats() {
        // You can make an AJAX call to get real stats
        // For now, using placeholder data
        $('#totalBookings').text('245');
        $('#totalRevenue').text('$12,450');
        $('#activeUsers').text('1,234');
        $('#pendingBookings').text('18');
    }

    function loadRecentActivity() {
        // Simulate loading recent activity
        setTimeout(function() {
            const activities = [
                {
                    type: 'Bookings Report',
                    user: 'Admin User',
                    dateRange: 'Jan 1 - Jan 31, 2024',
                    generatedAt: '2 hours ago',
                    status: 'completed'
                },
                {
                    type: 'Sales Report',
                    user: 'Admin User',
                    dateRange: 'Dec 1 - Dec 31, 2023',
                    generatedAt: '1 day ago',
                    status: 'completed'
                },
                {
                    type: 'Financial Report',
                    user: 'Admin User',
                    dateRange: 'Q4 2023',
                    generatedAt: '3 days ago',
                    status: 'completed'
                }
            ];

            let html = '';
            activities.forEach(activity => {
                const statusColor = activity.status === 'completed' ? 'success' : 
                                  activity.status === 'processing' ? 'warning' : 'danger';
                
                html += `
                    <tr>
                        <td><span class="badge bg-primary">${activity.type}</span></td>
                        <td>${activity.user}</td>
                        <td>${activity.dateRange}</td>
                        <td>${activity.generatedAt}</td>
                        <td><span class="badge bg-${statusColor}">${activity.status}</span></td>
                    </tr>
                `;
            });

            if (html === '') {
                html = `
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No recent report activity
                        </td>
                    </tr>
                `;
            }

            $('#recentActivity').html(html);
        }, 1000);
    }

    // Handle form submission with loading state
    $('form').on('submit', function() {
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.html();
        
        btn.prop('disabled', true)
           .html('<i class="fas fa-spinner fa-spin"></i> Generating...');
           
        // Re-enable button after 3 seconds (adjust based on actual processing time)
        setTimeout(function() {
            btn.prop('disabled', false).html(originalText);
        }, 3000);
    });
</script>
@endpush