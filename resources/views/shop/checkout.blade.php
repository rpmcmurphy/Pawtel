@extends('layouts.app')

@section('title', 'Checkout - Pawtel')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Checkout</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('shop.order.store') }}">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="shipping_address" class="form-label">Address</label>
                                    <textarea class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address"
                                        name="shipping_address" rows="3" required>{{ old('shipping_address') }}</textarea>
                                    @error('shipping_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="shipping_city" class="form-label">City</label>
                                    <input type="text" class="form-control @error('shipping_city') is-invalid @enderror"
                                        id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" required>
                                    @error('shipping_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="shipping_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('shipping_phone') is-invalid @enderror"
                                        id="shipping_phone" name="shipping_phone" value="{{ old('shipping_phone') }}"
                                        required>
                                    @error('shipping_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Method</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod"
                                    value="cod" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cod">
                                    <strong>Cash on Delivery</strong><br>
                                    <small class="text-muted">Pay when your order is delivered</small>
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer"
                                    value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                <label class="form-check-label" for="bank_transfer">
                                    <strong>Bank Transfer</strong><br>
                                    <small class="text-muted">Transfer to our bank account</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Notes (Optional)</h5>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" name="notes" rows="3" placeholder="Any special instructions for your order...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            @if (!empty($cart['items']))
                                @foreach ($cart['items'] as $item)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small">{{ $item['product']['name'] }} ×
                                            {{ $item['quantity'] }}</span>
                                        <span class="small">৳{{ number_format($item['total_price'], 2) }}</span>
                                    </div>
                                @endforeach

                                <hr>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>
