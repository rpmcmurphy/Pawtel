<?php

namespace App\Http\Middleware;

use App\Services\Web\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminWebMiddleware
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
            return redirect()->route('auth.login')
                ->with('error', 'Please login to access admin panel.');
        }

        if (!$this->authService->isAdmin()) {
            return redirect()->route('home')
                ->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
