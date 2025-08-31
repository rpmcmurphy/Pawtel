{{-- <div class="admin-sidebar">
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="sidebar-logo">
            <i class="fas fa-paw"></i>
            <span>Pawtel</span>
        </a>
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
                    href="{{ route('admin.bookings.index') }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Bookings</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                    href="{{ route('admin.products.index') }}">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                    href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>

            <li class="nav-divider"></li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-globe"></i>
                    <span>View Website</span>
                </a>
            </li>
        </ul>
    </div>
</div> --}}


<nav class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <i class="fas fa-paw text-primary"></i>
            <span class="brand-text">Pawtel Admin</span>
        </a>
    </div>

    <div class="sidebar-user">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="user-details">
                <div class="user-name">{{ auth()->user()->name ?? 'Admin User' }}</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>

    <div class="sidebar-menu">
        <ul class="nav nav-sidebar">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- Bookings Management -->
            <li class="nav-item {{ request()->routeIs('admin.bookings.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#bookingsMenu">
                    <i class="fas fa-calendar nav-icon"></i>
                    <span class="nav-text">Bookings</span>
                    <i class="fas fa-angle-left nav-arrow"></i>
                </a>
                <ul class="nav nav-treeview collapse {{ request()->routeIs('admin.bookings.*') ? 'show' : '' }}" id="bookingsMenu">
                    <li class="nav-item">
                        <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}">
                            <i class="fas fa-list nav-icon"></i>
                            <span class="nav-text">All Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.bookings.create') }}" class="nav-link {{ request()->routeIs('admin.bookings.create') ? 'active' : '' }}">
                            <i class="fas fa-plus nav-icon"></i>
                            <span class="nav-text">Create Booking</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.bookings.type', 'hotel') }}" class="nav-link {{ request()->routeIs('admin.bookings.type') && request()->route('type') === 'hotel' ? 'active' : '' }}">
                            <i class="fas fa-hotel nav-icon"></i>
                            <span class="nav-text">Hotel Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.bookings.type', 'spa') }}" class="nav-link {{ request()->routeIs('admin.bookings.type') && request()->route('type') === 'spa' ? 'active' : '' }}">
                            <i class="fas fa-spa nav-icon"></i>
                            <span class="nav-text">Spa Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.bookings.type', 'spay_neuter') }}" class="nav-link {{ request()->routeIs('admin.bookings.type') && request()->route('type') === 'spay_neuter' ? 'active' : '' }}">
                            <i class="fas fa-heartbeat nav-icon"></i>
                            <span class="nav-text">Spay/Neuter</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Products Management -->
            <li class="nav-item {{ request()->routeIs('admin.products.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#productsMenu">
                    <i class="fas fa-box nav-icon"></i>
                    <span class="nav-text">Products</span>
                    <i class="fas fa-angle-left nav-arrow"></i>
                </a>
                <ul class="nav nav-treeview collapse {{ request()->routeIs('admin.products.*') ? 'show' : '' }}" id="productsMenu">
                    <li class="nav-item">
                        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                            <i class="fas fa-list nav-icon"></i>
                            <span class="nav-text">All Products</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.products.create') }}" class="nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                            <i class="fas fa-plus nav-icon"></i>
                            <span class="nav-text">Add Product</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Users Management -->
            <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#usersMenu">
                    <i class="fas fa-users nav-icon"></i>
                    <span class="nav-text">Users</span>
                    <i class="fas fa-angle-left nav-arrow"></i>
                </a>
                <ul class="nav nav-treeview collapse {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="usersMenu">
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                            <i class="fas fa-list nav-icon"></i>
                            <span class="nav-text">All Users</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Reports -->
            <li class="nav-item {{ request()->routeIs('admin.reports.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#reportsMenu">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <span class="nav-text">Reports</span>
                    <i class="fas fa-angle-left nav-arrow"></i>
                </a>
                <ul class="nav nav-treeview collapse {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}" id="reportsMenu">
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.bookings') }}" class="nav-link {{ request()->routeIs('admin.reports.bookings') ? 'active' : '' }}">
                            <i class="fas fa-calendar nav-icon"></i>
                            <span class="nav-text">Bookings Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.sales') }}" class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart nav-icon"></i>
                            <span class="nav-text">Sales Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.financial') }}" class="nav-link {{ request()->routeIs('admin.reports.financial') ? 'active' : '' }}">
                            <i class="fas fa-dollar-sign nav-icon"></i>
                            <span class="nav-text">Financial Report</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Divider -->
            <li class="nav-divider">
                <hr>
            </li>

            <!-- Quick Actions -->
            <li class="nav-header">
                <span class="nav-header-text">Quick Actions</span>
            </li>

            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link" target="_blank">
                    <i class="fas fa-external-link-alt nav-icon"></i>
                    <span class="nav-text">View Site</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.bookings.create') }}" class="nav-link">
                    <i class="fas fa-plus-circle nav-icon"></i>
                    <span class="nav-text">Quick Booking</span>
                </a>
            </li>

            <!-- System -->
            <li class="nav-divider">
                <hr>
            </li>

            <li class="nav-header">
                <span class="nav-header-text">System</span>
            </li>

            <li class="nav-item">
                <form action="{{ route('auth.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-start w-100 border-0 p-0">
                        <i class="fas fa-sign-out-alt nav-icon"></i>
                        <span class="nav-text">Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="version-info">
            <small class="text-muted">Pawtel Admin v1.0</small>
        </div>
    </div>
