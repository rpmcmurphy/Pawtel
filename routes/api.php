<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\{
    LoginController,
    RegisterController,
    ProfileController,
    PasswordResetController,
    VerificationController
};
use App\Http\Controllers\API\Booking\{
    HotelBookingController,
    SpaBookingController,
    SpayNeuterBookingController,
    BookingDocumentController,
    BookingAddonController,
    AvailabilityController
};
use App\Http\Controllers\API\Shop\{
    ProductController,
    CategoryController,
    CartController,
    OrderController
};
use App\Http\Controllers\API\Community\{
    PostController,
    AdoptionController,
    CommentController
};
use App\Http\Controllers\API\Admin\{
    DashboardController,
    BookingManagementController,
    RoomManagementController,
    ProductManagementController,
    OrderManagementController,
    PostManagementController,
    ReportController,
    SettingsController,
    UserManagementController
};
use App\Http\Controllers\API\{
    NotificationController,
    FileUploadController
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Health Check
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'service' => 'Pawtel API',
        'version' => '1.0.0'
    ]);
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);
    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword']);

    // Email Verification
    Route::get('verify-email/{id}/{hash}', [VerificationController::class, 'verify'])
        ->name('verification.verify');
});

// Public Shop Routes
Route::prefix('shop')->group(function () {
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{slug}', [CategoryController::class, 'show']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{slug}', [ProductController::class, 'show']);
    Route::get('products/category/{categorySlug}', [ProductController::class, 'byCategory']);
    Route::get('featured-products', [ProductController::class, 'featured']);
});

// Public Community Routes
Route::prefix('community')->group(function () {
    Route::get('posts', [PostController::class, 'index']);
    Route::get('posts/{slug}', [PostController::class, 'show']);
    Route::get('adoptions', [AdoptionController::class, 'index']);
    Route::get('adoptions/{id}', [AdoptionController::class, 'show']);
});

