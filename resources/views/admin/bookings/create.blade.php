@extends('layouts.admin')

@section('title', 'Create Manual Booking - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Create Manual Booking</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                    <li class="breadcrumb-item active">Create Booking</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.bookings.store') }}" id="manualBookingForm">
        @csrf

        <!-- Customer Selection -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user"></i> Customer Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="customer_search" class="form-label">Search Customer *</label>
                        <input type="text" class="form-control" id="customer_search"
                            placeholder="Type customer name, email or phone..." autocomplete="off">
                        <div id="customer_results" class="dropdown-menu w-100" style="display: none;"></div>
                        <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}">
                        <div id="selected_customer" class="mt-2" style="display: none;">
                            <div class="alert alert-info">
                                <strong>Selected Customer:</strong> <span id="customer_info"></span>
                                <button type="button" class="btn-close float-end" onclick="clearCustomer()"></button>
                            </div>
                        </div>
                        @error('user_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Type Selection -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list"></i> Booking Type
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input booking-type" type="radio" name="type" value="hotel"
                                id="typeHotel" {{ old('type') == 'hotel' ? 'checked' : '' }}>
                            <label class="form-check-label" for="typeHotel">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-hotel fa-2x text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Hotel Booking</h6>
                                        <small class="text-muted">Pet boarding service</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input booking-type" type="radio" name="type" value="spa"
                                id="typeSpa" {{ old('type') == 'spa' ? 'checked' : '' }}>
                            <label class="form-check-label" for="typeSpa">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-spa fa-2x text-success me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Spa Booking</h6>
                                        <small class="text-muted">Pet grooming & spa</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input booking-type" type="radio" name="type" value="spay"
                                id="typeSpay" {{ old('type') == 'spay' ? 'checked' : '' }}>
                            <label class="form-check-label" for="typeSpay">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-md fa-2x text-warning me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Spay/Neuter</h6>
                                        <small class="text-muted">Medical procedure</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                @error('type')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Booking Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt"></i> Booking Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Check-in Date *</label>
                        <input type="date" class="form-control" name="check_in_date"
                            value="{{ old('check_in_date') }}" min="{{ date('Y-m-d') }}">
                        @error('check_in_date')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Check-out Date *</label>
                        <input type="date" class="form-control" name="check_out_date"
                            value="{{ old('check_out_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        @error('check_out_date')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Final Amount (৳)</label>
                        <input type="number" class="form-control" name="final_amount"
                            id="final_amount" value="{{ old('final_amount') }}" min="0" step="0.01" readonly>
                        <small class="text-muted">Calculated automatically. You can override if needed.</small>
                        @error('final_amount')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3" style="display: none;" id="total_amount_field">
                        <label class="form-label">Total Amount (৳)</label>
                        <input type="number" class="form-control" name="total_amount"
                            id="total_amount" value="{{ old('total_amount') }}" min="0" step="0.01" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Manual Reference *</label>
                        <input type="text" class="form-control" name="manual_reference"
                            value="{{ old('manual_reference') }}" placeholder="e.g., PHONE-001">
                        @error('manual_reference')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Hotel-specific fields -->
                <div id="hotel_fields" class="booking-fields" style="display: none;">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Room Type *</label>
                            <select class="form-select" name="room_type_id" id="room_type_id" required>
                                <option value="">Select Room Type</option>
                                @if(isset($roomTypes['data']) && is_array($roomTypes['data']))
                                    @foreach ($roomTypes['data'] as $roomType)
                                        <option value="{{ $roomType['id'] }}"
                                            data-base-rate="{{ $roomType['base_daily_rate'] ?? 0 }}"
                                            data-rate-7plus="{{ $roomType['rate_7plus_days'] ?? 0 }}"
                                            data-rate-10plus="{{ $roomType['rate_10plus_days'] ?? 0 }}"
                                            data-monthly-price="{{ $roomType['monthly_package_price'] ?? 0 }}"
                                            {{ old('room_type_id') == $roomType['id'] ? 'selected' : '' }}>
                                            {{ $roomType['name'] }} - ৳{{ number_format($roomType['base_daily_rate'] ?? 0, 2) }}/day
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('room_type_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Custom Monthly Discount (৳)</label>
                            <input type="number" class="form-control" name="custom_monthly_discount" 
                                   id="custom_monthly_discount" value="{{ old('custom_monthly_discount') }}" 
                                   min="0" step="0.01" placeholder="Optional discount for monthly stays">
                            <small class="text-muted">Only applies to monthly stays (30+ days)</small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div id="hotel_pricing_info" class="alert alert-info" style="display: none;">
                                <strong>Pricing Information:</strong>
                                <div id="pricing_details"></div>
                            </div>
                            <div id="hotel_availability_status" class="alert" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <!-- Spa-specific fields -->
                <div id="spa_fields" class="booking-fields" style="display: none;">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Spa Package *</label>
                            <select class="form-select" name="spa_package_id" id="spa_package_id" required>
                                <option value="">Select Spa Package</option>
                                @if(isset($spaPackages['data']) && is_array($spaPackages['data']))
                                    @foreach ($spaPackages['data'] as $spaPackage)
                                        <option value="{{ $spaPackage['id'] }}"
                                            data-price="{{ $spaPackage['price'] ?? 0 }}"
                                            data-resident-price="{{ $spaPackage['resident_price'] ?? 0 }}"
                                            {{ old('spa_package_id') == $spaPackage['id'] ? 'selected' : '' }}>
                                            {{ $spaPackage['name'] }} - ৳{{ number_format($spaPackage['price'] ?? 0, 2) }}
                                        </option>
                                    @endforeach
                                @elseif(is_array($spaPackages))
                                    @foreach ($spaPackages as $spaPackage)
                                        <option value="{{ $spaPackage['id'] }}"
                                            data-price="{{ $spaPackage['price'] ?? 0 }}"
                                            data-resident-price="{{ $spaPackage['resident_price'] ?? 0 }}"
                                            {{ old('spa_package_id') == $spaPackage['id'] ? 'selected' : '' }}>
                                            {{ $spaPackage['name'] }} - ৳{{ number_format($spaPackage['price'] ?? 0, 2) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('spa_package_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Appointment Time</label>
                            <input type="time" class="form-control" name="appointment_time" 
                                   id="appointment_time" value="{{ old('appointment_time', '09:00') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2" 
                                      placeholder="Additional notes for spa booking">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Spay-specific fields -->
                <div id="spay_fields" class="booking-fields" style="display: none;">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Spay/Neuter Package *</label>
                            <select class="form-select" name="spay_package_id" id="spay_package_id" required>
                                <option value="">Select Spay Package</option>
                                @if(isset($spayPackages['data']) && is_array($spayPackages['data']))
                                    @foreach ($spayPackages['data'] as $spayPackage)
                                        <option value="{{ $spayPackage['id'] }}"
                                            data-price="{{ $spayPackage['price'] ?? 0 }}"
                                            data-resident-price="{{ $spayPackage['resident_price'] ?? 0 }}"
                                            data-post-care-days="{{ $spayPackage['post_care_days'] ?? 0 }}"
                                            {{ old('spay_package_id') == $spayPackage['id'] ? 'selected' : '' }}>
                                            {{ $spayPackage['name'] }} ({{ ucfirst($spayPackage['type'] ?? '') }}) - ৳{{ number_format($spayPackage['price'] ?? 0, 2) }}
                                        </option>
                                    @endforeach
                                @elseif(is_array($spayPackages))
                                    @foreach ($spayPackages as $spayPackage)
                                        <option value="{{ $spaPackage['id'] }}"
                                            data-price="{{ $spayPackage['price'] ?? 0 }}"
                                            data-resident-price="{{ $spayPackage['resident_price'] ?? 0 }}"
                                            data-post-care-days="{{ $spayPackage['post_care_days'] ?? 0 }}"
                                            {{ old('spay_package_id') == $spayPackage['id'] ? 'selected' : '' }}>
                                            {{ $spayPackage['name'] }} ({{ ucfirst($spayPackage['type'] ?? '') }}) - ৳{{ number_format($spayPackage['price'] ?? 0, 2) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('spay_package_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Post-Care Days</label>
                            <input type="number" class="form-control" name="post_care_days" 
                                   id="post_care_days" value="{{ old('post_care_days') }}" 
                                   min="0" max="30" placeholder="Auto from package">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Pet Name</label>
                            <input type="text" class="form-control" name="pet_name" 
                                   value="{{ old('pet_name') }}" placeholder="Pet's name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pet Age</label>
                            <input type="text" class="form-control" name="pet_age" 
                                   value="{{ old('pet_age') }}" placeholder="e.g., 2 years">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pet Weight (kg)</label>
                            <input type="number" class="form-control" name="pet_weight" 
                                   value="{{ old('pet_weight') }}" step="0.1" min="0.1" max="50" placeholder="Weight">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Medical Notes</label>
                            <textarea class="form-control" name="medical_notes" rows="2" 
                                      placeholder="Any medical notes or special instructions">{{ old('medical_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label">Special Requests</label>
                        <textarea class="form-control" name="special_requests" rows="3"
                            placeholder="Any special requirements or notes">{{ old('special_requests') }}</textarea>
                        @error('special_requests')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Add-ons -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus-circle"></i> Add-on Services (Optional)
                </h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAddonRow()">
                    <i class="fas fa-plus"></i> Add Service
                </button>
            </div>
            <div class="card-body">
                <div id="addons_container">
                    @if (old('addons'))
                        @foreach (old('addons') as $index => $addon)
                            <div class="addon-row mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-select addon-service-select" name="addons[{{ $index }}][addon_service_id]">
                                            <option value="">Select Add-on Service</option>
                                            @php
                                                $services = isset($addonServices['data']) && is_array($addonServices['data']) 
                                                    ? $addonServices['data'] 
                                                    : (is_array($addonServices) ? $addonServices : []);
                                            @endphp
                                            @foreach ($services as $service)
                                                <option value="{{ $service['id'] }}"
                                                    data-price="{{ $service['price'] ?? 0 }}"
                                                    {{ $addon['addon_service_id'] == $service['id'] ? 'selected' : '' }}>
                                                    {{ $service['name'] }} - ৳{{ number_format($service['price'] ?? 0, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control addon-quantity" 
                                            name="addons[{{ $index }}][quantity]"
                                            value="{{ $addon['quantity'] }}" placeholder="Quantity" min="1" max="10">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="removeAddonRow(this)">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div id="no_addons" class="text-muted text-center py-3"
                    style="{{ old('addons') ? 'display: none;' : '' }}">
                    No add-on services selected. Click "Add Service" to add services.
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Booking
                    </button>
                    <button type="submit" name="send_confirmation" value="1" class="btn btn-success">
                        <i class="fas fa-envelope"></i> Create & Send Confirmation
                    </button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            // Bootstrap is now available globally via admin.js
            const {
                Modal,
                Dropdown,
                Tooltip
            } = window.bootstrap || {};
            
            // Initialize BookingForm if the form exists
            if (document.getElementById('manualBookingForm') && window.BookingForm) {
                @php
                    $services = isset($addonServices['data']) && is_array($addonServices['data']) 
                        ? $addonServices['data'] 
                        : (is_array($addonServices) ? $addonServices : []);
                @endphp
                
                window.bookingFormInstance = new window.BookingForm('manualBookingForm', {
                    customerSearchUrl: '{{ route("admin.customers.search") }}',
                    calculatePriceUrl: '{{ route("admin.bookings.calculate-price") }}',
                    addonServices: @json($services),
                    initialAddonIndex: {{ old('addons') ? count(old('addons')) : 0 }}
                });
            }

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#customer_search') && !e.target.closest('#customer_results')) {
                    const dropdown = document.getElementById('customer_results');
                    if (dropdown) {
                        dropdown.style.display = 'none';
                    }
                }
            });
        });
    </script>
@endpush
