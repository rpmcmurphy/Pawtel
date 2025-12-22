@extends('layouts.admin')

@section('title', 'Rooms Management - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Rooms Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Rooms</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Room
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.rooms.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Room Type</label>
                    <select name="room_type_id" class="form-select">
                        <option value="">All Types</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type['id'] }}" {{ request('room_type_id') == $type['id'] ? 'selected' : '' }}>
                                {{ $type['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Floor</label>
                    <input type="number" name="floor" class="form-control" min="1" max="10" value="{{ request('floor') }}" placeholder="Floor number">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Rooms List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Rooms List</h5>
        </div>
        <div class="card-body">
            @php
                $roomsList = is_array($rooms) && isset($rooms['data']) ? $rooms['data'] : (is_array($rooms) ? $rooms : []);
            @endphp
            @if(count($roomsList) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Room Number</th>
                                <th>Type</th>
                                <th>Floor</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roomsList as $room)
                                <tr>
                                    <td><strong>{{ $room['room_number'] ?? 'N/A' }}</strong></td>
                                    <td>{{ $room['room_type']['name'] ?? 'N/A' }}</td>
                                    <td>{{ $room['floor'] ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            ($room['status'] ?? '') == 'available' ? 'success' : 
                                            (($room['status'] ?? '') == 'occupied' ? 'warning' : 'danger') 
                                        }}">
                                            {{ ucfirst($room['status'] ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($room['notes'] ?? '', 50) }}</td>
                                    <td>
                                        <a href="{{ route('admin.rooms.show', $room['id']) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.rooms.edit', $room['id']) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No rooms found.
                </div>
            @endif
        </div>
    </div>
@endsection

