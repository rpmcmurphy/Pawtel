<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\Auth\{LoginController, RegisterController, ProfileController};
use App\Http\Controllers\Web\Booking\{HotelController, SpaController, SpayController};
use App\Http\Controllers\Web\Shop\{ProductController, CartController, OrderController};
use App\Http\Controllers\Web\Community\{PostController, AdoptionController};

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Web Routes - Authentication
|--------------------------------------------------------------------------
|
| These routes are for web-based features like email verification links
|
*/

// Route::get('/', function () {
//     return response()->json([
//         'name' => 'Pawtel API',
//         'version' => '1.0.0',
//     ]);
// });

// Email Verification Routes (handled by web browser)
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    // This will be handled by the frontend application
    return redirect(config('app.frontend_url') . '/verify-email?id=' . $id . '&hash=' . $hash);
})->name('verification.verify');

// Password Reset Routes (handled by web browser)
Route::get('/password/reset/{token}', function ($token) {
    // This will be handled by the frontend application
    return redirect(config('app.frontend_url') . '/reset-password?token=' . $token);
})->name('password.reset');

/*
|--------------------------------------------------------------------------
| Web Routes- Main
|--------------------------------------------------------------------------
|
| These routes are for main application features in the web interface
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('login', [LoginController::class, 'showLogin'])->name('login');

// Auth Routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'showLogin'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->name('login.post');
        Route::get('register', [RegisterController::class, 'showRegister'])->name('register');
        Route::post('register', [RegisterController::class, 'register'])->name('register.post');
    });

    Route::middleware('auth.web')->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    });
});

// Booking Routes
Route::prefix('booking')->name('booking.')->group(function () {
    // Hotel Booking
    Route::prefix('hotel')->name('hotel.')->group(function () {
        Route::get('/', [HotelController::class, 'index'])->name('index');
        Route::get('rooms', [HotelController::class, 'rooms'])->name('rooms');
        Route::post('check-availability', [HotelController::class, 'checkAvailability'])->name('availability');

        Route::middleware('auth.web')->group(function () {
            Route::get('book', [HotelController::class, 'showBookingForm'])->name('form');
            Route::post('book', [HotelController::class, 'store'])->name('store');
            Route::get('confirmation/{booking}', [HotelController::class, 'confirmation'])->name('confirmation');
        });
    });

    // Spa Booking
    Route::prefix('spa')->name('spa.')->group(function () {
        Route::get('/', [SpaController::class, 'index'])->name('index');
        Route::get('packages', [SpaController::class, 'packages'])->name('packages');

        Route::middleware('auth.web')->group(function () {
            Route::get('book', [SpaController::class, 'showBookingForm'])->name('form');
            Route::post('book', [SpaController::class, 'store'])->name('store');
        });
    });

    // Spay/Neuter Booking
    Route::prefix('spay')->name('spay.')->group(function () {
        Route::get('/', [SpayController::class, 'index'])->name('index');

        Route::middleware('auth.web')->group(function () {
            Route::get('book', [SpayController::class, 'showBookingForm'])->name('form');
            Route::post('book', [SpayController::class, 'store'])->name('store');
        });
    });
});

// Shop Routes
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('category/{slug}', [ProductController::class, 'category'])->name('category');
    Route::get('product/{slug}', [ProductController::class, 'show'])->name('product');

    Route::middleware('auth.web')->group(function () {
        Route::get('cart', [CartController::class, 'index'])->name('cart');
        Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::put('cart/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('cart/{id}', [CartController::class, 'remove'])->name('cart.remove');

        Route::get('checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('order', [OrderController::class, 'store'])->name('order.store');
    });
});

// Community Routes
Route::prefix('community')->name('community.')->group(function () {
    Route::get('posts', [PostController::class, 'index'])->name('posts');
    Route::get('post/{slug}', [PostController::class, 'show'])->name('post');
    Route::get('adoption', [AdoptionController::class, 'index'])->name('adoption');
    Route::get('adoption/{slug}', [AdoptionController::class, 'show'])->name('adoption.show');

    Route::middleware('auth.web')->group(function () {
        Route::post('post/{id}/like', [PostController::class, 'like'])->name('post.like');
        Route::post('post/{id}/comment', [PostController::class, 'comment'])->name('post.comment');
    });
});

// My Account Routes
Route::middleware('auth.web')->prefix('my-account')->name('account.')->group(function () {
    Route::get('/', [ProfileController::class, 'dashboard'])->name('dashboard');
    Route::get('bookings', [ProfileController::class, 'bookings'])->name('bookings');
    Route::get('orders', [ProfileController::class, 'orders'])->name('orders');
    Route::get('booking/{id}', [ProfileController::class, 'booking'])->name('booking.show');
    Route::get('order/{id}', [ProfileController::class, 'order'])->name('order.show');
    Route::post('booking/{id}/cancel', [ProfileController::class, 'cancelBooking'])->name('booking.cancel');
});

require __DIR__ . '/admin.php';

Route::middleware('auth.web')->get('/debug-auth', function () {
    $authService = app(\App\Services\Web\AuthService::class);

    return response()->json([
        'authenticated' => $authService->isAuthenticated(),
        'is_admin' => $authService->isAdmin(),
        'user' => $authService->getUser(),
        'user_roles' => $authService->getUserRoles(),
        'session_data' => [
            'has_token' => session()->has('api_token'),
            'has_user' => session()->has('user'),
            'token' => session()->get('api_token') ? 'Present' : 'Missing',
        ]
    ]);
});