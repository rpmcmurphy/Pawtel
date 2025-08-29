<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes are for web-based features like email verification links
|
*/

Route::get('/', function () {
    return response()->json([
        'name' => 'Pawtel API',
        'version' => '1.0.0',
        'documentation' => config('app.url') . '/api/documentation'
    ]);
});

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
