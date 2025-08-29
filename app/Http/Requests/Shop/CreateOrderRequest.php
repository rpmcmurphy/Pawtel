<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'delivery_address' => 'required|string|max:500',
            'delivery_phone' => 'required|string|max:20',
            'delivery_notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_address.required' => 'Delivery address is required',
            'delivery_address.max' => 'Delivery address cannot exceed 500 characters',
            'delivery_phone.required' => 'Delivery phone number is required',
            'delivery_phone.max' => 'Phone number cannot exceed 20 characters',
            'delivery_notes.max' => 'Delivery notes cannot exceed 500 characters',
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
