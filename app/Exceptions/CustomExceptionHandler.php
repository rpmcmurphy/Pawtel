<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomExceptionHandler extends Exception
{
    public static function handle(Throwable $e)
    {
        // Global logging for ALL exceptions
        Log::error('Exception: ' . get_class($e), [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
        ]);

        // Handle API responses
        if (request()->expectsJson() || request()->is('api/*')) {
            return self::handleApiException($e);
        }

        throw $e; // Re-throw for default handling
    }

    private static function handleApiException(Throwable $e)
    {
        // Validation errors - pass exact errors to frontend
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // All other errors
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}