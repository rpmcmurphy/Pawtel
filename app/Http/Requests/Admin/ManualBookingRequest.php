<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ManualBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $type = $this->input('type');
        
        // Remove empty type-specific fields that aren't relevant to the selected type
        if ($type === 'hotel') {
            // Remove spa and spay fields if empty
            if (empty($this->input('spa_package_id'))) {
                $this->merge(['spa_package_id' => null]);
            }
            if (empty($this->input('spay_package_id'))) {
                $this->merge(['spay_package_id' => null]);
            }
        } elseif ($type === 'spa') {
            // Remove hotel and spay fields if empty
            if (empty($this->input('room_type_id'))) {
                $this->merge(['room_type_id' => null]);
            }
            if (empty($this->input('spay_package_id'))) {
                $this->merge(['spay_package_id' => null]);
            }
        } elseif ($type === 'spay') {
            // Remove hotel and spa fields if empty
            if (empty($this->input('room_type_id'))) {
                $this->merge(['room_type_id' => null]);
            }
            if (empty($this->input('spa_package_id'))) {
                $this->merge(['spa_package_id' => null]);
            }
        }
    }

    public function rules(): array
    {
        $type = $this->input('type');
        
        $rules = [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:hotel,spa,spay',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'final_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'manual_reference' => 'required|string|max:255',
            'special_requests' => 'nullable|string|max:1000',
            'addons' => 'sometimes|array',
            'addons.*.addon_service_id' => 'required|exists:addon_services,id',
            'addons.*.quantity' => 'required|integer|min:1|max:10',
            'status' => 'sometimes|in:pending,confirmed',
            'send_confirmation' => 'sometimes|boolean',
            'custom_monthly_discount' => 'nullable|numeric|min:0',
        ];
        
        // Type-specific required fields - only validate exists when field is required
        if ($type === 'hotel') {
            $rules['room_type_id'] = 'required|exists:room_types,id';
            // Make other type fields nullable so they don't interfere
            $rules['spa_package_id'] = 'nullable';
            $rules['spay_package_id'] = 'nullable';
        } elseif ($type === 'spa') {
            $rules['spa_package_id'] = 'required|exists:spa_packages,id';
            // Make other type fields nullable so they don't interfere
            $rules['room_type_id'] = 'nullable';
            $rules['spay_package_id'] = 'nullable';
            // Spa-specific fields
            $rules['appointment_time'] = 'nullable|date_format:H:i';
            $rules['notes'] = 'nullable|string|max:500';
        } elseif ($type === 'spay') {
            $rules['spay_package_id'] = 'required|exists:spay_packages,id';
            // Make other type fields nullable so they don't interfere
            $rules['room_type_id'] = 'nullable';
            $rules['spa_package_id'] = 'nullable';
            // Spay-specific fields
            $rules['pet_name'] = 'nullable|string|max:100';
            $rules['pet_age'] = 'nullable|string|max:50';
            $rules['pet_weight'] = 'nullable|numeric|min:0.1|max:50';
            $rules['medical_notes'] = 'nullable|string|max:1000';
            $rules['post_care_days'] = 'nullable|integer|min:0|max:30';
        } else {
            // If type is not set yet, make all type-specific fields nullable
            $rules['room_type_id'] = 'nullable';
            $rules['spa_package_id'] = 'nullable';
            $rules['spay_package_id'] = 'nullable';
        }
        
        return $rules;
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