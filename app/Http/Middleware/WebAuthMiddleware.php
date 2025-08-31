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

        // Validate token by making a profile request
        $profileResponse = $this->authService->getProfile();

        if (!$profileResponse['success']) {
            // Token is invalid, clear session and redirect to login
            session()->forget(['api_token', 'user']);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Token expired'], 401);
            }

            return redirect()->route('auth.login')
                ->with('error', 'Your session has expired. Please login again.');
        }

        return $next($request);
    }
}