</nav>

<!-- Sidebar Toggle Button (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

@push('styles')
<style>
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 260px;
        background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
        color: #ecf0f1;
        overflow-y: auto;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 1050;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }

    .admin-sidebar.show {
        transform: translateX(0);
    }

    .sidebar-brand {
        padding: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .brand-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #ecf0f1;
        font-size: 1.25rem;
        font-weight: bold;
    }

    .brand-link:hover {
        color: #3498db;
    }

    .brand-text {
        margin-left: 0.5rem;
    }

    .sidebar-user {
        padding: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        background: rgba(0,0,0,0.1);
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: #3498db;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
    }

    .user-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .user-role {
        font-size: 0.875rem;
        color: #95a5a6;
    }

    .sidebar-menu {
        flex: 1;
        padding: 1rem 0;
    }

    .nav-sidebar {
        flex-direction: column;
    }

    .nav-item {
        margin-bottom: 0.25rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: #bdc3c7;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }

    .nav-link:hover {
        background: rgba(52, 152, 219, 0.2);
        color: #ecf0f1;
    }

    .nav-link.active {
        background: #3498db;
        color: white;
    }

    .nav-icon {
        width: 20px;
        margin-right: 0.75rem;
        text-align: center;
    }

    .nav-arrow {
        margin-left: auto;
        transition: transform 0.2s ease;
    }

    .nav-link[aria-expanded="true"] .nav-arrow {
        transform: rotate(-90deg);
    }

    .nav-treeview {
        background: rgba(0,0,0,0.1);
        margin-left: 1rem;
        border-left: 2px solid rgba(255,255,255,0.1);
    }

    .nav-treeview .nav-link {
        padding: 0.5rem 1rem 0.5rem 2rem;
        font-size: 0.9rem;
    }

    .nav-divider hr {
        border-color: rgba(255,255,255,0.1);
        margin: 0.5rem 1rem;
    }

    .nav-header {
        padding: 0.5rem 1rem;
    }

    .nav-header-text {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
        color: #95a5a6;
        letter-spacing: 0.05em;
    }

    .sidebar-footer {
        padding: 1rem;
        border-top: 1px solid rgba(255,255,255,0.1);
        text-align: center;
    }

    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1049;
        display: none;
    }

    /* Desktop styles */
    @media (min-width: 992px) {
        .admin-sidebar {
            transform: translateX(0);
            position: sticky;
        }
        
        .admin-content {
            margin-left: 260px;
        }
    }

    /* Mobile responsive */
    @media (max-width: 991px) {
        .sidebar-overlay.show {
            display: block;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // Auto-close mobile sidebar on navigation
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                setTimeout(() => {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }, 100);
            }
        });
    });
});
</script>
@endpush