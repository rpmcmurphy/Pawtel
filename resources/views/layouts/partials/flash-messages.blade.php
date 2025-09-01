{{-- Success Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <div class="flex-grow-1">
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

{{-- Error Messages --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div class="flex-grow-1">
                <strong>Error!</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

{{-- Warning Messages --}}
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div class="flex-grow-1">
                <strong>Warning!</strong> {{ session('warning') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

{{-- Info Messages --}}
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            <div class="flex-grow-1">
                <strong>Info:</strong> {{ session('info') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

{{-- Custom Status Messages --}}
@if(session('status'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-bell me-2"></i>
            <div class="flex-grow-1">
                {{ session('status') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

{{-- Validation Errors Summary --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-start">
            <i class="fas fa-exclamation-circle me-2 mt-1"></i>
            <div class="flex-grow-1">
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

{{-- Additional message types for specific use cases --}}
@if(session('booking_confirmed'))
    <div class="alert alert-success alert-dismissible fade show border-left-success" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-calendar-check me-2"></i>
            <div class="flex-grow-1">
                <strong>Booking Confirmed!</strong> {{ session('booking_confirmed') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session('payment_success'))
    <div class="alert alert-success alert-dismissible fade show border-left-success" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-credit-card me-2"></i>
            <div class="flex-grow-1">
                <strong>Payment Successful!</strong> {{ session('payment_success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session('booking_cancelled'))
    <div class="alert alert-warning alert-dismissible fade show border-left-warning" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-calendar-times me-2"></i>
            <div class="flex-grow-1">
                <strong>Booking Cancelled</strong> {{ session('booking_cancelled') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session('profile_updated'))
    <div class="alert alert-info alert-dismissible fade show border-left-info" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-check me-2"></i>
            <div class="flex-grow-1">
                <strong>Profile Updated!</strong> {{ session('profile_updated') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

{{-- Admin-specific messages --}}
@if(session('admin_action'))
    <div class="alert alert-primary alert-dismissible fade show border-left-primary" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-cog me-2"></i>
            <div class="flex-grow-1">
                <strong>Admin Action:</strong> {{ session('admin_action') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session('bulk_action'))
    <div class="alert alert-info alert-dismissible fade show border-left-info" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-list-check me-2"></i>
            <div class="flex-grow-1">
                <strong>Bulk Action Complete:</strong> {{ session('bulk_action') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@push('styles')
<style>
    /* Enhanced alert styles */
    .alert {
        border-radius: 0.5rem;
        border: none;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .alert .fas {
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    /* Border left accent styles */
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }

    .border-left-danger {
        border-left: 4px solid #dc3545 !important;
    }

    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }

    .border-left-info {
        border-left: 4px solid #17a2b8 !important;
    }

    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }

    /* Success alert enhancement */
    .alert-success {
        background: linear-gradient(90deg, #d4edda 0%, #f0f9f0 100%);
        color: #155724;
    }

    .alert-success .fas {
        color: #28a745;
    }

    /* Error alert enhancement */
    .alert-danger {
        background: linear-gradient(90deg, #f8d7da 0%, #fdf2f2 100%);
        color: #721c24;
    }

    .alert-danger .fas {
        color: #dc3545;
    }

    /* Warning alert enhancement */
    .alert-warning {
        background: linear-gradient(90deg, #fff3cd 0%, #fffbf0 100%);
        color: #856404;
    }

    .alert-warning .fas {
        color: #ffc107;
    }

    /* Info alert enhancement */
    .alert-info {
        background: linear-gradient(90deg, #d1ecf1 0%, #f0f8ff 100%);
        color: #0c5460;
    }

    .alert-info .fas {
        color: #17a2b8;
    }

    /* Primary alert enhancement */
    .alert-primary {
        background: linear-gradient(90deg, #d1e7ff 0%, #f0f7ff 100%);
        color: #084298;
    }

    .alert-primary .fas {
        color: #0d6efd;
    }

    /* Animation for alerts */
    .alert {
        animation: slideInDown 0.3s ease-out;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Enhanced close button */
    .alert .btn-close {
        padding: 0.75rem;
        opacity: 0.7;
    }

    .alert .btn-close:hover {
        opacity: 1;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .alert {
            margin-left: -15px;
            margin-right: -15px;
            border-radius: 0;
        }

        .alert .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .alert .fas {
            margin-bottom: 0.5rem;
        }

        .alert .btn-close {
            align-self: flex-end;
            margin-top: -2rem;
        }
    }

    /* Toast-like positioning for certain message types */
    .alert.toast-position {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1055;
        min-width: 300px;
        max-width: 500px;
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script type="module">
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds (except error alerts)
    const alerts = document.querySelectorAll('.alert:not(.alert-danger)');
    
    alerts.forEach(function(alert) {
        // Skip if it already has a close button interaction
        if (alert.querySelector('.btn-close')) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });

    // Enhanced error alert interaction
    const errorAlerts = document.querySelectorAll('.alert-danger');
    errorAlerts.forEach(function(alert) {
        // Add click-to-dismiss functionality
        alert.style.cursor = 'pointer';
        alert.title = 'Click to dismiss';
        
        alert.addEventListener('click', function(e) {
            if (!e.target.classList.contains('btn-close')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    });

    // Add sound notification for certain alert types (optional)
    const soundAlerts = document.querySelectorAll('.alert-success, .alert-danger');
    soundAlerts.forEach(function(alert) {
        // You can add audio notification here if desired
        // const audio = new Audio('/sounds/notification.mp3');
        // audio.play().catch(() => {}); // Ignore if audio fails
    });

    // Smooth scroll to first error alert
    const firstError = document.querySelector('.alert-danger');
    if (firstError) {
        setTimeout(function() {
            firstError.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }, 100);
    }

    // Add shake animation to validation error alerts
    const validationAlerts = document.querySelectorAll('.alert-danger');
    validationAlerts.forEach(function(alert) {
        alert.style.animation = 'shake 0.5s ease-in-out';
    });
});

// Add shake animation CSS
const shakeStyle = document.createElement('style');
shakeStyle.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
`;
document.head.appendChild(shakeStyle);
</script>
@endpush