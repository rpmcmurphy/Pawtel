<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->route('id'),
            'phone' => 'required|string|unique:users,phone,' . $this->route('id'),
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:20',
        ];
    }
}
