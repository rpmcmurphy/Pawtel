@extends('layouts.admin')

@section('title', 'User Details - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">User Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>
@endsection

@section('content')
    @if (empty($user))
        <div class="text-center py-5">
            <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">User not found</h5>
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Back to Users</a>
        </div>
    @else
        <div class="row">
            <!-- User Info Card -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user"></i> Profile Information
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if (!empty($user['avatar']))
                                <img src="{{ $user['avatar'] }}" class="rounded-circle mb-2" width="80" height="80"
                                    alt="Avatar">
                            @else
                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                    style="width: 80px; height: 80px;">
                                    <i class="fas fa-user fa-2x text-white"></i>
                                </div>
                            @endif
                        </div>

                        <h5 class="mb-1">{{ $user['name'] ?? 'N/A' }}</h5>
                        <p class="text-muted mb-3">{{ $user['email'] ?? 'N/A' }}</p>

                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <span
                                class="badge bg-{{ $user['status'] === 'active' ? 'success' : ($user['status'] === 'suspended' ? 'warning' : 'danger') }} px-3 py-2">
                                {{ ucfirst($user['status'] ?? 'Unknown') }}
                            </span>
                            <span
                                class="badge bg-{{ $user['email_verified_at'] ?? false ? 'success' : 'secondary' }} px-3 py-2">
                                {{ $user['email_verified_at'] ?? false ? 'Verified' : 'Unverified' }}
                            </span>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="changeUserStatus({{ $user['id'] ?? 0 }})">
                                <i class="fas fa-user-cog"></i> Change Status
                            </button>
                            <a href="{{ route('admin.users.bookings', $user['id'] ?? 0) }}"
                                class="btn btn-outline-primary">
                                <i class="fas fa-calendar"></i> View Bookings
                            </a>
                            <a href="{{ route('admin.users.orders', $user['id'] ?? 0) }}" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-cart"></i> View Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Card -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Account Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <p class="form-control-plaintext">{{ $user['name'] ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <p class="form-control-plaintext">{{ $user['email'] ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Phone Number</label>
                                <p class="form-control-plaintext">{{ $user['phone'] ?? 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Role</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info px-3 py-2">{{ ucfirst($user['role'] ?? 'user') }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Account Status</label>
                                <p class="form-control-plaintext">
                                    <span
                                        class="badge bg-{{ $user['status'] === 'active' ? 'success' : ($user['status'] === 'suspended' ? 'warning' : 'danger') }} px-3 py-2">
                                        {{ ucfirst($user['status'] ?? 'Unknown') }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Verified</label>
                                <p class="form-control-plaintext">
                                    @if ($user['email_verified_at'] ?? false)
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                        <small
                                            class="text-muted d-block">{{ date('M j, Y', strtotime($user['email_verified_at'])) }}</small>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">
                                            <i class="fas fa-times-circle"></i> Unverified
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Member Since</label>
                                <p class="form-control-plaintext">
                                    {{ isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Last Updated</label>
                                <p class="form-control-plaintext">
                                    {{ isset($user['updated_at']) ? date('M j, Y g:i A', strtotime($user['updated_at'])) : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Bookings
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $user['stats']['total_bookings'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-left-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Orders
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $user['stats']['total_orders'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-left-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Spent
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ${{ number_format($user['stats']['total_spent'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script type="module">
        function changeUserStatus(userId) {
            const currentStatus = @json($user['status'] ?? 'active');

            Swal.fire({
                title: 'Change User Status',
                html: `
            <select id="statusSelect" class="form-select">
                <option value="active" ${currentStatus === 'active' ? 'selected' : ''}>Active</option>
                <option value="suspended" ${currentStatus === 'suspended' ? 'selected' : ''}>Suspended</option>
                <option value="banned" ${currentStatus === 'banned' ? 'selected' : ''}>Banned</option>
            </select>
        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Update Status',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const status = document.getElementById('statusSelect').value;
                    if (!status) {
                        Swal.showValidationMessage('Please select a status');
                        return false;
                    }
                    return status;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('admin.users.status', $user['id'] ?? 0) }}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PUT';

                    const statusField = document.createElement('input');
                    statusField.type = 'hidden';
                    statusField.name = 'status';
                    statusField.value = result.value;

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    form.appendChild(statusField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
