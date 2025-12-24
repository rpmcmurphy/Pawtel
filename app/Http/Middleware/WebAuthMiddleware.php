<?php

namespace App\Http\Middleware;

use App\Services\Web\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAuthMiddleware
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->authService->isAuthenticated()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            return redirect()->route('auth.login')
                ->with('error', 'Please login to access this page.');
        }

        // Refresh user profile to get latest data
        $profileResponse = $this->authService->getProfile();
        if (!$profileResponse['success']) {
            session()->forget(['api_token', 'user']);
            return redirect()->route('auth.login')
                ->with('error', 'Your session has expired. Please login again.');
        }

        $user = $this->authService->getUser();

        // Check if user is active
        if ($user && ($user['status'] ?? 'active') !== 'active') {
            session()->forget(['api_token', 'user']);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been suspended. Please contact support.',
                ], 403);
            }

            return redirect()->route('auth.login')
                ->with('error', 'Your account has been suspended. Please contact support.');
        }

        // Check if user is verified (optional - can be made required for specific routes)
        // Uncomment below if email verification is required for all authenticated routes
        // if ($user && !($user['email_verified_at'] ?? false)) {
        //     if ($request->expectsJson()) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Please verify your email address to continue.',
        //         ], 403);
        //     }
        //     return redirect()->route('auth.login')
        //         ->with('error', 'Please verify your email address to continue.');
        // }

        return $next($request);
    }
}
