<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdminWebMiddleware;
use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\CustomerMiddleware;
use App\Http\Middleware\JsonResponseMiddleware;
use App\Http\Middleware\LogApiRequests;
use App\Http\Middleware\WebAuthMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // $middleware->append([
        // HandleCors::class,
        // CorsMiddleware::class,
        // ]);

        $middleware->prepend(HandleCors::class);

        // $middleware->appendToGroup('api', HandleCors::class);

        // $middleware->web(append: [
        //     EncryptCookies::class,
        //     AddQueuedCookiesToResponse::class,
        //     StartSession::class,
        //     ShareErrorsFromSession::class,
        //     VerifyCsrfToken::class,
        //     SubstituteBindings::class,
        // ]);

        $middleware->api(append: [
            // EnsureFrontendRequestsAreStateful::class,
            JsonResponseMiddleware::class,
            LogApiRequests::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/*',
            // 'auth/*',
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'admin' => AdminMiddleware::class,
            'customer' => CustomerMiddleware::class,
            'active' => CheckUserStatus::class,
            'auth.web' => WebAuthMiddleware::class,
            'admin.web' => AdminWebMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
