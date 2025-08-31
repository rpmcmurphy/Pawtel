<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Services\Web\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        if ($this->authService->isAuthenticated()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $response = $this->authService->login($credentials);

        if ($response['success']) {
            return redirect()->intended(route('home'))
                ->with('success', 'Welcome back! You have been logged in successfully.');
        }

        throw ValidationException::withMessages([
            'email' => [$response['message'] ?? 'Login failed. Please check your credentials.'],
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}
