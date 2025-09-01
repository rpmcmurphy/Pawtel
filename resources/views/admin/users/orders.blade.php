@extends('layouts.admin')

@section('title', 'User Orders - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">User Orders</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.show', $user_id) }}" class="btn btn-outline-primary">
                <i class="fas fa-user"></i> User Profile
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.orders', $user_id) }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="processing" {{ ($filters['status'] ?? '') === 'processing' ? 'selected' : '' }}>
                            Processing</option>
                        <option value="shipped" {{ ($filters['status'] ?? '') === 'shipped' ? 'selected' : '' }}>Shipped
                        </option>
                        <option value="delivered" {{ ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' }}>
                            Delivered</option>
                        <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>
                            Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.users.orders', $user_id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-shopping-cart"></i> Orders List
                @if (isset($orders['pagination']['total']))
                    <small class="text-muted">({{ $orders['pagination']['total'] }} total)</small>
                @elseif (!empty($orders) && is_countable($orders))
                    <small class="text-muted">({{ count($orders) }} total)</small>
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if (empty($orders) || (is_array($orders) && count($orders) === 0) || (isset($orders['data']) && empty($orders['data'])))
                <div class="text-center py-4">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No orders found</h5>
                    <p class="text-muted">This user hasn't placed any orders yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Delivery</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders['data'] ?? $orders as $order)
                                <tr>
                                    <td>
                                        <strong>#{{ $order['order_number'] ?? $order['id'] }}</strong>
                                    </td>
                                    <td>
                                        @if (!empty($order['items']))
                                            <div class="d-flex flex-column">
                                                @foreach (array_slice($order['items'], 0, 2) as $item)
                                                    <small class="text-truncate" style="max-width: 200px;">
                                                        {{ $item['quantity'] ?? 1 }}x
                                                        {{ $item['product']['name'] ?? 'Product' }}
                                                    </small>
                                                @endforeach
                                                @if (count($order['items']) > 2)
                                                    <small class="text-muted">+{{ count($order['items']) - 2 }} more
                                                        items</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">No items</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ number_format($order['total_amount'] ?? 0, 2) }}</strong>
                                        @if (!empty($order['subtotal']) && $order['subtotal'] != $order['total_amount'])
                                            <br><small class="text-muted">Subtotal:
                                                ${{ number_format($order['subtotal'], 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($order['delivery_charge']) && $order['delivery_charge'] > 0)
                                            <span
                                                class="text-success">+${{ number_format($order['delivery_charge'], 2) }}</span>
                                        @else
                                            <span class="text-muted">Free</span>
                                        @endif
                                        @if (!empty($order['delivery_address']))
                                            <br><small class="text-muted text-truncate d-block" style="max-width: 150px;"
                                                title="{{ $order['delivery_address'] }}">
                                                {{ Str::limit($order['delivery_address'], 30) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = $order['status'] ?? 'pending';
                                            $statusClass = match ($status) {
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'shipped' => 'primary',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                        @if ($status === 'delivered' && !empty($order['delivered_at']))
                                            <br><small
                                                class="text-muted">{{ date('M j', strtotime($order['delivered_at'])) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ isset($order['created_at']) ? date('M j, Y', strtotime($order['created_at'])) : 'N/A' }}
                                        </small>
                                        @if (!empty($order['created_at']))
                                            <br><small
                                                class="text-muted">{{ date('g:i A', strtotime($order['created_at'])) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.orders.show', $order['id'] ?? 0) }}">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                </li>
                                                @if (in_array($status, ['pending', 'processing']))
                                                    <li>
                                                        <button class="dropdown-item"
                                                            onclick="updateOrderStatus({{ $order['id'] ?? 0 }}, 'processing')">
                                                            <i class="fas fa-cogs"></i> Mark Processing
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item"
                                                            onclick="updateOrderStatus({{ $order['id'] ?? 0 }}, 'shipped')">
                                                            <i class="fas fa-truck"></i> Mark Shipped
                                                        </button>
                                                    </li>
                                                @endif
                                                @if ($status === 'shipped')
                                                    <li>
                                                        <button class="dropdown-item"
                                                            onclick="updateOrderStatus({{ $order['id'] ?? 0 }}, 'delivered')">
                                                            <i class="fas fa-check-circle"></i> Mark Delivered
                                                        </button>
                                                    </li>
                                                @endif
                                                @if (in_array($status, ['pending', 'processing']))
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item text-danger"
                                                            onclick="cancelOrder({{ $order['id'] ?? 0 }})">
                                                            <i class="fas fa-times"></i> Cancel Order
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-shopping-bag fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No orders found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if (isset($orders['pagination']) && $orders['pagination']['total'] > $orders['pagination']['per_page'])
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Orders pagination">
                            <ul class="pagination">
                                @if ($orders['pagination']['current_page'] > 1)
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ request()->fullUrlWithQuery(['page' => $orders['pagination']['current_page'] - 1]) }}">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                @for ($i = 1; $i <= $orders['pagination']['last_page']; $i++)
                                    <li
                                        class="page-item {{ $i == $orders['pagination']['current_page'] ? 'active' : '' }}">
                                        <a class="page-link"
                                            href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                @if ($orders['pagination']['current_page'] < $orders['pagination']['last_page'])
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ request()->fullUrlWithQuery(['page' => $orders['pagination']['current_page'] + 1]) }}">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        function updateOrderStatus(orderId, status) {
            const statusText = status.charAt(0).toUpperCase() + status.slice(1);

            Swal.fire({
                title: `Update Order Status?`,
                text: `Mark this order as ${statusText.toLowerCase()}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Yes, Mark ${statusText}`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to order management (assuming there's a route for status update)
                    window.location.href = `/admin/orders/${orderId}?action=update_status&status=${status}`;
                }
            });
        }

        function cancelOrder(orderId) {
            Swal.fire({
                title: 'Cancel Order?',
                text: 'Are you sure you want to cancel this order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Cancel Order',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'No, Keep Order'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to order management for cancellation
                    window.location.href = `/admin/orders/${orderId}?action=cancel`;
                }
            });
        }
    </script>
@endpush
