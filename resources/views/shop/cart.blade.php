@extends('layouts.app')

@section('title', 'Shopping Cart - Pawtel')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Shopping Cart</h2>
            </div>
        </div>

        @if (!empty($cart['items']))
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            @foreach ($cart['items'] as $item)
                                <div class="row align-items-center mb-3 pb-3 border-bottom">
                                    <div class="col-md-2">
                                        <img src="{{ $item['product']['image_url'] ?? 'https://via.placeholder.com/100x80' }}"
                                            alt="{{ $item['product']['name'] }}" class="img-fluid rounded">
                                    </div>
                                    <div class="col-md-4">
                                        <h6>{{ $item['product']['name'] }}</h6>
                                        <p class="text-muted small">{{ $item['product']['brand'] ?? '' }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="fw-bold">৳{{ number_format($item['unit_price'], 2) }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control form-control-sm cart-quantity-input"
                                            data-cart-item-id="{{ $item['id'] }}" value="{{ $item['quantity'] }}"
                                            min="1">
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <div class="fw-bold mb-2">৳{{ number_format($item['total_price'], 2) }}</div>
                                        <button class="btn btn-danger btn-sm remove-cart-item"
                                            data-cart-item-id="{{ $item['id'] }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span>৳{{ number_format($cart['subtotal'], 2) }}</span>
                            </div>

                            @if ($cart['discount_amount'] > 0)
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Discount:</span>
                                    <span class="text-success">-৳{{ number_format($cart['discount_amount'], 2) }}</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mb-3">
                                <span>Shipping:</span>
                                <span>{{ $cart['shipping_cost'] > 0 ? '৳' . number_format($cart['shipping_cost'], 2) : 'Free' }}</span>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong class="text-primary">৳{{ number_format($cart['total'], 2) }}</strong>
                            </div>

                            <a href="{{ route('shop.checkout') }}" class="btn btn-primary w-100 mb-3">
                                Proceed to Checkout
                            </a>

                            <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary w-100">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4>Your cart is empty</h4>
                <p class="text-muted">Start shopping to add items to your cart</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/modules/shop.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Shop.init();
        });
    </script>
@endpush
