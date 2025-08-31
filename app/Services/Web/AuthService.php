<?php

namespace App\Services\Web;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthService extends ApiService
{
    public function login($credentials)
    {
        $response = $this->post('auth/login', $credentials);

        if ($response['success']) {
            $payload = $response['data']['data'] ?? [];

            Session::put('api_token', $payload['access_token'] ?? null);
            Session::put('user', $payload['user'] ?? null);

            return $response;
        }

        return $response;
    }

    public function register($userData)
    {
        $response = $this->post('auth/register', $userData);

        if ($response['success']) {
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
            Session::put('user', $response['data']['data']['user']);
        }

        return $response;
    }

    public function updateProfile($profileData)
    {
        $response = $this->put('auth/profile', $profileData);

        if ($response['success']) {
            Session::put('user', $response['data']['data']['user']);
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

        if (!$user) {
            return false;
        }

        // Get roles - handle both array format and string format
        $roles = $user['roles'] ?? [];

        // Handle different role data structures
        if (is_string($roles)) {
            // If roles is a single string
            $rolesArray = [$roles];
        } elseif (is_array($roles)) {
            // If roles is already an array
            if (empty($roles)) {
                return false;
            }

            // Check if it's an array of role objects or just role names
            $rolesArray = [];
            foreach ($roles as $role) {
                if (is_array($role) && isset($role['name'])) {
                    // Role object with 'name' field
                    $rolesArray[] = $role['name'];
                } elseif (is_string($role)) {
                    // Direct role name
                    $rolesArray[] = $role;
                }
            }
        } else {
            return false;
        }

        $isAdmin = in_array('admin', $rolesArray, true) || in_array('super_admin', $rolesArray, true);

        return $isAdmin;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        $user = $this->getUser();

        if (!$user) {
            return false;
        }

        $roles = $user['roles'] ?? [];

        // Handle different role data structures
        if (is_string($roles)) {
            return $roles === $roleName;
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (is_array($role) && isset($role['name'])) {
                    if ($role['name'] === $roleName) {
                        return true;
                    }
                } elseif (is_string($role)) {
                    if ($role === $roleName) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get all user roles as array of strings
     */
    public function getUserRoles()
    {
        $user = $this->getUser();

        if (!$user) {
            return [];
        }

        $roles = $user['roles'] ?? [];
        $rolesArray = [];

        if (is_string($roles)) {
            $rolesArray = [$roles];
        } elseif (is_array($roles)) {
            foreach ($roles as $role) {
                if (is_array($role) && isset($role['name'])) {
                    $rolesArray[] = $role['name'];
                } elseif (is_string($role)) {
                    $rolesArray[] = $role;
                }
            }
        }

        return $rolesArray;
    }
}
