<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|unique:rooms,room_number|max:20',
            'floor' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
