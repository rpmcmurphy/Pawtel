@extends('layouts.admin')

@section('title', 'Users Management - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Users Management</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                <i class="fas fa-filter"></i> Filters
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="collapse mb-4" id="filtersCollapse">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" 
                               value="{{ $filters['search'] ?? '' }}" 
                               placeholder="Name, email...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role">
                            <option value="">All Roles</option>
                            <option value="user" {{ ($filters['role'] ?? '') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ ($filters['role'] ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="banned" {{ ($filters['status'] ?? '') === 'banned' ? 'selected' : '' }}>Banned</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Email Verified</label>
                        <select class="form-select" name="verified">
                            <option value="">All</option>
                            <option value="1" {{ ($filters['verified'] ?? '') === '1' ? 'selected' : '' }}>Verified</option>
                            <option value="0" {{ ($filters['verified'] ?? '') === '0' ? 'selected' : '' }}>Not Verified</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-users"></i> Users List
                @if(isset($users['total']))
                    <small class="text-muted">({{ $users['total'] }} total)</small>
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if(empty($users) || (is_array($users) && count($users) === 0) || (is_object($users) && $users->isEmpty()))
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No users found</h5>
                    <p class="text-muted">Try adjusting your filters or check back later.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Verified</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                @if(!empty($user['avatar']))
                                                    <img src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}" 
                                                         class="rounded-circle" width="32" height="32">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 32px; height: 32px; font-size: 12px;">
                                                        {{ strtoupper(substr($user['name'] ?? 'U', 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $user['name'] ?? 'Unknown' }}</div>
                                                @if(!empty($user['phone']))
                                                    <small class="text-muted">{{ $user['phone'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user['email'] ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ ($user['role'] ?? '') === 'admin' ? 'success' : 'primary' }}">
                                            {{ ucfirst($user['role'] ?? 'user') }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $status = $user['status'] ?? 'active';
                                            $statusColors = [
                                                'active' => 'success',
                                                'suspended' => 'warning', 
                                                'banned' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user['email_verified_at'] ?? false)
                                            <i class="fas fa-check-circle text-success" title="Verified"></i>
                                        @else
                                            <i class="fas fa-times-circle text-danger" title="Not Verified"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($user['created_at']))
                                            <span title="{{ $user['created_at'] }}">
                                                {{ \Carbon\Carbon::parse($user['created_at'])->format('M d, Y') }}
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                    data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.show', $user['id']) }}">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.bookings', $user['id']) }}">
                                                        <i class="fas fa-calendar"></i> View Bookings
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.orders', $user['id']) }}">
                                                        <i class="fas fa-shopping-cart"></i> View Orders
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item" onclick="changeUserStatus({{ $user['id'] }})">
                                                        <i class="fas fa-user-cog"></i> Change Status
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No users found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if(isset($users['links']) && $users['links'])
                    <div class="d-flex justify-content-center mt-4">
                        {!! $users['links'] !!}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Change Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change User Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="statusForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">New Status</label>
                            <select class="form-select" name="status" required>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                                <option value="banned">Banned</option>
                            </select>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Changing user status will affect their access to the platform.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="module">
    function changeUserStatus(userId) {
        const form = document.getElementById('statusForm');
        form.action = `/admin/users/${userId}/status`;
        
        const modal = new bootstrap.Modal(document.getElementById('statusModal'));
        modal.show();
    }

    // Initialize DataTable if needed
    $(document).ready(function() {
        if ($('#usersTable tbody tr').length > 10) {
            $('#usersTable').DataTable({
                pageLength: 25,
                order: [[5, 'desc']], // Sort by created date
                columnDefs: [
                    { orderable: false, targets: [0, 6] } // Disable sorting for avatar and actions
                ]
            });
        }
    });
</script>
@endpush