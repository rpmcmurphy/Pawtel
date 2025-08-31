// import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize CSRF token for Axios
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }

    // Initialize global components
    App.init();
});

// Global App object
window.App = {
    init: function() {
        this.initTooltips();
        this.initAlerts();
        this.initImageUploads();
        this.initDateInputs();
    },

    initTooltips: function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },

    initAlerts: function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    },

    initImageUploads: function() {
        document.querySelectorAll('input[type="file"]').forEach(function(input) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Show file preview if it's an image
                    if (file.type.startsWith('image/')) {
                        const preview = document.createElement('img');
                        preview.style.maxWidth = '200px';
                        preview.style.maxHeight = '200px';
                        preview.style.marginTop = '10px';
                        preview.src = URL.createObjectURL(file);
                        
                        // Remove existing preview
                        const existingPreview = input.parentNode.querySelector('img');
                        if (existingPreview) {
                            existingPreview.remove();
                        }
                        
                        input.parentNode.appendChild(preview);
                    }
                }
            });
        });
    },

    initDateInputs: function() {