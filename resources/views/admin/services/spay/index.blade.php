@extends('layouts.admin')

@section('title', 'Spay/Neuter Packages - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Spay/Neuter Packages</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Spay Packages</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.services.spay.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Package
        </a>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @php $packagesList = is_array($packages) && isset($packages['data']) ? $packages['data'] : (is_array($packages) ? $packages : []); @endphp
            @if(count($packagesList) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Resident Price</th>
                                <th>Post Care Days</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packagesList as $package)
                                <tr>
                                    <td><strong>{{ $package['name'] ?? 'N/A' }}</strong></td>
                                    <td><span class="badge bg-info">{{ ucfirst($package['type'] ?? 'N/A') }}</span></td>
                                    <td>৳{{ number_format($package['price'] ?? 0, 2) }}</td>
                                    <td>৳{{ number_format($package['resident_price'] ?? 0, 2) }}</td>
                                    <td>{{ $package['post_care_days'] ?? 0 }}</td>
                                    <td>
                                        <a href="{{ route('admin.services.spay.edit', $package['id']) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">No spay packages found.</div>
            @endif
        </div>
    </div>
@endsection

