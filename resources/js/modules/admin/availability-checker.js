/**
 * Availability Checker Module
 * Handles availability checks for bookings
 */
export class AvailabilityChecker {
    constructor(options = {}) {
        this.options = {
            apiBaseUrl: options.apiBaseUrl || '/api',
            ...options
        };
    }

    /**
     * Check hotel room availability
     */
    async checkHotelAvailability(roomTypeId, checkInDate, checkOutDate) {
        try {
            const response = await fetch(`${this.options.apiBaseUrl}/availability/check`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    type: 'hotel',
                    room_type_id: roomTypeId,
                    check_in_date: checkInDate,
                    check_out_date: checkOutDate
                })
            });

            const data = await response.json();
            return data.success ? data.data : null;
        } catch (error) {
            console.error('Availability check error:', error);
            return null;
        }
    }

    /**
     * Check spa availability
     */
    async checkSpaAvailability(spaPackageId, date, time) {
        try {
            const response = await fetch(`${this.options.apiBaseUrl}/availability/spa-slots`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    spa_package_id: spaPackageId,
                    date: date,
                    time: time
                })
            });

            const data = await response.json();
            return data.success ? data.data : null;
        } catch (error) {
            console.error('Availability check error:', error);
            return null;
        }
    }

    /**
     * Check spay/neuter availability
     */
    async checkSpayAvailability(spayPackageId, date) {
        try {
            const response = await fetch(`${this.options.apiBaseUrl}/availability/spay-slots`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    spay_package_id: spayPackageId,
                    date: date
                })
            });

            const data = await response.json();
            return data.success ? data.data : null;
        } catch (error) {
            console.error('Availability check error:', error);
            return null;
        }
    }

    /**
     * Get available room types
     */
    async getAvailableRoomTypes(checkInDate, checkOutDate) {
        try {
            const response = await fetch(`${this.options.apiBaseUrl}/availability/room-types?check_in=${checkInDate}&check_out=${checkOutDate}`);
            const data = await response.json();
            return data.success ? data.data : [];
        } catch (error) {
            console.error('Error fetching room types:', error);
            return [];
        }
    }

    /**
     * Display availability status
     */
    displayAvailabilityStatus(elementId, isAvailable, message = null) {
        const element = document.getElementById(elementId);
        if (!element) return;

        if (isAvailable) {
            element.className = 'alert alert-success';
            element.textContent = message || 'Available';
        } else {
            element.className = 'alert alert-danger';
            element.textContent = message || 'Not Available';
        }
    }
}

// Make available globally
window.AvailabilityChecker = AvailabilityChecker;

