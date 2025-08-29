<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Services
use App\Services\Admin\{
    DashboardService,
    BookingManagementService,
    ProductManagementService,
    OrderManagementService,
    UserManagementService,
    PostManagementService,
    ReportService
};

// Repositories
use App\Repositories\{
    BookingRepository,
    OrderRepository,
    ProductRepository,
    UserRepository,
    PostRepository,
    RoomRepository
};

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Repositories
        $this->app->singleton(BookingRepository::class);
        $this->app->singleton(OrderRepository::class);
        $this->app->singleton(ProductRepository::class);
        $this->app->singleton(UserRepository::class);
        $this->app->singleton(PostRepository::class);
        $this->app->singleton(RoomRepository::class);

        // Register Services
        $this->app->singleton(DashboardService::class);
        $this->app->singleton(BookingManagementService::class);
        $this->app->singleton(ProductManagementService::class);
        $this->app->singleton(OrderManagementService::class);
        $this->app->singleton(UserManagementService::class);
        $this->app->singleton(PostManagementService::class);
        $this->app->singleton(ReportService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
