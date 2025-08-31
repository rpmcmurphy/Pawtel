// Booking functionality
export const Booking = {
    init() {
        this.initDateInputs();
        this.initFormHandlers();
    },

    initHotelBooking() {
        console.log("Hotel booking initialized");
        this.init();
        // Hotel-specific booking logic here
    },

    initDateInputs() {
        const today = new Date().toISOString().split("T")[0];
        const checkInInput = document.getElementById("check_in_date");
        const checkOutInput = document.getElementById("check_out_date");

        if (checkInInput) {
            checkInInput.setAttribute("min", today);
            checkInInput.addEventListener("change", (e) => {
                if (checkOutInput) {
                    const checkInDate = new Date(e.target.value);
                    const minCheckOut = new Date(checkInDate);
                    minCheckOut.setDate(minCheckOut.getDate() + 1);
                    checkOutInput.setAttribute(
                        "min",
                        minCheckOut.toISOString().split("T")[0]
                    );
                }
            });
        }
    },

    initFormHandlers() {
        const bookingForms = document.querySelectorAll(".booking-form");
        bookingForms.forEach((form) => {
            form.addEventListener(
                "submit",
                this.handleBookingSubmit.bind(this)
            );
        });
    },

    async handleBookingSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        try {
            App.showLoading(".booking-form");
            const response = await API.post("/bookings", data);

            if (response.success) {
                App.showAlert("Booking submitted successfully!", "success");
                form.reset();
            } else {
                App.showAlert(response.message || "Booking failed", "danger");
            }
        } catch (error) {
            App.showAlert("An error occurred. Please try again.", "danger");
        } finally {
            App.hideLoading();
        }
    },
};

window.Booking = Booking;
