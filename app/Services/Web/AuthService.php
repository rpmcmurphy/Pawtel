<?php

namespace App\Services\Web;

use Illuminate\Support\Facades\Session;

class AuthService extends ApiService
{
    public function login($credentials)
    {
        // $csrfResponse = $this->get('sanctum/csrf-cookie');

        // if (!$csrfResponse['success']) {
        //     return $csrfResponse;
        // }

        $response = $this->post('auth/login', $credentials);

        if ($response['success']) {
            // $data = $response['data'];

            error_log('Login Response: ' . print_r($response, true));
            $payload = $response['data']['data'] ?? [];

            Session::put('api_token', $payload['access_token'] ?? null);
            Session::put('user', $payload['user'] ?? null);

            // Store auth data in session
            // Session::put('api_token', $data['token']);
            // Session::put('user', $data['user']);

            return $response;
        }

        return $response;
    }

    public function register($userData)
    {
        $response = $this->post('auth/register', $userData);

        if ($response['success']) {
            // $data = $response['data'];

            // // Auto login after registration
            // Session::put('api_token', $data['token']);
            // Session::put('user', $data['user']);

            $payload = $response['data']['data'] ?? [];

            Session::put('api_token', $payload['access_token'] ?? null);
            Session::put('user', $payload['user'] ?? null);
        }

        return $response;
    }

    public function logout()
    {
        $response = $this->post('auth/logout');

        // Clear session regardless of API response
        Session::forget(['api_token', 'user']);

        return $response;
    }

    public function getProfile()
    {
        $response = $this->get('auth/profile');

        if ($response['success']) {
            // Update session with fresh user data
            Session::put('user', $response['data']['user']);
        }

        return $response;
    }

    public function updateProfile($profileData)
    {
        $response = $this->put('auth/profile', $profileData);

        if ($response['success']) {
            Session::put('user', $response['data']['user']);
        }

        return $response;
    }

    public function forgotPassword($email)
    {
        return $this->post('auth/forgot-password', ['email' => $email]);
    }

    public function resetPassword($data)
    {
        return $this->post('auth/reset-password', $data);
    }

    public function isAuthenticated()
    {
        return Session::has('api_token') && Session::has('user');
    }

    public function getUser()
    {
        return Session::get('user');
    }

    public function getUserId()
    {
        $user = $this->getUser();
        return $user['id'] ?? null;
    }

    public function isAdmin()
    {
        $user = $this->getUser();
        $roles = $user['roles'] ?? [];

        // return isset($user['role']) && in_array($user['role'], ['admin', 'super_admin']);
        return in_array('admin', $roles, true) || in_array('super_admin', $roles, true);
    }
}
