<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'check_in_date' => 'sometimes|date|after_or_equal:today',
            'check_out_date' => 'sometimes|date|after:check_in_date',
            'special_requests' => 'sometimes|nullable|string|max:500',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('check_in_date') || $this->has('check_out_date')) {
                $checkInDate = $this->check_in_date ?: $this->booking->check_in_date;
                $checkOutDate = $this->check_out_date ?: $this->booking->check_out_date;

                $checkInDate = Carbon::parse($checkInDate);
                $checkOutDate = Carbon::parse($checkOutDate);
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
            'check_in_date.after_or_equal' => 'Check-in date cannot be in the past',
            'check_out_date.after' => 'Check-out date must be after check-in date',
            'special_requests.max' => 'Special requests cannot exceed 500 characters',
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
