<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateSpaBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'spa_package_id' => 'required|exists:spa_packages,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
            'special_requests' => 'nullable|string|max:500',
            'addons' => 'sometimes|array',
            'addons.*.addon_service_id' => 'required|exists:addon_services,id',
            'addons.*.quantity' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'spa_package_id.required' => 'Please select a spa package',
            'spa_package_id.exists' => 'Selected spa package is invalid',
            'appointment_date.required' => 'Appointment date is required',
            'appointment_date.after_or_equal' => 'Appointment date cannot be in the past',
            'appointment_time.required' => 'Appointment time is required',
            'appointment_time.date_format' => 'Invalid time format. Use HH:MM format',
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
