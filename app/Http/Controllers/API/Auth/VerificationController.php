<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification link'
                ], 400);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email already verified'
                ]);
            }

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->log('Email verified');

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resendEmailVerification(): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email already verified'
                ]);
            }

            $user->sendEmailVerificationNotification();

            return response()->json([
                'success' => true,
                'message' => 'Verification email sent'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendPhoneVerification(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($user->hasVerifiedPhone()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Phone already verified'
                ]);
            }

            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store OTP in cache for 5 minutes
            cache(["phone_otp_{$user->id}" => $otp], now()->addMinutes(5));

            // In production, send SMS here
            // For development, return OTP in response (remove in production)
            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your phone',
                'debug_otp' => config('app.debug') ? $otp : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send phone verification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyPhone(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        try {
            $user = Auth::user();
            $storedOtp = cache("phone_otp_{$user->id}");

            if (!$storedOtp || $storedOtp !== $request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 400);
            }

            $user->update(['phone_verified_at' => now()]);
            cache()->forget("phone_otp_{$user->id}");

            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->log('Phone verified');

            return response()->json([
                'success' => true,
                'message' => 'Phone verified successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Phone verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
