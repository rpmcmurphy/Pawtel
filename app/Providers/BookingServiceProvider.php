<?php

namespace App\Providers;

use App\Services\BookingAddonService;
use App\Services\BookingDocumentService;
use Illuminate\Support\ServiceProvider;

class BookingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BookingDocumentService::class);
        $this->app->singleton(BookingAddonService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
