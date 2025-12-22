@extends('layouts.admin')

@section('title', 'Order Details - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Order #{{ $order['order_number'] ?? $order['id'] }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.invoice', $order['id']) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-file-invoice"></i> Invoice
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Order Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order Number:</strong> #{{ $order['order_number'] ?? $order['id'] }}</p>
                            <p><strong>Customer:</strong> {{ $order['customer']['name'] ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $order['customer']['email'] ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $order['customer']['phone'] ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ 
                                    ($order['status'] ?? '') == 'delivered' ? 'success' : 
                                    (($order['status'] ?? '') == 'cancelled' ? 'danger' : 
                                    (($order['status'] ?? '') == 'shipped' ? 'info' : 
                                    (($order['status'] ?? '') == 'processing' ? 'warning' : 'secondary'))) 
                                }}">
                                    {{ ucfirst($order['status'] ?? 'N/A') }}
                                </span>
                            </p>
                            <p><strong>Order Date:</strong> {{ isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('M d, Y g:i A') : 'N/A' }}</p>
                            <p><strong>Payment Method:</strong> {{ ucfirst($order['payment_method'] ?? 'COD') }}</p>
                            @if(isset($order['delivered_at']))
                                <p><strong>Delivered At:</strong> {{ \Carbon\Carbon::parse($order['delivered_at'])->format('M d, Y g:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <p><strong>Delivery Address:</strong></p>
                            <p>{{ $order['delivery_address'] ?? 'N/A' }}</p>
                            @if(isset($order['delivery_phone']))
                                <p><strong>Delivery Phone:</strong> {{ $order['delivery_phone'] }}</p>
                            @endif
                            @if(isset($order['delivery_notes']))
                                <p><strong>Delivery Notes:</strong> {{ $order['delivery_notes'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($order['items']) && is_array($order['items']))
                                    @foreach($order['items'] as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item['product']['name'] ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">SKU: {{ $item['product']['sku'] ?? 'N/A' }}</small>
                                            </td>
                                            <td>{{ $item['quantity'] ?? 0 }}</td>
                                            <td>৳{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                            <td><strong>৳{{ number_format($item['total_price'] ?? 0, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No items found</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal:</th>
                                    <th>৳{{ number_format($order['subtotal'] ?? $order['total_amount'] ?? 0, 2) }}</th>
                                </tr>
                                @if(isset($order['delivery_charge']) && $order['delivery_charge'] > 0)
                                    <tr>
                                        <th colspan="3" class="text-end">Delivery Charge:</th>
                                        <th>৳{{ number_format($order['delivery_charge'], 2) }}</th>
                                    </tr>
                                @endif
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>৳{{ number_format($order['total_amount'] ?? 0, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-md-4">
            <!-- Status Update -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Update Status</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order['id']) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ ($order['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ ($order['status'] ?? '') == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ ($order['status'] ?? '') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ ($order['status'] ?? '') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ ($order['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    @if(($order['status'] ?? '') == 'processing')
                        <form method="POST" action="{{ route('admin.orders.ship', $order['id']) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fas fa-shipping-fast"></i> Mark as Shipped
                            </button>
                        </form>
                    @endif

                    @if(($order['status'] ?? '') == 'shipped')
                        <form method="POST" action="{{ route('admin.orders.deliver', $order['id']) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check-circle"></i> Mark as Delivered
                            </button>
                        </form>
                    @endif

                    @if(in_array($order['status'] ?? '', ['pending', 'processing']))
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                            <i class="fas fa-times-circle"></i> Cancel Order
                        </button>
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>৳{{ number_format($order['subtotal'] ?? $order['total_amount'] ?? 0, 2) }}</span>
                    </div>
                    @if(isset($order['delivery_charge']) && $order['delivery_charge'] > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Charge:</span>
                            <span>৳{{ number_format($order['delivery_charge'], 2) }}</span>
                        </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>৳{{ number_format($order['total_amount'] ?? 0, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.orders.cancel', $order['id']) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Cancellation Reason *</label>
                            <textarea name="reason" class="form-control" rows="3" required placeholder="Enter reason for cancellation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Cancel Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

