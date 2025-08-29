<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddBookingAddonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'addon_service_id' => 'required|exists:addon_services,id',
            'quantity' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'addon_service_id.required' => 'Please select an addon service',
            'addon_service_id.exists' => 'Selected addon service is invalid',
            'quantity.required' => 'Quantity is required',
            'quantity.integer' => 'Quantity must be a valid number',
            'quantity.min' => 'Quantity must be at least 1',
            'quantity.max' => 'Maximum 10 quantity allowed',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
