@extends('layouts.app')

@section('title', 'Book Spa Service - Pawtel')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="booking-progress mb-4">
                    <div class="progress-steps">
                        <div class="step completed">1. Select Package</div>
                        <div class="step active">2. Booking Details</div>
                        <div class="step">3. Confirmation</div>
                    </div>
                </div>

                <div class="booking-form-card">
                    <h3 class="mb-4">Book Spa Service</h3>

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="spaBookingForm" action="{{ route('booking.spa.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Package Selection -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-spa text-primary me-2"></i>Select Spa Package
                            </h5>
                            <div class="row">
                                @forelse($packages as $package)
                                    <div class="col-md-6 mb-3">
                                        <div class="package-card border rounded p-3 {{ old('spa_package_id') == $package['id'] ? 'border-primary bg-light' : '' }}" style="cursor: pointer;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="spa_package_id"
                                                    value="{{ $package['id'] }}" id="package_{{ $package['id'] }}"
                                                    {{ old('spa_package_id') == $package['id'] ? 'checked' : '' }} required>
                                                <label class="form-check-label w-100" for="package_{{ $package['id'] }}">
                                                    <h6 class="mb-2">{{ $package['name'] }}</h6>
                                                    <p class="text-muted small mb-2">{{ $package['description'] }}</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-info">{{ $package['duration_minutes'] }}
                                                            min</span>
                                                        <strong
                                                            class="text-primary">৳{{ number_format($package['price'], 2) }}</strong>
                                                    </div>
                                                    @if (!empty($package['resident_price']))
                                                        <small class="text-muted d-block mt-2">
                                                            Resident Rate:
                                                            ৳{{ number_format($package['resident_price'], 2) }}
                                                        </small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            No spa packages available at this time. Please check back later.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Appointment Date & Time -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Appointment Details
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="appointment_date" class="form-label">Appointment Date *</label>
                                    <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                                        min="{{ date('Y-m-d') }}" value="{{ old('appointment_date') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="appointment_time" class="form-label">Appointment Time *</label>
                                    <select class="form-select" id="appointment_time" name="appointment_time" required>
                                        <option value="">Select Time</option>
                                        <option value="09:00" {{ old('appointment_time') == '09:00' ? 'selected' : '' }}>
                                            09:00 AM</option>
                                        <option value="10:00" {{ old('appointment_time') == '10:00' ? 'selected' : '' }}>
                                            10:00 AM</option>
                                        <option value="11:00" {{ old('appointment_time') == '11:00' ? 'selected' : '' }}>
                                            11:00 AM</option>
                                        <option value="12:00" {{ old('appointment_time') == '12:00' ? 'selected' : '' }}>
                                            12:00 PM</option>
                                        <option value="13:00" {{ old('appointment_time') == '13:00' ? 'selected' : '' }}>
                                            01:00 PM</option>
                                        <option value="14:00" {{ old('appointment_time') == '14:00' ? 'selected' : '' }}>
                                            02:00 PM</option>
                                        <option value="15:00" {{ old('appointment_time') == '15:00' ? 'selected' : '' }}>
                                            03:00 PM</option>
                                        <option value="16:00" {{ old('appointment_time') == '16:00' ? 'selected' : '' }}>
                                            04:00 PM</option>
                                    </select>
                                </div>
                            </div>
                            <div id="availabilityMessage" class="mt-2"></div>
                        </div>

                        <!-- Additional Services -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-plus-circle text-primary me-2"></i>Additional Services
                            </h5>
                            <div id="addonServices" class="addon-services">
                                @if (!empty($addonServices))
                                    @foreach ($addonServices as $addon)
                                        <div class="form-check mb-3 p-3 border rounded">
                                            <input class="form-check-input addon-checkbox" type="checkbox" name="addons[]"
                                                value="{{ $addon->id }}" id="addon_{{ $addon->id }}"
                                                data-price="{{ $addon->price }}">
                                            <label class="form-check-label w-100" for="addon_{{ $addon->id }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $addon->name }}</strong>
                                                        @if ($addon->description)
                                                            <br><small class="text-muted">{{ $addon->description }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="text-end">
                                                        <span
                                                            class="badge bg-primary">৳{{ number_format($addon->price, 2) }}</span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No additional services available at this time.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Special Requests -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-comment-alt text-primary me-2"></i>Special Requests
                            </h5>
                            <textarea class="form-control" name="special_requests" rows="3"
                                placeholder="Any special requirements or notes for your cat's spa session...">{{ old('special_requests') }}</textarea>
                        </div>

                        <!-- Documents -->
                        <div class="section mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-file-upload text-primary me-2"></i>Documents (Optional)
                            </h5>
                            <p class="text-muted small mb-3">You can upload documents after booking confirmation.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="vaccination_certificate" class="form-label">Vaccination
                                        Certificate</label>
                                    <input type="file" class="form-control" id="vaccination_certificate"
                                        name="vaccination_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label for="medical_records" class="form-label">Medical Records</label>
                                    <input type="file" class="form-control" id="medical_records"
                                        name="medical_records" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="booking-total mb-4">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-calculator text-primary me-2"></i>Price Breakdown
                            </h5>
                            <div class="total-breakdown bg-light p-3 rounded">
                                <div id="priceBreakdown">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Spa Package:</span>
                                        <strong id="packagePrice">৳0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Additional Services:</span>
                                        <strong id="addonCharges">৳0.00</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span class="h5 mb-0">Total Amount:</span>
                                        <strong class="h5 mb-0 text-primary" id="totalAmount">৳0.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-check me-2"></i>
                            Confirm Booking
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="booking-summary-sidebar bg-light p-4 rounded mb-4">
                    <h4 class="mb-3">Booking Summary</h4>
                    <div id="sidebarSummary">
                        <p class="text-muted">Select a package and date to see summary.</p>
                    </div>
                </div>

                <div class="info-card bg-light p-4 rounded">
                    <h5 class="mb-3">Need Help?</h5>
                    <p class="small mb-2">Our team is here to assist you.</p>
                    <a href="tel:+8801733191556" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-phone me-2"></i>Call Us
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .booking-progress {
            margin-bottom: 2rem;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .step {
            flex: 1;
            padding: 0.75rem;
            text-align: center;
            background: #e9ecef;
            color: #6c757d;
            border-radius: 0.5rem;
            margin: 0 0.25rem;
            font-weight: 500;
        }

        .step.completed {
            background: #28a745;
            color: white;
        }

        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .booking-form-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color: #333;
            font-weight: 600;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }

        .package-card:hover {
            border-color: #667eea !important;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
        }

        .package-card.border-primary {
            border-width: 2px !important;
        }
    </style>
@endpush

@push('scripts')
    <script type="module">
        $(document).ready(function() {
            const packages = @json($packages ?? []);
            const addonServices = @json($addonServices ?? []);

            // Get package from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const packageId = urlParams.get('package');
            if (packageId) {
                $('#package_' + packageId).prop('checked', true).trigger('change');
                selectPackage(packageId);
            }

            // Calculate initial price
            calculatePrice();

            // Update price when package or addons change
            $('input[name="spa_package_id"]').on('change', function() {
                selectPackage($(this).val());
                calculatePrice();
            });

            $('.addon-checkbox').on('change', function() {
                calculatePrice();
            });

            // Check availability when date/time changes
            $('#appointment_date, #appointment_time').on('change', function() {
                checkAvailability();
            });

            function selectPackage(packageId) {
                $('.package-card').removeClass('border-primary bg-light');
                $('.package-card').has(`#package_${packageId}`).addClass('border-primary bg-light');
                calculatePrice();
            }

            function calculatePrice() {
                const selectedPackage = $('input[name="spa_package_id"]:checked');
                if (!selectedPackage.length) {
                    $('#packagePrice').text('৳0.00');
                    $('#totalAmount').text('৳0.00');
                    return;
                }

                const packageId = selectedPackage.val();
                const pickedPackage = packages.find(p => p.id == packageId);
                if (!pickedPackage) return;

                // Check if user is resident (simplified - would need API call)
                const isResident = false; // TODO: Check from API
                const packagePrice = isResident && pickedPackage.resident_price ? parseFloat(pickedPackage.resident_price) :
                    parseFloat(pickedPackage.price);

                // Calculate addon charges
                let addonCharges = 0;
                $('.addon-checkbox:checked').each(function() {
                    const price = parseFloat($(this).data('price') || 0);
                    addonCharges += price;
                });

                const total = packagePrice + addonCharges;

                // Update display
                $('#packagePrice').text('৳' + packagePrice.toFixed(2));
                $('#addonCharges').text('৳' + addonCharges.toFixed(2));
                $('#totalAmount').text('৳' + total.toFixed(2));

                // Update sidebar
                updateSidebar(pickedPackage);
            }

            function updateSidebar(passedPackage) {
                const date = $('#appointment_date').val();
                const time = $('#appointment_time').val();

                let html = `
                    <div class="mb-3">
                        <strong>Package:</strong> ${passedPackage.name}
                    </div>
                `;

                if (date) {
                    html +=
                        `<div class="mb-3"><strong>Date:</strong><br>${new Date(date).toLocaleDateString()}</div>`;
                }
                if (time) {
                    html += `<div class="mb-3"><strong>Time:</strong> ${time}</div>`;
                }

                $('#sidebarSummary').html(html);
            }

            function checkAvailability() {
                const date = $('#appointment_date').val();
                const time = $('#appointment_time').val();
                const packageId = $('input[name="spa_package_id"]:checked').val();

                if (!date || !time || !packageId) {
                    $('#availabilityMessage').html('');
                    return;
                }

                // TODO: Make API call to check availability
                $('#availabilityMessage').html(
                    '<small class="text-info"><i class="fas fa-info-circle"></i> Checking availability...</small>'
                    );
            }

            // Form submission
            $('#spaBookingForm').on('submit', function(e) {
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
            });
        });

        window.selectPackage = function(packageId) {
            $(`#package_${packageId}`).prop('checked', true).trigger('change');
        };
    </script>
@endpush
