<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Services\Web\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showRegister()
    {
        if ($this->authService->isAuthenticated()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:20',
        ]);

        $userData = $request->only([
            'name',
            'email',
            'phone',
            'password',
            'password_confirmation',
            'address',
            'city',
            'emergency_contact'
        ]);

        $response = $this->authService->register($userData);

        if ($response['success']) {
            return redirect()->route('home')
                ->with('success', 'Registration successful! Welcome to Pawtel.');
        }

        // Handle API validation errors
        if (isset($response['data']['errors'])) {
            $errors = $response['data']['errors'];
            throw ValidationException::withMessages($errors);
        }

        throw ValidationException::withMessages([
            'email' => [$response['message'] ?? 'Registration failed. Please try again.'],
        ]);
    }
}
