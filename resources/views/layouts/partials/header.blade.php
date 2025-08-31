<header class="main-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand logo" href="{{ route('home') }}">
                <i class="fas fa-paw logo-icon"></i>
                Pawtel
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('booking.*') ? 'active' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown">
                            Booking
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('booking.hotel.index') }}">
                                    <i class="fas fa-hotel"></i> Cat Hotel
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('booking.spa.index') }}">
                                    <i class="fas fa-spa"></i> Spa & Grooming
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('booking.spay.index') }}">
                                    <i class="fas fa-stethoscope"></i> Spay/Neuter
                                </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}"
                            href="{{ route('shop.index') }}">
                            Shop
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('community.*') ? 'active' : '' }}"
                            href="{{ route('community.posts') }}">
                            Community
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    @if (session('user'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-dropdown" href="#" role="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                {{ session('user')['name'] }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('account.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a></li>
                                <li><a class="dropdown-item" href="{{ route('auth.profile') }}">
                                        <i class="fas fa-user"></i> Profile
                                    </a></li>
                                <li><a class="dropdown-item" href="{{ route('account.bookings') }}">
                                        <i class="fas fa-calendar"></i> My Bookings
                                    </a></li>
                                <li><a class="dropdown-item" href="{{ route('account.orders') }}">
                                        <i class="fas fa-shopping-bag"></i> My Orders
                                    </a></li>
                                @if (session('user')['role'] === 'admin')
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-cog"></i> Admin Panel
                                        </a></li>
                                @endif
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
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('auth.login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm ms-2" href="{{ route('auth.register') }}">Register</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
</header>
