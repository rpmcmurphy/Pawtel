<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been suspended. Please contact support.'
                ], 403);
            }

            // Delete old tokens
            $user->tokens()->delete();

            // Generate new token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Log the login activity
            activity()
                ->causedBy($user)
                ->log('User logged in');

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'city' => $user->city,
                        'email_verified_at' => $user->email_verified_at,
                        'phone_verified_at' => $user->phone_verified_at,
                        'roles' => $user->getRoleNames(),
                    ],
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Delete current token
            $user->currentAccessToken()->delete();

            activity()
                ->causedBy($user)
                ->log('User logged out');

            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function refresh(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Delete current token
            $user->currentAccessToken()->delete();

            // Generate new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
