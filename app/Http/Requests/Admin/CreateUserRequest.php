<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:20',
            'role' => 'required|in:admin,customer',
        ];
    }
}
