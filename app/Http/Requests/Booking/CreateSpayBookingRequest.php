<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateSpayBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'spay_package_id' => 'required|exists:spay_packages,id',
            'procedure_date' => 'required|date|after_or_equal:today',
            'pet_name' => 'required|string|max:100',
            'pet_age' => 'nullable|string|max:50',
            'pet_weight' => 'nullable|numeric|min:0.1|max:50',
            'medical_notes' => 'nullable|string|max:1000',
            'special_requests' => 'nullable|string|max:500',
            'addons' => 'sometimes|array',
            'addons.*.addon_service_id' => 'required|exists:addon_services,id',
            'addons.*.quantity' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'spay_package_id.required' => 'Please select a spay/neuter package',
            'spay_package_id.exists' => 'Selected package is invalid',
            'procedure_date.required' => 'Procedure date is required',
            'procedure_date.after_or_equal' => 'Procedure date cannot be in the past',
            'pet_name.required' => 'Pet name is required',
            'pet_name.max' => 'Pet name cannot exceed 100 characters',
            'pet_age.max' => 'Pet age cannot exceed 50 characters',
            'pet_weight.numeric' => 'Pet weight must be a number',
            'pet_weight.min' => 'Pet weight must be at least 0.1 kg',
            'pet_weight.max' => 'Pet weight cannot exceed 50 kg',
            'medical_notes.max' => 'Medical notes cannot exceed 1000 characters',
            'addons.*.addon_service_id.required' => 'Addon service is required',
            'addons.*.addon_service_id.exists' => 'Selected addon service is invalid',
            'addons.*.quantity.required' => 'Addon quantity is required',
            'addons.*.quantity.min' => 'Addon quantity must be at least 1',
            'addons.*.quantity.max' => 'Maximum 10 quantity allowed per addon',
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
