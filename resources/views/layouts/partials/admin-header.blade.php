{{-- <div class="admin-header">
    <div class="d-flex justify-content-between align-items-center">
        <div class="admin-breadcrumb">
            @hasSection('breadcrumb')
                @yield('breadcrumb')
            @else
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item active">@yield('page-title', 'Dashboard')</li>
                    </ol>
                </nav>
            @endif
        </div>

        <div class="admin-user-menu">
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                    {{ session('user')['name'] }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('auth.profile') }}">
                            <i class="fas fa-user"></i> Profile
                        </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('auth.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div> --}}

<header class="admin-header">
    <div class="header-container">
        <!-- Mobile Sidebar Toggle -->
        <div class="header-left">
            <button type="button" class="btn btn-link sidebar-toggle d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Page Title (optional - can be set via yield) -->
            <div class="page-title d-none d-md-block">
                @hasSection('page-title')
                    <h4 class="mb-0">@yield('page-title')</h4>
                @endif
            </div>
        </div>

        <!-- Header Actions -->
        <div class="header-right">
            <!-- Quick Search -->
            <div class="header-search d-none d-md-flex">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Search bookings, users..." id="quickSearch">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Notifications -->
            <div class="dropdown header-dropdown">
                <button class="btn btn-link header-btn" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="badge bg-danger notification-badge">3</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>Notifications</span>
                        <small><a href="#" class="text-primary">Mark all read</a></small>
                    </div>
                    <div class="dropdown-divider"></div>
                    
                    <!-- Notification Items -->
                    <a class="dropdown-item notification-item" href="#">
                        <div class="notification-icon bg-primary">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">New booking received</div>
                            <div class="notification-text">Hotel booking for Mr. Whiskers</div>
                            <div class="notification-time">5 minutes ago</div>
                        </div>
                    </a>

                    <a class="dropdown-item notification-item" href="#">
                        <div class="notification-icon bg-success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">Payment received</div>
                            <div class="notification-text">$89.99 from John Doe</div>
                            <div class="notification-time">1 hour ago</div>
                        </div>
                    </a>

                    <a class="dropdown-item notification-item" href="#">
                        <div class="notification-icon bg-info">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">New user registered</div>
                            <div class="notification-text">Sarah Johnson joined</div>
                            <div class="notification-time">2 hours ago</div>
                        </div>
                    </a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center" href="#">View all notifications</a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dropdown header-dropdown">
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus"></i>
                    <span class="d-none d-md-inline ms-1">Quick Actions</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <h6 class="dropdown-header">Create New</h6>
                    <a class="dropdown-item" href="{{ route('admin.bookings.create') }}">
                        <i class="fas fa-calendar-plus"></i> Manual Booking
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.products.create') }}">
                        <i class="fas fa-box"></i> Product
                    </a>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Reports</h6>
                    <a class="dropdown-item" href="{{ route('admin.reports.bookings') }}">
                        <i class="fas fa-chart-bar"></i> Booking Report
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.reports.sales') }}">
                        <i class="fas fa-chart-line"></i> Sales Report
                    </a>
                </div>
            </div>

            <!-- User Profile -->
            <div class="dropdown header-dropdown">
                <button class="btn btn-link header-btn user-btn" type="button" data-bs-toggle="dropdown">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="user-name d-none d-md-inline">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <i class="fas fa-chevron-down ms-1"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end user-dropdown">
                    <div class="dropdown-header">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ auth()->user()->name ?? 'Admin User' }}</div>
                                <small class="text-muted">{{ auth()->user()->email ?? 'admin@pawtel.com' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    
                    <a class="dropdown-item" href="{{ route('auth.profile') }}">
                        <i class="fas fa-user-cog"></i> Profile Settings
                    </a>
                    <a class="dropdown-item" href="{{ route('home') }}" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Website
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('auth.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Search Results (Hidden by default) -->
    <div class="search-results" id="searchResults" style="display: none;">
        <div class="search-results-content">
            <div class="search-loading">
                <i class="fas fa-spinner fa-spin"></i> Searching...
            </div>
        </div>
    </div>
</header>

@push('styles')
<style>
    .admin-header {
        background: white;
        border-bottom: 1px solid #e3e6f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        position: sticky;
        top: 0;
        z-index: 1040;
    }

    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        min-height: 60px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sidebar-toggle {
        border: none;
        background: none;
        color: #6c757d;
        font-size: 1.25rem;
        padding: 0.5rem;
    }

    .sidebar-toggle:hover {
        color: #495057;
    }

    .page-title h4 {
        color: #495057;
        font-weight: 600;
    }

    .header-search {
        margin-right: 0.5rem;
    }

    .header-search .input-group {
        width: 300px;
    }

    .header-btn {
        position: relative;
        border: none;
        background: none;
        color: #6c757d;
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .header-btn:hover {
        color: #495057;
        background: #f8f9fa;
    }

    .notification-badge {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
        font-size: 0.7rem;
        min-width: 18px;
        height: 18px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.5rem;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        background: #6c757d;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.875rem;
    }

    .user-name {
        font-weight: 500;
        color: #495057;
    }

    /* Dropdown Styles */
    .header-dropdown .dropdown-menu {
        border: none;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        border-radius: 0.5rem;
        margin-top: 0.5rem;
    }

    .notification-dropdown {
        width: 350px;
        max-height: 400px;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        align-items: flex-start;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f8f9fa;
        transition: background-color 0.2s ease;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.25rem;
    }

    .notification-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #adb5bd;
    }

    .user-dropdown {
        width: 280px;
    }

    .user-dropdown .dropdown-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 0;
        padding: 1rem;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e3e6f0;
        border-top: none;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        z-index: 1000;
    }

    .search-results-content {
        padding: 1rem;
    }

    .search-loading {
        text-align: center;
        color: #6c757d;
        padding: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-search {
            display: none !important;
        }
        
        .user-name {
            display: none;
        }
        
        .header-right {
            gap: 0.25rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick search functionality
    const searchInput = document.getElementById('quickSearch');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 300);
        });

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }

    function performSearch(query) {
        searchResults.style.display = 'block';
        
        // Show loading state
        searchResults.innerHTML = `
            <div class="search-results-content">
                <div class="search-loading">
                    <i class="fas fa-spinner fa-spin"></i> Searching...
                </div>
            </div>
        `;

        // Simulate search API call (replace with actual implementation)
        setTimeout(() => {
            const mockResults = [
                {
                    type: 'booking',
                    title: 'Booking #BK001',
                    subtitle: 'Mr. Whiskers - Hotel Booking',
                    url: '/admin/bookings/BK001'
                },
                {
                    type: 'user',
                    title: 'John Doe',
                    subtitle: 'john@example.com',
                    url: '/admin/users/1'
                }
            ];

            displaySearchResults(mockResults, query);
        }, 500);
    }

    function displaySearchResults(results, query) {
        if (results.length === 0) {
            searchResults.innerHTML = `
                <div class="search-results-content">
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-search"></i>
                        <div class="mt-2">No results found for "${query}"</div>
                    </div>
                </div>
            `;
            return;
        }

        let resultsHtml = '<div class="search-results-content">';
        
        results.forEach(result => {
            const icon = result.type === 'booking' ? 'calendar' : 'user';
            resultsHtml += `
                <a href="${result.url}" class="d-block p-2 text-decoration-none border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-${icon} text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">${result.title}</div>
                            <small class="text-muted">${result.subtitle}</small>
                        </div>
                    </div>
                </a>
            `;
        });
        
        resultsHtml += '</div>';
        searchResults.innerHTML = resultsHtml;
    }

    // Auto-hide notifications badge after interaction
    const notificationDropdown = document.querySelector('.notification-dropdown');
    if (notificationDropdown) {
        notificationDropdown.addEventListener('shown.bs.dropdown', function() {
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                setTimeout(() => {
                    badge.style.display = 'none';
                }, 1000);
            }
        });
    }
});
</script>
@endpush