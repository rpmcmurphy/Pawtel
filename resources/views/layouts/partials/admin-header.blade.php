<div class="admin-header">
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
</div>
