<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ManualBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:hotel,spa,spay',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'room_type_id' => 'required_if:type,hotel|exists:room_types,id',
            'final_amount' => 'required|numeric|min:0',
            'manual_reference' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
