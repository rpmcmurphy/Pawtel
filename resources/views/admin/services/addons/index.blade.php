@extends('layouts.admin')

@section('title', 'Addon Services - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Addon Services</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Addon Services</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.services.addons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Service
        </a>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @php $servicesList = is_array($services) && isset($services['data']) ? $services['data'] : (is_array($services) ? $services : []); @endphp
            @if(count($servicesList) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Unit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($servicesList as $service)
                                <tr>
                                    <td><strong>{{ $service['name'] ?? 'N/A' }}</strong></td>
                                    <td><span class="badge bg-secondary">{{ $service['category'] ?? 'N/A' }}</span></td>
                                    <td>৳{{ number_format($service['price'] ?? 0, 2) }}</td>
                                    <td>{{ $service['unit'] ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.services.addons.edit', $service['id']) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">No addon services found.</div>
            @endif
        </div>
    </div>
@endsection

