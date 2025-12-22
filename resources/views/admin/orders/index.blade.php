@extends('layouts.admin')

@section('title', 'Orders Management - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Orders Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Order number, customer..." value="{{ request('search') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Orders List</h5>
        </div>
        <div class="card-body">
            @php
                $ordersList = is_array($orders) && isset($orders['data']) ? $orders['data'] : (is_array($orders) ? $orders : []);
            @endphp
            @if(count($ordersList) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ordersList as $order)
                                <tr>
                                    <td>
                                        <strong>#{{ $order['order_number'] ?? $order['id'] }}</strong>
                                    </td>
                                    <td>
                                        {{ $order['customer']['name'] ?? 'N/A' }}<br>
                                        <small class="text-muted">{{ $order['customer']['email'] ?? '' }}</small>
                                    </td>
                                    <td>
                                        {{ $order['items_count'] ?? count($order['items'] ?? []) }} item(s)
                                    </td>
                                    <td>
                                        <strong>৳{{ number_format($order['total_amount'] ?? 0, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            ($order['status'] ?? '') == 'delivered' ? 'success' : 
                                            (($order['status'] ?? '') == 'cancelled' ? 'danger' : 
                                            (($order['status'] ?? '') == 'shipped' ? 'info' : 
                                            (($order['status'] ?? '') == 'processing' ? 'warning' : 'secondary'))) 
                                        }}">
                                            {{ ucfirst($order['status'] ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order['id']) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(isset($orders['pagination']))
                    <div class="d-flex justify-content-center mt-4">
                        <nav>
                            <ul class="pagination">
                                @php
                                    $pagination = is_array($orders) && isset($orders['pagination']) ? $orders['pagination'] : [];
                                    $lastPage = $pagination['last_page'] ?? 1;
                                    $currentPage = $pagination['current_page'] ?? 1;
                                @endphp
                                @for($i = 1; $i <= $lastPage; $i++)
                                    <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ route('admin.orders.index', array_merge(request()->all(), ['page' => $i])) }}">
                                            {{ $i }}
                                        </a>
                                    </li>
                                @endfor
                            </ul>
                        </nav>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No orders found.
                </div>
            @endif
        </div>
    </div>
@endsection

