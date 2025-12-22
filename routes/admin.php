<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Admin\{
    DashboardController,
    BookingController,
    ProductController as AdminProductController,
    UserController,
    ReportController,
    PostController
};

Route::middleware(['auth.web', 'admin.web'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('stats', [DashboardController::class, 'stats'])->name('stats');

    // Bookings Management
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('{id}', [BookingController::class, 'show'])->name('show'); // FIXED: removed 'booking/' prefix
        Route::get('{id}/edit', [BookingController::class, 'edit'])->name('edit'); // ADDED
        Route::put('{id}', [BookingController::class, 'update'])->name('update'); // ADDED
        Route::post('{id}/confirm', [BookingController::class, 'confirm'])->name('confirm');
        Route::post('{id}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('calculate-price', [BookingController::class, 'calculatePrice'])->name('calculate-price');
        Route::get('type/{type}', [BookingController::class, 'byType'])->name('type'); // FIXED: moved to end
    });

    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('create', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::get('{id}', [AdminProductController::class, 'show'])->name('show');
        Route::get('{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('{id}', [AdminProductController::class, 'update'])->name('update');
        Route::delete('{id}', [AdminProductController::class, 'destroy'])->name('destroy');
        Route::put('{id}/status', [AdminProductController::class, 'updateStatus'])->name('status');
    });

    // Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Admin\OrderController::class, 'index'])->name('index');
        Route::get('{id}', [\App\Http\Controllers\Web\Admin\OrderController::class, 'show'])->name('show');
        Route::put('{id}/status', [\App\Http\Controllers\Web\Admin\OrderController::class, 'updateStatus'])->name('status');
        Route::post('{id}/ship', [\App\Http\Controllers\Web\Admin\OrderController::class, 'ship'])->name('ship');
        Route::post('{id}/deliver', [\App\Http\Controllers\Web\Admin\OrderController::class, 'deliver'])->name('deliver');
        Route::post('{id}/cancel', [\App\Http\Controllers\Web\Admin\OrderController::class, 'cancel'])->name('cancel');
        Route::get('{id}/invoice', [\App\Http\Controllers\Web\Admin\OrderController::class, 'invoice'])->name('invoice');
    });

    // Rooms Management
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Admin\RoomController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\Web\Admin\RoomController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Web\Admin\RoomController::class, 'store'])->name('store');
        Route::get('{id}', [\App\Http\Controllers\Web\Admin\RoomController::class, 'show'])->name('show');
        Route::get('{id}/edit', [\App\Http\Controllers\Web\Admin\RoomController::class, 'edit'])->name('edit');
        Route::put('{id}', [\App\Http\Controllers\Web\Admin\RoomController::class, 'update'])->name('update');
        Route::put('{id}/status', [\App\Http\Controllers\Web\Admin\RoomController::class, 'updateStatus'])->name('status');
        Route::post('block-dates', [\App\Http\Controllers\Web\Admin\RoomController::class, 'blockDates'])->name('block-dates');
    });

    // Service Packages Management
    Route::prefix('services')->name('services.')->group(function () {
        // Spa Packages
        Route::prefix('spa')->name('spa.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'spaIndex'])->name('index');
            Route::get('create', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'spaCreate'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'spaStore'])->name('store');
            Route::get('{id}/edit', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'spaEdit'])->name('edit');
            Route::put('{id}', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'spaUpdate'])->name('update');
        });
        // Spay Packages
        Route::prefix('spay')->name('spay.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'spayIndex'])->name('index');
            Route::get('create', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'spayCreate'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'spayStore'])->name('store');
            Route::get('{id}/edit', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'spayEdit'])->name('edit');
            Route::put('{id}', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'spayUpdate'])->name('update');
        });
        // Addon Services
        Route::prefix('addons')->name('addons.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'addonIndex'])->name('index');
            Route::get('create', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'addonCreate'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'addonStore'])->name('store');
            Route::get('{id}/edit', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'addonEdit'])->name('edit');
            Route::put('{id}', [\App\Http\Controllers\Web\Admin\ServicePackageController::class, 'addonUpdate'])->name('update');
        });
    });

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('search', [UserController::class, 'search'])->name('search');
        Route::get('{id}', [UserController::class, 'show'])->name('show');
        Route::put('{id}/status', [UserController::class, 'updateStatus'])->name('status');
        Route::get('{id}/bookings', [UserController::class, 'bookings'])->name('bookings');
        Route::get('{id}/orders', [UserController::class, 'orders'])->name('orders');
    });

    Route::get('customers/search', [UserController::class, 'searchCustomers'])->name('customers.search');

    // Posts/Community Management
    Route::prefix('posts')->name('posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::get('{id}', [PostController::class, 'show'])->name('show');
        Route::get('{id}/edit', [PostController::class, 'edit'])->name('edit');
        Route::put('{id}', [PostController::class, 'update'])->name('update');
        Route::delete('{id}', [PostController::class, 'destroy'])->name('destroy');
        Route::post('{id}/publish', [PostController::class, 'publish'])->name('publish');
        Route::post('{id}/archive', [PostController::class, 'archive'])->name('archive');

        // Comment moderation
        Route::get('comments/pending', [PostController::class, 'pendingComments'])->name('comments.pending');
        Route::post('comments/{commentId}/approve', [PostController::class, 'approveComment'])->name('comments.approve');
        Route::post('comments/{commentId}/reject', [PostController::class, 'rejectComment'])->name('comments.reject');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('bookings', [ReportController::class, 'bookings'])->name('bookings');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('financial', [ReportController::class, 'financial'])->name('financial');
        Route::post('export', [ReportController::class, 'export'])->name('export');
    });
});
