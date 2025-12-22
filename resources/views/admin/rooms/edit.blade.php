@extends('layouts.admin')

@section('title', 'Edit Room - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Edit Room {{ $room['room_number'] ?? 'N/A' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.index') }}">Rooms</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.show', $room['id']) }}">View</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.rooms.show', $room['id']) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Room Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.rooms.update', $room['id']) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="room_number" class="form-label">Room Number *</label>
                                    <input type="text" class="form-control @error('room_number') is-invalid @enderror" 
                                        id="room_number" name="room_number" value="{{ old('room_number', $room['room_number'] ?? '') }}" required>
                                    @error('room_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="floor" class="form-label">Floor *</label>
                                    <input type="number" class="form-control @error('floor') is-invalid @enderror" 
                                        id="floor" name="floor" value="{{ old('floor', $room['floor'] ?? '') }}" min="1" max="10" required>
                                    @error('floor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="room_type_id" class="form-label">Room Type *</label>
                            <select class="form-select @error('room_type_id') is-invalid @enderror" 
                                id="room_type_id" name="room_type_id" required>
                                <option value="">Select Room Type</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type['id'] }}" 
                                        {{ old('room_type_id', $room['room_type']['id'] ?? '') == $type['id'] ? 'selected' : '' }}>
                                        {{ $type['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                                <option value="available" {{ old('status', $room['status'] ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ old('status', $room['status'] ?? '') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="maintenance" {{ old('status', $room['status'] ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" name="notes" rows="3">{{ old('notes', $room['notes'] ?? '') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.rooms.show', $room['id']) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Room
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

