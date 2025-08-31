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

        // Check if user has admin role
        if (!$this->authService->isAdmin()) {
            $user = $this->authService->getUser();

            return redirect()->route('home')
                ->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