// Public Availability Routes
Route::prefix('availability')->group(function () {
    Route::get('room-types', [AvailabilityController::class, 'roomTypes']);
    Route::post('check', [AvailabilityController::class, 'check']);
    Route::get('spa-packages', [AvailabilityController::class, 'spaPackages']);
    Route::get('spa-slots', [AvailabilityController::class, 'spaSlots']);
    Route::get('spay-packages', [AvailabilityController::class, 'spayPackages']);
    Route::get('spay-slots', [AvailabilityController::class, 'spaySlots']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth Management
    Route::prefix('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::post('refresh', [LoginController::class, 'refresh']);
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::post('change-password', [ProfileController::class, 'changePassword']);
        Route::post('verify-phone', [VerificationController::class, 'sendPhoneVerification']);
        Route::post('verify-phone-code', [VerificationController::class, 'verifyPhone']);
        Route::post('resend-verification', [VerificationController::class, 'resendEmailVerification']);
    });

    // User Bookings
    Route::prefix('bookings')->group(function () {
        Route::get('/', [HotelBookingController::class, 'index']);
        Route::get('/{bookingNumber}', [HotelBookingController::class, 'show']);
        Route::get('/{bookingNumber}/invoice', [HotelBookingController::class, 'invoice']);

        // Hotel Bookings
        Route::post('hotel', [HotelBookingController::class, 'store']);
        Route::put('hotel/{bookingNumber}', [HotelBookingController::class, 'update']);

        // Spa Bookings
        Route::post('spa', [SpaBookingController::class, 'store']);
        Route::put('spa/{bookingNumber}', [SpaBookingController::class, 'update']);

        // Spay/Neuter Bookings
        Route::post('spay', [SpayNeuterBookingController::class, 'store']);
        Route::put('spay/{bookingNumber}', [SpayNeuterBookingController::class, 'update']);

        // Common Booking Operations
        Route::post('{bookingNumber}/cancel', [HotelBookingController::class, 'cancel']);
        Route::post('{bookingNumber}/addons', [BookingAddonController::class, 'store']);
        Route::delete('{bookingNumber}/addons/{addonId}', [BookingAddonController::class, 'destroy']);

        // Document Management
        Route::post('{bookingNumber}/documents', [BookingDocumentController::class, 'store']);
        Route::get('{bookingNumber}/documents', [BookingDocumentController::class, 'index']);
        Route::delete('{bookingNumber}/documents/{documentId}', [BookingDocumentController::class, 'destroy']);
    });

    // Shopping Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('add', [CartController::class, 'add']);
        Route::put('{itemId}', [CartController::class, 'update']);
        Route::delete('{itemId}', [CartController::class, 'remove']);
        Route::delete('/', [CartController::class, 'clear']);
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{orderNumber}', [OrderController::class, 'show']);
        Route::post('/{orderNumber}/cancel', [OrderController::class, 'cancel']);
    });

    // Community Interactions
    Route::prefix('posts')->group(function () {
        Route::post('{id}/like', [PostController::class, 'like']);
        Route::delete('{id}/unlike', [PostController::class, 'unlike']);
        Route::post('{id}/comment', [CommentController::class, 'store']);
        Route::put('comments/{commentId}', [CommentController::class, 'update']);
        Route::delete('comments/{commentId}', [CommentController::class, 'destroy']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('{id}', [NotificationController::class, 'destroy']);
    });

    // File Upload
    Route::post('upload', [FileUploadController::class, 'upload']);

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Requires Admin Role)
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('dashboard/recent-activities', [DashboardController::class, 'recentActivities']);

        // Booking Management
        Route::prefix('bookings')->group(function () {
            Route::get('/', [BookingManagementController::class, 'index']);
            Route::get('/{id}', [BookingManagementController::class, 'show']);
            Route::put('/{id}/confirm', [BookingManagementController::class, 'confirm']);
            Route::put('/{id}/cancel', [BookingManagementController::class, 'cancel']);
            Route::put('/{id}/complete', [BookingManagementController::class, 'complete']);
            Route::post('/{id}/assign-room', [BookingManagementController::class, 'assignRoom']);
            Route::post('/{id}/resend-confirmation', [BookingManagementController::class, 'resendConfirmation']);
            Route::post('/{id}/verify-documents', [BookingManagementController::class, 'verifyDocuments']);

            // Manual Booking Entry
            Route::post('manual', [BookingManagementController::class, 'createManualBooking']);
        });

        // Room Management
        Route::prefix('rooms')->group(function () {
            Route::get('/', [RoomManagementController::class, 'index']);
            Route::post('/', [RoomManagementController::class, 'store']);
            Route::get('/{id}', [RoomManagementController::class, 'show']);
            Route::put('/{id}', [RoomManagementController::class, 'update']);
            Route::delete('/{id}', [RoomManagementController::class, 'destroy']);
            Route::put('/{id}/status', [RoomManagementController::class, 'updateStatus']);

            // Room Types
            Route::get('types/list', [RoomManagementController::class, 'roomTypes']);
            Route::post('types', [RoomManagementController::class, 'storeRoomType']);
            Route::put('types/{id}', [RoomManagementController::class, 'updateRoomType']);
            Route::delete('types/{id}', [RoomManagementController::class, 'deleteRoomType']);

            // Block Dates
            Route::post('block-dates', [RoomManagementController::class, 'blockDates']);
            Route::delete('block-dates/{id}', [RoomManagementController::class, 'unblockDate']);
            Route::get('blocked-dates', [RoomManagementController::class, 'getBlockedDates']);
        });

        // Product Management
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductManagementController::class, 'index']);
            Route::post('/', [ProductManagementController::class, 'store']);
            Route::get('/{id}', [ProductManagementController::class, 'show']);
            Route::put('/{id}', [ProductManagementController::class, 'update']);
            Route::delete('/{id}', [ProductManagementController::class, 'destroy']);
            Route::post('/{id}/toggle-featured', [ProductManagementController::class, 'toggleFeatured']);
            Route::post('/{id}/update-stock', [ProductManagementController::class, 'updateStock']);
            Route::post('import', [ProductManagementController::class, 'import']);
            Route::get('export', [ProductManagementController::class, 'export']);

            // Categories
            Route::get('categories/tree', [ProductManagementController::class, 'categoryTree']);
            Route::post('categories', [ProductManagementController::class, 'storeCategory']);
            Route::put('categories/{id}', [ProductManagementController::class, 'updateCategory']);
            Route::delete('categories/{id}', [ProductManagementController::class, 'deleteCategory']);
        });

        // Order Management
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderManagementController::class, 'index']);
            Route::get('/{id}', [OrderManagementController::class, 'show']);
            Route::put('/{id}/status', [OrderManagementController::class, 'updateStatus']);
            Route::post('/{id}/ship', [OrderManagementController::class, 'markAsShipped']);
            Route::post('/{id}/deliver', [OrderManagementController::class, 'markAsDelivered']);
            Route::post('/{id}/cancel', [OrderManagementController::class, 'cancel']);
            Route::get('/{id}/invoice', [OrderManagementController::class, 'invoice']);
        });

        // Post Management
        Route::prefix('posts')->group(function () {
            Route::get('/', [PostManagementController::class, 'index']);
            Route::post('/', [PostManagementController::class, 'store']);
            Route::get('/{id}', [PostManagementController::class, 'show']);
            Route::put('/{id}', [PostManagementController::class, 'update']);
            Route::delete('/{id}', [PostManagementController::class, 'destroy']);
            Route::post('/{id}/publish', [PostManagementController::class, 'publish']);
            Route::post('/{id}/archive', [PostManagementController::class, 'archive']);

            // Comment Moderation
            Route::get('comments/pending', [PostManagementController::class, 'pendingComments']);
            Route::put('comments/{id}/approve', [PostManagementController::class, 'approveComment']);
            Route::put('comments/{id}/reject', [PostManagementController::class, 'rejectComment']);

            // Adoption Management
            Route::put('adoptions/{id}/status', [PostManagementController::class, 'updateAdoptionStatus']);
        });

        // Service Package Management
        Route::prefix('services')->group(function () {
            // Spa Packages
            Route::get('spa-packages', [PostManagementController::class, 'spaPackages']);
            Route::post('spa-packages', [PostManagementController::class, 'storeSpaPackage']);
            Route::put('spa-packages/{id}', [PostManagementController::class, 'updateSpaPackage']);
            Route::delete('spa-packages/{id}', [PostManagementController::class, 'deleteSpaPackage']);

            // Spay/Neuter Packages
            Route::get('spay-packages', [PostManagementController::class, 'spayPackages']);
            Route::post('spay-packages', [PostManagementController::class, 'storeSpayPackage']);
            Route::put('spay-packages/{id}', [PostManagementController::class, 'updateSpayPackage']);
            Route::delete('spay-packages/{id}', [PostManagementController::class, 'deleteSpayPackage']);

            // Add-on Services
            Route::get('addons', [PostManagementController::class, 'addonServices']);
            Route::post('addons', [PostManagementController::class, 'storeAddonService']);
            Route::put('addons/{id}', [PostManagementController::class, 'updateAddonService']);
            Route::delete('addons/{id}', [PostManagementController::class, 'deleteAddonService']);
        });

        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [UserManagementController::class, 'index']);
            Route::get('/{id}', [UserManagementController::class, 'show']);
            Route::post('/', [UserManagementController::class, 'store']);
            Route::put('/{id}', [UserManagementController::class, 'update']);
            Route::delete('/{id}', [UserManagementController::class, 'destroy']);
            Route::put('/{id}/status', [UserManagementController::class, 'updateStatus']);
            Route::put('/{id}/reset-password', [UserManagementController::class, 'resetPassword']);
            Route::get('/{id}/bookings', [UserManagementController::class, 'userBookings']);
            Route::get('/{id}/orders', [UserManagementController::class, 'userOrders']);
            Route::get('/{id}/activity', [UserManagementController::class, 'userActivity']);
        });

        // Reports
        Route::prefix('reports')->group(function () {
            // Booking Reports
            Route::get('bookings', [ReportController::class, 'bookings']);
            Route::get('bookings/summary', [ReportController::class, 'bookingSummary']);
            Route::get('bookings/revenue', [ReportController::class, 'bookingRevenue']);
            Route::get('bookings/occupancy', [ReportController::class, 'occupancyReport']);
            Route::get('bookings/export', [ReportController::class, 'exportBookings']);

            // Sales Reports
            Route::get('sales', [ReportController::class, 'sales']);
            Route::get('sales/summary', [ReportController::class, 'salesSummary']);
            Route::get('sales/products', [ReportController::class, 'productSales']);
            Route::get('sales/categories', [ReportController::class, 'categorySales']);
            Route::get('sales/export', [ReportController::class, 'exportSales']);

            // Service Reports
            Route::get('services', [ReportController::class, 'services']);
            Route::get('services/spa', [ReportController::class, 'spaReport']);
            Route::get('services/spay', [ReportController::class, 'spayReport']);
            Route::get('services/addons', [ReportController::class, 'addonsReport']);

            // Customer Reports
            Route::get('customers', [ReportController::class, 'customers']);
            Route::get('customers/top', [ReportController::class, 'topCustomers']);
            Route::get('customers/new', [ReportController::class, 'newCustomers']);
            Route::get('customers/retention', [ReportController::class, 'customerRetention']);

            // Financial Reports
            Route::get('financial', [ReportController::class, 'financial']);
            Route::get('financial/daily', [ReportController::class, 'dailyRevenue']);
            Route::get('financial/monthly', [ReportController::class, 'monthlyRevenue']);
            Route::get('financial/yearly', [ReportController::class, 'yearlyRevenue']);

            // Custom Reports
            Route::post('custom', [ReportController::class, 'generateCustomReport']);
        });

        // Settings
        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingsController::class, 'index']);
            Route::get('/{group}', [SettingsController::class, 'getByGroup']);
            Route::put('/', [SettingsController::class, 'update']);
            Route::post('/', [SettingsController::class, 'store']);

            // Specific Settings
            Route::get('booking/rules', [SettingsController::class, 'bookingRules']);
            Route::put('booking/rules', [SettingsController::class, 'updateBookingRules']);
            Route::get('email/templates', [SettingsController::class, 'emailTemplates']);
            Route::put('email/templates', [SettingsController::class, 'updateEmailTemplates']);
            Route::get('payment/options', [SettingsController::class, 'paymentOptions']);
            Route::put('payment/options', [SettingsController::class, 'updatePaymentOptions']);
            Route::get('site/info', [SettingsController::class, 'siteInfo']);
            Route::put('site/info', [SettingsController::class, 'updateSiteInfo']);
        });

        // Activity Logs
        Route::prefix('activity-logs')->group(function () {
            Route::get('/', [DashboardController::class, 'activityLogs']);
            Route::get('/export', [DashboardController::class, 'exportActivityLogs']);
        });

        // System Maintenance
        Route::prefix('system')->group(function () {
            Route::post('cache/clear', [SettingsController::class, 'clearCache']);
            Route::post('backup', [SettingsController::class, 'backup']);
            Route::get('health', [SettingsController::class, 'systemHealth']);
            Route::get('info', [SettingsController::class, 'systemInfo']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint not found',
        'error' => 'The requested API endpoint does not exist'
    ], 404);
});