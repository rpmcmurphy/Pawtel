/**
 * Pricing Calculator Module
 * Handles pricing calculations for bookings
 */
export class PricingCalculator {
    constructor(options = {}) {
        this.options = {
            apiBaseUrl: options.apiBaseUrl || '/api',
            ...options
        };
    }

    /**
     * Calculate hotel booking pricing
     */
    async calculateHotelBooking(roomTypeId, checkInDate, checkOutDate, addons = [], customMonthlyDiscount = null) {
        const days = this.calculateDays(checkInDate, checkOutDate);
        
        try {
            const response = await fetch(`${this.options.apiBaseUrl}/admin/bookings/calculate-price`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    type: 'hotel',
                    room_type_id: roomTypeId,
                    check_in_date: checkInDate,
                    check_out_date: checkOutDate,
                    custom_monthly_discount: customMonthlyDiscount,
                    addons: addons
                })
            });

            const data = await response.json();
            return data.success ? data.data : null;
        } catch (error) {
            console.error('Pricing calculation error:', error);
            return null;
        }
    }

    /**
     * Calculate spa booking pricing
     */
    async calculateSpaBooking(spaPackageId, addons = [], isResident = false) {
        try {
            const response = await fetch(`${this.options.apiBaseUrl}/admin/bookings/calculate-price`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    type: 'spa',
                    spa_package_id: spaPackageId,
                    is_resident: isResident,
                    addons: addons
                })
            });

            const data = await response.json();
            return data.success ? data.data : null;
        } catch (error) {
            console.error('Pricing calculation error:', error);
            return null;
        }
    }

    /**
     * Calculate spay/neuter booking pricing
     */
    async calculateSpayBooking(spayPackageId, addons = [], isResident = false, postCareDays = 0) {
        try {
            const response = await fetch(`${this.options.apiBaseUrl}/admin/bookings/calculate-price`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    type: 'spay',
                    spay_package_id: spayPackageId,
                    is_resident: isResident,
                    post_care_days: postCareDays,
                    addons: addons
                })
            });

            const data = await response.json();
            return data.success ? data.data : null;
        } catch (error) {
            console.error('Pricing calculation error:', error);
            return null;
        }
    }

    /**
     * Calculate number of days between two dates
     */
    calculateDays(checkInDate, checkOutDate) {
        const checkIn = new Date(checkInDate);
        const checkOut = new Date(checkOutDate);
        const diffTime = Math.abs(checkOut - checkIn);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays || 1;
    }

    /**
     * Format price for display
     */
    formatPrice(amount) {
        return `৳${parseFloat(amount || 0).toFixed(2)}`;
    }

    /**
     * Calculate addon total
     */
    calculateAddonTotal(addons) {
        return addons.reduce((total, addon) => {
            return total + (addon.price * addon.quantity);
        }, 0);
    }
}

// Make available globally
window.PricingCalculator = PricingCalculator;

