import "./bootstrap";
import "../scss/admin.scss";

// Import Bootstrap JS
import "bootstrap";

// Import Chart.js
import Chart from "chart.js/auto";
window.Chart = Chart;

// Import DataTables
import DataTable from "datatables.net-bs5";
window.DataTable = DataTable;

// Admin specific functionality
document.addEventListener("DOMContentLoaded", function () {
    // Initialize DataTables
    $(".data-table, .table").each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                responsive: true,
                pageLength: 25,
                order: [[0, "desc"]],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous",
                    },
                },
            });
        }
    });

    // Auto-refresh stats every 30 seconds
    if (window.location.pathname === "/admin") {
        setInterval(function () {
            refreshDashboardStats();
        }, 30000);
    }

    // Confirm actions
    document.addEventListener("click", function (e) {
        if (e.target.matches("[data-confirm]")) {
            if (!confirm(e.target.dataset.confirm)) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Status update handlers
    document.addEventListener("change", function (e) {
        if (e.target.matches(".status-select")) {
            updateStatus(e.target);
        }
    });
});

function refreshDashboardStats() {
    fetch("/admin/stats")
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                updateStatCard("todayBookings", data.data.today_bookings);
                updateStatCard(
                    "monthlyRevenue",
                    App.formatCurrency(data.data.monthly_revenue || 0)
                );
                updateStatCard("activeUsers", data.data.active_users);
                updateStatCard("pendingBookings", data.data.pending_bookings);
            }
        })
        .catch((error) => console.error("Failed to refresh stats:", error));
}

function updateStatCard(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = value;
    }
}

function updateStatus(selectElement) {
    const url = selectElement.dataset.url;
    const status = selectElement.value;

    fetch(url, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({ status: status }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                App.showAlert(data.message, "success");
            } else {
                App.showAlert(data.message, "error");
                // Revert select value
                selectElement.value = selectElement.dataset.originalValue;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            App.showAlert("Failed to update status", "error");
            selectElement.value = selectElement.dataset.originalValue;
        });
}
