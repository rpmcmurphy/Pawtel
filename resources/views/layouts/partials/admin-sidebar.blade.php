<div class="admin-sidebar">
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

            <!-- Bookings Section -->
            <li class="nav-divider"></li>

            <p class="divider-title">Bookings</p>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}"
                    href="{{ route('admin.bookings.index') }}">
                    <i class="fas fa-list"></i>
                    <span>All Bookings</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bookings.create') ? 'active' : '' }}"
                    href="{{ route('admin.bookings.create') }}">
                    <i class="fas fa-plus"></i>
                    <span>Create Booking</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bookings.type') && request()->route('type') === 'hotel' ? 'active' : '' }}"
                    href="{{ route('admin.bookings.type', 'hotel') }}">
                    <i class="fas fa-hotel"></i>
                    <span>Hotel Bookings</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bookings.type') && request()->route('type') === 'spa' ? 'active' : '' }}"
                    href="{{ route('admin.bookings.type', 'spa') }}">
                    <i class="fas fa-spa"></i>
                    <span>Spa Bookings</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bookings.type') && request()->route('type') === 'spay_neuter' ? 'active' : '' }}"
                    href="{{ route('admin.bookings.type', 'spay_neuter') }}">
                    <i class="fas fa-heartbeat"></i>
                    <span>Spay/Neuter</span>
                </a>
            </li>

            <!-- Products Section -->
            <li class="nav-divider"></li>

            <p class="divider-title">Products</p>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}"
                    href="{{ route('admin.products.index') }}">
                    <i class="fas fa-list"></i>
                    <span>All Products</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}"
                    href="{{ route('admin.products.create') }}">
                    <i class="fas fa-plus"></i>
                    <span>Add Product</span>
                </a>
            </li>

            <!-- Users Section -->
            <li class="nav-divider"></li>

            <p class="divider-title">Users</p>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <i class="fas fa-list"></i>
                    <span>All Users</span>
                </a>
            </li>

            <!-- Community Posts Section -->
            <li class="nav-divider"></li>

            <p class="divider-title">Posts</p>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.posts.index') ? 'active' : '' }}"
                    href="{{ route('admin.posts.index') }}">
                    <i class="fas fa-list"></i>
                    <span>All Posts</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.posts.create') ? 'active' : '' }}"
                    href="{{ route('admin.posts.create') }}">
                    <i class="fas fa-plus"></i>
                    <span>Create Post</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->fullUrlIs(route('admin.posts.index', ['type' => 'adoption'])) ? 'active' : '' }}"
                    href="{{ route('admin.posts.index', ['type' => 'adoption']) }}">
                    <i class="fas fa-heart"></i>
                    <span>Adoptions</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->fullUrlIs(route('admin.posts.index', ['type' => 'story'])) ? 'active' : '' }}"
                    href="{{ route('admin.posts.index', ['type' => 'story']) }}">
                    <i class="fas fa-book"></i>
                    <span>Stories</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->fullUrlIs(route('admin.posts.index', ['type' => 'news'])) ? 'active' : '' }}"
                    href="{{ route('admin.posts.index', ['type' => 'news']) }}">
                    <i class="fas fa-newspaper"></i>
                    <span>News</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->fullUrlIs(route('admin.posts.index', ['type' => 'job'])) ? 'active' : '' }}"
                    href="{{ route('admin.posts.index', ['type' => 'job']) }}">
                    <i class="fas fa-briefcase"></i>
                    <span>Jobs</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.posts.comments.pending') ? 'active' : '' }}"
                    href="{{ route('admin.posts.comments.pending') }}">
                    <i class="fas fa-clock text-warning"></i>
                    <span>Pending Comments</span>
                </a>
            </li>

            <!-- Reports Section -->
            <li class="nav-divider"></li>

            <p class="divider-title">Reports</p>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}"
                    href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.bookings') ? 'active' : '' }}"
                    href="{{ route('admin.reports.bookings') }}">
                    <i class="fas fa-calendar"></i>
                    <span>Bookings Report</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}"
                    href="{{ route('admin.reports.sales') }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Sales Report</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.financial') ? 'active' : '' }}"
                    href="{{ route('admin.reports.financial') }}">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Financial Report</span>
                </a>
            </li>

            <!-- Quick Actions Section -->
            <li class="nav-divider"></li>

            <p class="divider-title">Quick Actions</p>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Website</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.bookings.create') }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Quick Booking</span>
                </a>
            </li>

            <!-- System Section -->
            <li class="nav-divider"></li>

            <p class="divider-title">System</p>

            <li class="nav-item">
                <form action="{{ route('auth.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-start w-100 border-0 p-0">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
