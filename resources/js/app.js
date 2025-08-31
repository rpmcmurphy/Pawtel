import "./bootstrap";
import "../scss/app.scss";

import "bootstrap";

// Import our modules
import "./modules/api";
import "./modules/booking";
import "./modules/shop";

// Global App object
window.App = {
    init: function () {
        this.initTooltips();
        this.initAlerts();
        this.initImageUploads();
        this.initDateInputs();
        this.initFormValidation();
    },

    initTooltips: function () {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },

    initAlerts: function () {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function () {
            const alerts = document.querySelectorAll(".alert");
            alerts.forEach((alert) => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    },

    initImageUploads: function () {
        document
            .querySelectorAll('input[type="file"]')
            .forEach(function (input) {
                input.addEventListener("change", function (e) {
                    const file = e.target.files[0];
                    if (file && file.type.startsWith("image/")) {
                        const preview = document.createElement("img");
                        preview.style.maxWidth = "200px";
                        preview.style.maxHeight = "200px";
                        preview.style.marginTop = "10px";
                        preview.className = "rounded";
                        preview.src = URL.createObjectURL(file);

                        // Remove existing preview
                        const existingPreview =
                            input.parentNode.querySelector("img");
                        if (existingPreview) {
                            existingPreview.remove();
                        }

                        input.parentNode.appendChild(preview);
                    }
                });
            });
    },

    initDateInputs: function () {
        // Set minimum date to today for date inputs
        const today = new Date().toISOString().split("T")[0];
        document
            .querySelectorAll('input[type="date"]')
            .forEach(function (input) {
                if (!input.getAttribute("min")) {
                    input.setAttribute("min", today);
                }
            });
    },

    initFormValidation: function () {
        // Bootstrap form validation
        const forms = document.querySelectorAll(".needs-validation");
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener(
                "submit",
                function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add("was-validated");
                },
                false
            );
        });
    },

    showLoading: function (element) {
        if (typeof element === "string") {
            element = document.querySelector(element);
        }
        if (element) {
            element.innerHTML =
                '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        }
    },

    hideLoading: function () {
        document.querySelectorAll(".loading").forEach((el) => el.remove());
    },

    showAlert: function (message, type = "info") {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show pawtel-alert" role="alert">
                <i class="fas fa-${
                    type === "success"
                        ? "check"
                        : type === "danger"
                        ? "exclamation"
                        : "info"
                }-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        const container = document.querySelector(".container");
        if (container) {
            container.insertAdjacentHTML("afterbegin", alertHtml);
        }
    },

    formatCurrency: function (amount) {
        return (
            "৳" +
            Number(amount).toLocaleString("en-BD", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        );
    },
};

// Initialize app when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    App.init();

    // // Initialize CSRF token for Axios
    // const token = document.head.querySelector('meta[name="csrf-token"]');
    // if (token) {
    //     axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
    // }

    // Initialize specific modules based on page
    if (window.location.pathname.includes("/booking/")) {
        if (typeof Booking !== "undefined") {
            Booking.init();
        }
    }

    if (window.location.pathname.includes("/shop/")) {
        if (typeof Shop !== "undefined") {
            Shop.init();
        }
    }
});
