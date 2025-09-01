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
                        <label class="form-label">Final Amount (৳) *</label>
                        <input type="number" class="form-control" name="final_amount"
                            value="{{ old('final_amount') }}" min="0" step="0.01">
                        @error('final_amount')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
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
                            <select class="form-select" name="room_type_id">
                                <option value="">Select Room Type</option>
                                @foreach ($roomTypes['data'] as $roomType)
                                    <option value="{{ $roomType['id'] }}"
                                        {{ old('room_type_id') == $roomType['id'] ? 'selected' : '' }}>
                                        {{ $roomType['name'] }} - ৳{{ number_format($roomType['base_daily_rate'], 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_type_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Spa-specific fields -->
                <div id="spa_fields" class="booking-fields" style="display: none;">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Spa Package *</label>
                            <select class="form-select" name="spa_package_id">
                                <option value="">Select Spa Package</option>
                                @foreach ($spaPackages as $spaPackage)
                                    <option value="{{ $spaPackage['id'] }}"
                                        {{ old('spa_package_id') == $spaPackage['id'] ? 'selected' : '' }}>
                                        {{ $spaPackage['name'] }} - ৳{{ number_format($spaPackage['price'], 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('spa_package_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Spay-specific fields -->
                <div id="spay_fields" class="booking-fields" style="display: none;">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Spay/Neuter Package *</label>
                            <select class="form-select" name="spay_package_id">
                                <option value="">Select Spay Package</option>
                                @foreach ($spayPackages as $spayPackage)
                                    <option value="{{ $spayPackage['id'] }}"
                                        {{ old('spay_package_id') == $spayPackage['id'] ? 'selected' : '' }}>
                                        {{ $spayPackage['name'] }} - ৳{{ number_format($spayPackage['price'], 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('spay_package_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
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
                                        <select class="form-select" name="addons[{{ $index }}][addon_service_id]">
                                            <option value="">Select Add-on Service</option>
                                            @foreach ($addonServices as $service)
                                                <option value="{{ $service['id'] }}"
                                                    data-price="{{ $service['price'] }}"
                                                    {{ $addon['addon_service_id'] == $service['id'] ? 'selected' : '' }}>
                                                    {{ $service['name'] }} - ৳{{ number_format($service['price'], 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control"
                                            name="addons[{{ $index }}][quantity]"
                                            value="{{ $addon['quantity'] }}" placeholder="Quantity" min="1"
                                            max="10">
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

@section('scripts')
    <script type="module">
        $(document).ready(function() {
            let addonIndex = {{ old('addons') ? count(old('addons')) : 0 }};

            // Handle booking type changes
            $('.booking-type').change(function() {
                $('.booking-fields').hide();
                if (this.checked) {
                    $('#' + this.value + '_fields').show();
                }
            });

            // Show the correct fields on page load
            $('.booking-type:checked').trigger('change');

            // Customer search functionality
            let searchTimeout;
            $('#customer_search').on('input', function() {
                const searchTerm = $(this).val().trim();

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    if (searchTerm.length >= 2) {
                        searchCustomers(searchTerm);
                    } else {
                        $('#customer_results').hide();
                    }
                }, 300);
            });

            // Hide dropdown when clicking outside
            $(document).click(function(e) {
                if (!$(e.target).closest('#customer_search, #customer_results').length) {
                    $('#customer_results').hide();
                }
            });

            // Date validation
            $('input[name="check_in_date"]').change(function() {
                const checkInDate = new Date($(this).val());
                const checkOutInput = $('input[name="check_out_date"]');
                const currentCheckOut = new Date(checkOutInput.val());

                // Set minimum check-out date to day after check-in
                const minCheckOut = new Date(checkInDate);
                minCheckOut.setDate(minCheckOut.getDate() + 1);
                checkOutInput.attr('min', minCheckOut.toISOString().split('T')[0]);

                // If current check-out is before new minimum, update it
                if (currentCheckOut <= checkInDate) {
                    checkOutInput.val(minCheckOut.toISOString().split('T')[0]);
                }
            });
        });

        function searchCustomers(searchTerm) {
            $.ajax({
                url: '{{ route('admin.customers.search') }}',
                method: 'GET',
                data: {
                    search: searchTerm
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '';
                        response.data.forEach(customer => {
                            html += `
                        <a href="#" class="dropdown-item" onclick="selectCustomer(${customer.id}, '${customer.name}', '${customer.email}', '${customer.phone || ''}')">
                            <div>
                                <strong>${customer.name}</strong><br>
                                <small class="text-muted">${customer.email}${customer.phone ? ' • ' + customer.phone : ''}</small>
                            </div>
                        </a>
                    `;
                        });
                        $('#customer_results').html(html).show();
                    } else {
                        $('#customer_results').html('<div class="dropdown-item-text">No customers found</div>')
                            .show();
                    }
                },
                error: function() {
                    $('#customer_results').html(
                        '<div class="dropdown-item-text text-danger">Error searching customers</div>')
                    .show();
                }
            });
        }

        function selectCustomer(id, name, email, phone) {
            $('#user_id').val(id);
            $('#customer_search').val(name);
            $('#customer_info').text(`${name} (${email}${phone ? ' • ' + phone : ''})`);
            $('#selected_customer').show();
            $('#customer_results').hide();
        }

        function clearCustomer() {
            $('#user_id').val('');
            $('#customer_search').val('');
            $('#selected_customer').hide();
        }

        function addAddonRow() {
            const html = `
        <div class="addon-row mb-3">
            <div class="row">
                <div class="col-md-6">
                    <select class="form-select" name="addons[${addonIndex}][addon_service_id]">
                        <option value="">Select Add-on Service</option>
                        @foreach ($addonServices as $service)
                            <option value="{{ $service['id'] }}" data-price="{{ $service['price'] }}">
                                {{ $service['name'] }} - ৳{{ number_format($service['price'], 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="addons[${addonIndex}][quantity]" 
                           placeholder="Quantity" min="1" max="10" value="1">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-danger" onclick="removeAddonRow(this)">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;

            $('#addons_container').append(html);
            $('#no_addons').hide();
            addonIndex++;
        }

        function removeAddonRow(button) {
            $(button).closest('.addon-row').remove();

            if ($('.addon-row').length === 0) {
                $('#no_addons').show();
            }
        }
    </script>
@endsection
