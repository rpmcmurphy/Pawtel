<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class CustomExceptionHandler extends Exception
{
    public static function handle(Throwable $e, Request $request = null)
    {
        $request = $request ?: request();

        // Global logging for ALL exceptions
        Log::error('Exception: ' . get_class($e), [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
        ]);

        // Handle API responses
        if ($request->expectsJson() || $request->is('api/*')) {
            return self::handleApiException($e);
        }

        // For web requests, let Laravel handle it normally
        return null;
    }

    private static function handleApiException(Throwable $e)
    {
        // Validation errors - pass exact errors to frontend
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // HTTP exceptions
        if ($e instanceof HttpException) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'An error occurred',
                'status' => $e->getStatusCode()
            ], $e->getStatusCode());
        }

        // All other errors
        $statusCode = 500;
        $message = app()->environment('production')
            ? 'An internal server error occurred'
            : $e->getMessage();

        return response()->json([
            'success' => false,
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
}
