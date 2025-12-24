<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use App\Services\Web\AuthService;

use App\Services\{
    AvailabilityService,
    BookingAddonService,
    BookingDocumentService,
    BookingService,
    FileUploadService,
    NotificationService,
    PricingService
};
use Illuminate\Support\Facades\Auth;

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

        // Share AuthService with all views
        View::composer('*', function ($view) {
            $authService = app(AuthService::class);
            $view->with('authService', $authService);
            $view->with('authUser', $authService->getUser());
            $view->with('isAuthenticated', $authService->isAuthenticated());
        });

        // Custom Blade directive for web authentication
        Blade::if('authWeb', function () {
            $authService = app(AuthService::class);
            return $authService->isAuthenticated();
        });

        // Custom Blade directive for verified users
        Blade::if('verified', function () {
            $authService = app(AuthService::class);
            $user = $authService->getUser();
            return $user && ($user['email_verified_at'] ?? false);
        });

        // Custom Blade directive for active users
        Blade::if('active', function () {
            $authService = app(AuthService::class);
            $user = $authService->getUser();
            return $user && ($user['status'] ?? 'active') === 'active';
        });
    }
}
