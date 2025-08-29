<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'Quantity is required',
            'quantity.integer' => 'Quantity must be a valid number',
            'quantity.min' => 'Quantity must be at least 1',
            'quantity.max' => 'Maximum 10 items allowed per product',
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
