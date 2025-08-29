<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use App\Services\{
    AvailabilityService,
    BookingAddonService,
    BookingDocumentService,
    BookingService,
    FileUploadService,
    NotificationService,
    PricingService
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AvailabilityService::class);
        $this->app->singleton(BookingAddonService::class);
        $this->app->singleton(BookingDocumentService::class);
        $this->app->singleton(BookingService::class);
        $this->app->singleton(FileUploadService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(PricingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
