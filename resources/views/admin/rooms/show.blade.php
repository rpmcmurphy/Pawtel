@extends('layouts.admin')

@section('title', 'Room Details - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Room {{ $room['room_number'] ?? 'N/A' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.index') }}">Rooms</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.rooms.edit', $room['id']) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Room Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Room Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Room Number:</strong> {{ $room['room_number'] ?? 'N/A' }}</p>
                            <p><strong>Room Type:</strong> {{ $room['room_type']['name'] ?? 'N/A' }}</p>
                            <p><strong>Floor:</strong> {{ $room['floor'] ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ 
                                    ($room['status'] ?? '') == 'available' ? 'success' : 
                                    (($room['status'] ?? '') == 'occupied' ? 'warning' : 'danger') 
                                }}">
                                    {{ ucfirst($room['status'] ?? 'N/A') }}
                                </span>
                            </p>
                            <p><strong>Created:</strong> {{ isset($room['created_at']) ? \Carbon\Carbon::parse($room['created_at'])->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    @if(isset($room['notes']) && $room['notes'])
                        <hr>
                        <p><strong>Notes:</strong></p>
                        <p>{{ $room['notes'] }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-md-4">
            <!-- Update Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Update Status</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.rooms.updateStatus', $room['id']) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <select name="status" class="form-select" required>
                                <option value="available" {{ ($room['status'] ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ ($room['status'] ?? '') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="maintenance" {{ ($room['status'] ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <textarea name="notes" class="form-control" rows="3" placeholder="Status notes...">{{ $room['notes'] ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Block Dates -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Block Dates</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.rooms.block-dates') }}">
                        @csrf
                        <input type="hidden" name="room_type_id" value="{{ $room['room_type']['id'] ?? '' }}">
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <input type="text" name="reason" class="form-control" required placeholder="Reason for blocking...">
                        </div>
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-ban"></i> Block Dates
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

