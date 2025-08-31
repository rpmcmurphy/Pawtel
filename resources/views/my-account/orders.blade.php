@extends('layouts.app')

@section('title', 'My Orders - Pawtel')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>My Orders</h2>
                    <a href="{{ route('account.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ !$currentStatus ? 'active' : '' }}" 
                           href="{{ route('account.orders') }}">
                            All Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $currentStatus === 'pending' ? 'active' : '' }}" 
                           href="{{ route('account.orders', ['status' => 'pending']) }}">
                            Pending
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $currentStatus === 'processing' ? 'active' : '' }}" 
                           href="{{ route('account.orders', ['status' => 'processing']) }}">
                            Processing
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $currentStatus === 'shipped' ? 'active' : '' }}" 
                           href="{{ route('account.orders', ['status' => 'shipped']) }}">
                            Shipped
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $currentStatus === 'delivered' ? 'active' : '' }}" 
                           href="{{ route('account.orders', ['status' => 'delivered']) }}">
                            Delivered
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $currentStatus === 'cancelled' ? 'active' : '' }}" 
                           href="{{ route('account.orders', ['status' => 'cancelled']) }}">
                            Cancelled
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Orders List -->
        <div class="row">
            <div class="col-12">
                @if(isset($orders['data']) && count($orders['data']) > 0)
                    @foreach($orders['data'] as $order)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <span class="badge badge-{{ 
                                            $order['status'] === 'delivered' ? 'success' : 
                                            ($order['status'] === 'processing' || $order['status'] === 'shipped' ? 'info' : 
                                            ($order['status'] === 'cancelled' ? 'danger' : 'warning'))
                                        }}">
                                            {{ ucfirst($order['status']) }}
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="mb-1">Order #{{ $order['order_number'] ?? $order['id'] }}</h6>
                                        <small class="text-muted">
                                            {{ isset($order['created_at']) ? date('M d, Y', strtotime($order['created_at'])) : '' }}
                                        </small>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-0">
                                            <strong>Items:</strong> {{ $order['total_items'] ?? count($order['items'] ?? []) }}<br>
                                            @if(isset($order['items']) && count($order['items']) > 0)
                                                <small class="text-muted">
                                                    {{ $order['items'][0]['product_name'] ?? 'Product' }}
                                                    @if(count($order['items']) > 1)
                                                        + {{ count($order['items']) - 1 }} more
                                                    @endif
                                                </small>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-0">
                                            <strong>Total:</strong><br>
                                            ${{ number_format($order['total_amount'] ?? 0, 2) }}
                                        </p>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <a href="{{ route('account.order.show', $order['id']) }}" 
                                           class="btn btn-sm btn-primary">
                                            View Details
                                        </a>
                                        @if($order['status'] === 'delivered')
                                            <button class="btn btn-sm btn-outline-success mt-1" disabled>
                                                <i class="fas fa-check"></i> Delivered
                                            </button>
                                        @elseif(in_array($order['status'], ['pending', 'processing']))
                                            <button class="btn btn-sm btn-outline-info mt-1" disabled>
                                                <i class="fas fa-clock"></i> In Progress
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                @if(isset($order['tracking_number']) && $order['tracking_number'])
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-info">
                                                <i class="fas fa-truck"></i> 
                                                Tracking: {{ $order['tracking_number'] }}
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if(isset($orders['meta']) && $orders['meta']['last_page'] > 1)
                        <div class="d-flex justify-content-center">
                            <nav aria-label="Orders pagination">
                                <ul class="pagination">
                                    @if($orders['meta']['current_page'] > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $orders['meta']['current_page'] - 1]) }}">Previous</a>
                                        </li>
                                    @endif
                                    
                                    @for($i = 1; $i <= $orders['meta']['last_page']; $i++)
                                        <li class="page-item {{ $i === $orders['meta']['current_page'] ? 'active' : '' }}">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                        </li>
                                    @endfor
                                    
                                    @if($orders['meta']['current_page'] < $orders['meta']['last_page'])
                                        <li class="page-item">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $orders['meta']['current_page'] + 1]) }}">Next</a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @endif
                @else
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders found</h5>
                            <p class="text-muted">You haven't placed any orders yet.</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary">
                                Start Shopping
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection