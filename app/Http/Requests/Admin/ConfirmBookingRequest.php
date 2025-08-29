<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'room_assignments' => 'nullable|array',
            'room_assignments.*' => 'exists:rooms,id',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
