/**
 * Reusable Booking Form Module
 * Handles dynamic form fields, customer search, pricing calculation, and form interactions
 */
export class BookingForm {
    constructor(formId, options = {}) {
        this.form = document.getElementById(formId);
        this.options = {
            apiBaseUrl: options.apiBaseUrl || '/api',
            customerSearchUrl: options.customerSearchUrl || '/admin/customers/search',
            calculatePriceUrl: options.calculatePriceUrl || '/admin/bookings/calculate-price',
            ...options
        };
        this.addonIndex = options.initialAddonIndex || 0;
        this.selectedCustomer = null;
        this.addonServices = options.addonServices || [];
        
        if (this.form) {
            this.init();
        }
    }

    init() {
        this.setupEventListeners();
        this.initializeBookingType();
        this.initializeDateValidation();
        this.initializeAddonHandlers();
    }

    setupEventListeners() {
        // Booking type change
        const bookingTypeRadios = this.form.querySelectorAll('input[name="type"]');
        bookingTypeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => this.handleBookingTypeChange(e.target.value));
        });

        // Customer search
        const customerSearch = this.form.querySelector('#customer_search');
        if (customerSearch) {
            customerSearch.addEventListener('input', this.debounce((e) => this.searchCustomers(e.target.value), 300));
        }

        // Date changes trigger pricing calculation
        const dateInputs = this.form.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            input.addEventListener('change', () => this.calculatePricing());
        });

        // Room type/package selection triggers pricing
        const selectInputs = this.form.querySelectorAll('select[name*="_id"]');
        selectInputs.forEach(select => {
            select.addEventListener('change', () => this.calculatePricing());
        });

        // Custom monthly discount
        const customDiscount = this.form.querySelector('#custom_monthly_discount');
        if (customDiscount) {
            customDiscount.addEventListener('input', () => this.calculatePricing());
        }

        // Post care days
        const postCareDays = this.form.querySelector('#post_care_days');
        if (postCareDays) {
            postCareDays.addEventListener('change', () => this.calculatePricing());
        }

        // Is resident checkbox
        const isResident = this.form.querySelector('#is_resident');
        if (isResident) {
            isResident.addEventListener('change', () => this.calculatePricing());
        }

        // Final amount override
        const finalAmount = this.form.querySelector('#final_amount');
        if (finalAmount) {
            finalAmount.addEventListener('focus', () => {
                finalAmount.removeAttribute('readonly');
            });
        }
    }

    initializeBookingType() {
        const selectedType = this.form.querySelector('input[name="type"]:checked');
        if (selectedType) {
            this.handleBookingTypeChange(selectedType.value);
        } else {
            // Trigger change on first checked radio
            const firstChecked = this.form.querySelector('input[name="type"]:checked');
            if (firstChecked) {
                firstChecked.dispatchEvent(new Event('change'));
            }
        }
    }

    initializeDateValidation() {
        const checkInInput = this.form.querySelector('input[name="check_in_date"]');
        const checkOutInput = this.form.querySelector('input[name="check_out_date"]');

        if (checkInInput && checkOutInput) {
            checkInInput.addEventListener('change', () => {
                const checkInDate = new Date(checkInInput.value);
                const minCheckOut = new Date(checkInDate);
                minCheckOut.setDate(minCheckOut.getDate() + 1);
                checkOutInput.setAttribute('min', minCheckOut.toISOString().split('T')[0]);

                const currentCheckOut = new Date(checkOutInput.value);
                if (currentCheckOut <= checkInDate) {
                    checkOutInput.value = minCheckOut.toISOString().split('T')[0];
                }
                
                this.calculatePricing();
            });
        }
    }

    initializeAddonHandlers() {
        // Listen for addon changes
        this.form.addEventListener('change', (e) => {
            if (e.target.classList.contains('addon-service-select') || e.target.classList.contains('addon-quantity')) {
                this.calculatePricing();
            }
        });
    }

    handleBookingTypeChange(type) {
        // Hide all type-specific sections
        const sections = {
            hotel: this.form.querySelector('.hotel-fields, #hotel_fields'),
            spa: this.form.querySelector('.spa-fields, #spa_fields'),
            spay: this.form.querySelector('.spay-fields, #spay_fields')
        };

        // Hide all booking fields first
        this.form.querySelectorAll('.booking-fields').forEach(field => {
            field.style.display = 'none';
        });

        Object.values(sections).forEach(section => {
            if (section) section.style.display = 'none';
        });

        // Show relevant section
        if (sections[type]) {
            sections[type].style.display = 'block';
        }

        // Also show by ID if exists
        const targetField = this.form.querySelector(`#${type}_fields`);
        if (targetField) {
            targetField.style.display = 'block';
        }

        // Recalculate pricing
        this.calculatePricing();
    }

    async searchCustomers(query) {
        if (query.length < 2) {
            this.hideCustomerDropdown();
            return;
        }

        try {
            const response = await fetch(`${this.options.customerSearchUrl}?search=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            const data = await response.json();
            
            if (data.success && data.data) {
                this.displayCustomerResults(data.data);
            } else {
                this.hideCustomerDropdown();
            }
        } catch (error) {
            console.error('Customer search error:', error);
            this.hideCustomerDropdown();
        }
    }

    displayCustomerResults(customers) {
        const dropdown = this.getOrCreateCustomerDropdown();
        dropdown.innerHTML = '';

        if (customers.length === 0) {
            dropdown.innerHTML = '<div class="dropdown-item text-muted">No customers found</div>';
            dropdown.style.display = 'block';
            return;
        }

        customers.forEach(customer => {
            const item = document.createElement('a');
            item.className = 'dropdown-item';
            item.href = '#';
            item.style.cursor = 'pointer';
            item.innerHTML = `
                <div>
                    <strong>${customer.name}</strong><br>
                    <small class="text-muted">${customer.email}${customer.phone ? ' • ' + customer.phone : ''}</small>
                </div>
            `;
            item.addEventListener('click', (e) => {
                e.preventDefault();
                this.selectCustomer(customer);
            });
            dropdown.appendChild(item);
        });

        dropdown.style.display = 'block';
    }

    selectCustomer(customer) {
        this.selectedCustomer = customer;
        const searchInput = this.form.querySelector('#customer_search');
        const displayDiv = this.form.querySelector('#selected_customer');
        const hiddenInput = this.form.querySelector('input[name="user_id"]');
        
        if (searchInput) {
            searchInput.value = customer.name;
        }
        if (hiddenInput) {
            hiddenInput.value = customer.id;
        }

        if (displayDiv) {
            const customerInfo = this.form.querySelector('#customer_info');
            if (customerInfo) {
                customerInfo.textContent = `${customer.name} (${customer.email}${customer.phone ? ' • ' + customer.phone : ''})`;
            }
            displayDiv.style.display = 'block';
        }

        this.hideCustomerDropdown();
        this.calculatePricing();
    }

    clearCustomer() {
        this.selectedCustomer = null;
        const searchInput = this.form.querySelector('#customer_search');
        const displayDiv = this.form.querySelector('#selected_customer');
        const hiddenInput = this.form.querySelector('input[name="user_id"]');
        
        if (searchInput) searchInput.value = '';
        if (displayDiv) displayDiv.style.display = 'none';
        if (hiddenInput) hiddenInput.value = '';
        
        this.calculatePricing();
    }

    getOrCreateCustomerDropdown() {
        let dropdown = document.getElementById('customer_results');
        if (!dropdown) {
            dropdown = document.createElement('div');
            dropdown.id = 'customer_results';
            dropdown.className = 'dropdown-menu show';
            dropdown.style.position = 'absolute';
            dropdown.style.zIndex = '1000';
            dropdown.style.maxHeight = '300px';
            dropdown.style.overflowY = 'auto';
            dropdown.style.width = '100%';
            
            const searchInput = this.form.querySelector('#customer_search');
            if (searchInput && searchInput.parentElement) {
                searchInput.parentElement.style.position = 'relative';
                searchInput.parentElement.appendChild(dropdown);
            }
        }
        return dropdown;
    }

    hideCustomerDropdown() {
        const dropdown = document.getElementById('customer_results');
        if (dropdown) {
            dropdown.style.display = 'none';
        }
    }

    async calculatePricing() {
        const bookingType = this.form.querySelector('input[name="type"]:checked')?.value;
        if (!bookingType) {
            const finalAmountInput = this.form.querySelector('#final_amount');
            if (finalAmountInput) finalAmountInput.value = '';
            return;
        }

        const formData = new FormData(this.form);
        const bookingData = {
            type: bookingType,
            user_id: formData.get('user_id') || null,
            check_in_date: formData.get('check_in_date'),
            check_out_date: formData.get('check_out_date'),
            room_type_id: formData.get('room_type_id'),
            spa_package_id: formData.get('spa_package_id'),
            spay_package_id: formData.get('spay_package_id'),
            custom_monthly_discount: formData.get('custom_monthly_discount') || null,
            post_care_days: formData.get('post_care_days') || null,
            is_resident: formData.get('is_resident') === 'on' || formData.get('is_resident') === '1',
            addons: this.getAddonsData()
        };

        // Validate required fields based on type
        if (bookingType === 'hotel' && (!bookingData.room_type_id || !bookingData.check_in_date || !bookingData.check_out_date)) {
            return;
        }
        if (bookingType === 'spa' && !bookingData.spa_package_id) {
            return;
        }
        if (bookingType === 'spay' && !bookingData.spay_package_id) {
            return;
        }

        try {
            const response = await fetch(this.options.calculatePriceUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(bookingData)
            });

            const data = await response.json();
            
            if (data.success && data.data) {
                this.updatePricingDisplay(data.data, bookingType);
                
                // Check availability for hotel bookings
                if (bookingType === 'hotel' && bookingData.room_type_id && bookingData.check_in_date && bookingData.check_out_date) {
                    this.checkHotelAvailability(bookingData.room_type_id, bookingData.check_in_date, bookingData.check_out_date);
                }
            } else {
                console.error('Pricing calculation failed:', data.message);
            }
        } catch (error) {
            console.error('Pricing calculation error:', error);
            // Fallback to client-side calculation
            this.calculatePricingClientSide(bookingType);
        }
    }

    calculatePricingClientSide(bookingType) {
        // Fallback client-side calculation if API fails
        let basePrice = 0;
        let addonsTotal = 0;
        
        // Calculate addons total
        this.form.querySelectorAll('.addon-row').forEach(row => {
            const serviceSelect = row.querySelector('.addon-service-select');
            const quantityInput = row.querySelector('.addon-quantity');
            if (serviceSelect && quantityInput && serviceSelect.value && quantityInput.value) {
                const price = parseFloat(serviceSelect.options[serviceSelect.selectedIndex].dataset.price || 0);
                const quantity = parseInt(quantityInput.value) || 0;
                addonsTotal += price * quantity;
            }
        });
        
        if (bookingType === 'hotel') {
            const roomTypeSelect = this.form.querySelector('#room_type_id');
            const checkInDate = this.form.querySelector('input[name="check_in_date"]')?.value;
            const checkOutDate = this.form.querySelector('input[name="check_out_date"]')?.value;
            
            if (roomTypeSelect && roomTypeSelect.value && checkInDate && checkOutDate) {
                const checkIn = new Date(checkInDate);
                const checkOut = new Date(checkOutDate);
                const days = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24)) + 1;
                
                if (days >= 3) {
                    const option = roomTypeSelect.options[roomTypeSelect.selectedIndex];
                    const baseRate = parseFloat(option.dataset.baseRate || 0);
                    const rate7Plus = parseFloat(option.dataset.rate7plus || 0);
                    const rate10Plus = parseFloat(option.dataset.rate10plus || 0);
                    const monthlyPrice = parseFloat(option.dataset.monthlyPrice || 0);
                    
                    if (days >= 30 && monthlyPrice > 0) {
                        const customDiscount = parseFloat(this.form.querySelector('#custom_monthly_discount')?.value || 0);
                        basePrice = monthlyPrice - customDiscount;
                    } else if (days >= 10 && rate10Plus > 0) {
                        basePrice = rate10Plus * days;
                    } else if (days >= 7 && rate7Plus > 0) {
                        basePrice = rate7Plus * days;
                    } else {
                        basePrice = baseRate * days;
                    }
                }
            }
        } else if (bookingType === 'spa') {
            const spaPackageSelect = this.form.querySelector('#spa_package_id');
            if (spaPackageSelect && spaPackageSelect.value) {
                const option = spaPackageSelect.options[spaPackageSelect.selectedIndex];
                basePrice = parseFloat(option.dataset.price || 0);
            }
        } else if (bookingType === 'spay') {
            const spayPackageSelect = this.form.querySelector('#spay_package_id');
            if (spayPackageSelect && spayPackageSelect.value) {
                const option = spayPackageSelect.options[spayPackageSelect.selectedIndex];
                basePrice = parseFloat(option.dataset.price || 0);
            }
        }
        
        const totalAmount = basePrice + addonsTotal;
        const totalAmountInput = this.form.querySelector('#total_amount');
        const finalAmountInput = this.form.querySelector('#final_amount');
        
        if (totalAmountInput) totalAmountInput.value = totalAmount.toFixed(2);
        if (finalAmountInput) finalAmountInput.value = totalAmount.toFixed(2);
    }

    updatePricingDisplay(pricing, bookingType) {
        const subtotalEl = document.getElementById('pricing_subtotal');
        const discountEl = document.getElementById('pricing_discount');
        const totalEl = document.getElementById('pricing_total');
        const totalAmountInput = this.form.querySelector('#total_amount');
        const finalAmountInput = this.form.querySelector('#final_amount');

        if (subtotalEl) subtotalEl.textContent = `৳${(pricing.subtotal || pricing.total_amount || 0).toFixed(2)}`;
        if (discountEl) discountEl.textContent = `৳${(pricing.discount_amount || 0).toFixed(2)}`;
        if (totalEl) totalEl.textContent = `৳${(pricing.final_amount || 0).toFixed(2)}`;
        if (totalAmountInput) totalAmountInput.value = pricing.total_amount || pricing.final_amount || 0;
        if (finalAmountInput) finalAmountInput.value = pricing.final_amount || 0;

        // Update hotel pricing info if hotel booking
        if (bookingType === 'hotel') {
            this.updateHotelPricingInfo(pricing);
        }
    }

    updateHotelPricingInfo(pricing) {
        const infoDiv = document.getElementById('hotel_pricing_info');
        const detailsDiv = document.getElementById('pricing_details');
        
        if (!infoDiv || !detailsDiv) return;

        const days = pricing.total_days || 0;
        let pricingText = `Total Days: ${days}<br>`;
        
        if (days >= 30 && pricing.room_price) {
            pricingText += `Monthly Package: ৳${pricing.room_price.toFixed(2)}`;
            if (pricing.discount_amount > 0) {
                pricingText += ` - Discount: ৳${pricing.discount_amount.toFixed(2)}`;
            }
        } else {
            pricingText += `Room Price: ৳${(pricing.room_price || 0).toFixed(2)}`;
        }
        
        detailsDiv.innerHTML = pricingText;
        infoDiv.style.display = 'block';
    }

    async checkHotelAvailability(roomTypeId, checkInDate, checkOutDate) {
        try {
            const response = await fetch(`/api/availability/check?room_type_id=${roomTypeId}&check_in_date=${checkInDate}&check_out_date=${checkOutDate}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            const statusDiv = document.getElementById('hotel_availability_status');
            
            if (!statusDiv) return;

            if (data.success && data.data && data.data.available) {
                statusDiv.innerHTML = '<div class="text-success">✓ Available</div>';
                statusDiv.className = 'alert alert-success';
            } else {
                statusDiv.innerHTML = '<div class="text-danger">✗ No vacancy available for selected dates</div>';
                statusDiv.className = 'alert alert-danger';
            }
            statusDiv.style.display = 'block';
        } catch (error) {
            console.error('Availability check error:', error);
        }
    }

    getAddonsData() {
        const addons = [];
        const addonRows = this.form.querySelectorAll('.addon-row');
        
        addonRows.forEach(row => {
            const serviceSelect = row.querySelector('.addon-service-select, select[name*="[addon_service_id]"]');
            const quantityInput = row.querySelector('.addon-quantity, input[name*="[quantity]"]');
            
            if (serviceSelect && quantityInput && serviceSelect.value && quantityInput.value) {
                addons.push({
                    addon_service_id: parseInt(serviceSelect.value),
                    quantity: parseInt(quantityInput.value) || 1
                });
            }
        });

        return addons;
    }

    addAddonRow(addonService = null) {
        const container = this.form.querySelector('#addons_container');
        if (!container) return;

        const row = document.createElement('div');
        row.className = 'addon-row mb-3';
        row.dataset.index = this.addonIndex++;

        let optionsHtml = '<option value="">Select Add-on Service</option>';
        this.addonServices.forEach(service => {
            const selected = addonService && service.id === addonService.id ? 'selected' : '';
            optionsHtml += `<option value="${service.id}" data-price="${service.price || 0}" ${selected}>${service.name} - ৳${parseFloat(service.price || 0).toFixed(2)}</option>`;
        });

        row.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <select class="form-select addon-service-select" name="addons[${row.dataset.index}][addon_service_id]">
                        ${optionsHtml}
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control addon-quantity" name="addons[${row.dataset.index}][quantity]" 
                           placeholder="Quantity" min="1" max="10" value="1">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-danger" onclick="window.bookingFormInstance.removeAddonRow(this)">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;

        container.appendChild(row);

        const noAddons = document.getElementById('no_addons');
        if (noAddons) {
            noAddons.style.display = 'none';
        }

        this.calculatePricing();
    }

    removeAddonRow(button) {
        const row = button.closest('.addon-row');
        if (row) {
            row.remove();
            
            if (this.form.querySelectorAll('.addon-row').length === 0) {
                const noAddons = document.getElementById('no_addons');
                if (noAddons) {
                    noAddons.style.display = 'block';
                }
            }
            
            this.calculatePricing();
        }
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Make available globally for inline scripts
window.BookingForm = BookingForm;

// Global helper functions for backward compatibility
window.selectCustomer = function(id, name, email, phone) {
    if (window.bookingFormInstance) {
        window.bookingFormInstance.selectCustomer({ id, name, email, phone });
    }
};

window.clearCustomer = function() {
    if (window.bookingFormInstance) {
        window.bookingFormInstance.clearCustomer();
    }
};

window.addAddonRow = function() {
    if (window.bookingFormInstance) {
        window.bookingFormInstance.addAddonRow();
    }
};

window.removeAddonRow = function(button) {
    if (window.bookingFormInstance) {
        window.bookingFormInstance.removeAddonRow(button);
    }
};
