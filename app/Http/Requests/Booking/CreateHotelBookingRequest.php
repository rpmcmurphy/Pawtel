<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class CreateHotelBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'special_requests' => 'nullable|string|max:500',
            'addons' => 'sometimes|array',
            'addons.*.addon_service_id' => 'required|exists:addon_services,id',
            'addons.*.quantity' => 'required|integer|min:1|max:10',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->check_in_date && $this->check_out_date) {
                $checkInDate = Carbon::parse($this->check_in_date);
                $checkOutDate = Carbon::parse($this->check_out_date);
                $totalDays = $checkInDate->diffInDays($checkOutDate) + 1;

                if ($totalDays < 3) {
                    $validator->errors()->add('check_out_date', 'Minimum stay is 3 days.');
                }

                if ($totalDays > 365) {
                    $validator->errors()->add('check_out_date', 'Maximum stay is 365 days.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'room_type_id.required' => 'Please select a room type',
            'room_type_id.exists' => 'Selected room type is invalid',
            'check_in_date.required' => 'Check-in date is required',
            'check_in_date.after_or_equal' => 'Check-in date cannot be in the past',
            'check_out_date.required' => 'Check-out date is required',
            'check_out_date.after' => 'Check-out date must be after check-in date',
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
