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
        $status = 500;
        $message = 'An error occurred';
        $errors = [];

        if (method_exists($error, 'getResponse') && $error->getResponse()) {
            $response = $error->getResponse();
            $status = $response->getStatusCode();
            $body = json_decode($response->getBody()->getContents(), true);
            
            if ($body) {
                $message = $body['message'] ?? $message;
                $errors = $body['errors'] ?? [];
            }
        } elseif (method_exists($error, 'response') && $error->response) {
            $response = json_decode($error->response->getBody(), true);
            $message = $response['message'] ?? $message;
            $errors = $response['errors'] ?? [];
            $status = $error->response->getStatusCode();
        }

        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'status' => $status,
            'data' => [
                'message' => $message,
                'errors' => $errors
            ]
        ];
    }

    public static function getAuthHeaders()
    {
        $token = session('api_token');
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        // Add CSRF token for same-origin requests
        if (request() && request()->hasSession()) {
            $csrfToken = csrf_token();
            if ($csrfToken) {
                $headers['X-CSRF-TOKEN'] = $csrfToken;
            }
        }

        return $headers;
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