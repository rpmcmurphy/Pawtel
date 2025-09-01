<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Admin\{
    DashboardController,
    BookingController,
    ProductController as AdminProductController,
    UserController,
    ReportController
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

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('bookings', [ReportController::class, 'bookings'])->name('bookings');
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('financial', [ReportController::class, 'financial'])->name('financial');
        Route::post('export', [ReportController::class, 'export'])->name('export');
    });
});
