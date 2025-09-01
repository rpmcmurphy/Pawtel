@extends('layouts.admin')

@section('title', 'Edit Product - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Edit Product: {{ $product['name'] ?? 'Unknown Product' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">Edit Product</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.show', $product['id']) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>
    </div>
@endsection

@section('content')
    @include('admin.products._form', [
        'product' => $product,
        'method' => 'PUT',
        'action' => route('admin.products.update', $product['id']),
        'categories' => $categories
    ])
@endsection