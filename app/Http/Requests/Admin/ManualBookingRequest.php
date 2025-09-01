<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_type_id' => 'required_if:type,hotel|exists:room_types,id',
            'spa_package_id' => 'required_if:type,spa|exists:spa_packages,id',
            'spay_package_id' => 'required_if:type,spay|exists:spay_packages,id',
            'final_amount' => 'required|numeric|min:0',
            'manual_reference' => 'required|string|max:255',
            'special_requests' => 'nullable|string|max:1000',
            'addons' => 'sometimes|array',
            'addons.*.addon_service_id' => 'required|exists:addon_services,id',
            'addons.*.quantity' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Customer is required',
            'user_id.exists' => 'Selected customer is invalid',
            'type.required' => 'Booking type is required',
            'type.in' => 'Invalid booking type selected',
            'check_in_date.required' => 'Check-in date is required',
            'check_in_date.after_or_equal' => 'Check-in date cannot be in the past',
            'check_out_date.required' => 'Check-out date is required',
            'check_out_date.after' => 'Check-out date must be after check-in date',
            'room_type_id.required_if' => 'Room type is required for hotel bookings',
            'spa_package_id.required_if' => 'Spa package is required for spa bookings',
            'spay_package_id.required_if' => 'Spay package is required for spay bookings',
            'final_amount.required' => 'Final amount is required',
            'final_amount.numeric' => 'Final amount must be a number',
            'final_amount.min' => 'Final amount cannot be negative',
            'manual_reference.required' => 'Manual reference is required',
            'manual_reference.max' => 'Manual reference cannot exceed 255 characters',
            'special_requests.max' => 'Special requests cannot exceed 1000 characters',
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