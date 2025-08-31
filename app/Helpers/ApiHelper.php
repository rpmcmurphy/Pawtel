<?php

namespace App\Helpers;

class ApiHelper
{
    public static function formatResponse($response, $success = true, $message = '')
    {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $response
        ];
    }

    public static function handleApiError($error)
    {
        $status = $error->getCode() ?: 500;
        $message = 'An error occurred';

        if (method_exists($error, 'response') && $error->response) {
            $response = json_decode($error->response->getBody(), true);
            $message = $response['message'] ?? $message;
            $status = $error->response->getStatusCode();
        }

        return [
            'success' => false,
            'message' => $message,
            'status' => $status
        ];
    }

    public static function getAuthHeaders()
    {
        $token = session('api_token');

        return $token ? [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ] : [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public static function formatCurrency($amount)
    {
        return '৳' . number_format($amount, 2);
    }

    public static function formatDate($date, $format = 'M d, Y')
    {
        return \Carbon\Carbon::parse($date)->format($format);
    }
}
