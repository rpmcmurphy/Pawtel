<?php

namespace App\Http\Controllers\API\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateSpayBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SpayNeuterBookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private AvailabilityService $availabilityService,
        private PricingService $pricingService,
        private NotificationService $notificationService
    ) {}

    public function store(CreateSpayBookingRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Check availability
            $isAvailable = $this->availabilityService->checkSpayAvailability(
                $request->spay_package_id,
                $request->procedure_date
            );

            if (!$isAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'No slots available for the selected date'
                ], 400);
            }

            // Calculate pricing
            $pricing = $this->pricingService->calculateSpayBooking(
                $request->spay_package_id,
                $request->addons ?? []
            );

            // Create booking
            $booking = $this->bookingService->createSpayBooking([
                'user_id' => $user->id,
                'type' => 'spay',
                'check_in_date' => $request->procedure_date,
                'check_out_date' => $request->procedure_date,
                'total_days' => 1,
                'total_amount' => $pricing['total_amount'],
                'discount_amount' => $pricing['discount_amount'],
                'final_amount' => $pricing['final_amount'],
                'special_requests' => $request->special_requests,
                'addons' => $request->addons ?? [],
                'spay_details' => [
                    'spay_package_id' => $request->spay_package_id,
                    'procedure_date' => $request->procedure_date,
                    'pet_name' => $request->pet_name,
                    'pet_age' => $request->pet_age,
                    'pet_weight' => $request->pet_weight,
                    'medical_notes' => $request->medical_notes,
                ]
            ]);

            // Send confirmation email
            $this->notificationService->sendBookingConfirmation($booking);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Spay/Neuter booking created successfully',
                'data' => new BookingResource($booking->load(['spayBooking.spayPackage', 'addons.addonService']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create spay/neuter booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(string $bookingNumber, Request $request): JsonResponse
    {
        $request->validate([
            'procedure_date' => 'sometimes|date|after_or_equal:today',
            'pet_name' => 'sometimes|string|max:100',
            'pet_age' => 'sometimes|string|max:50',
            'pet_weight' => 'sometimes|numeric|min:0.1|max:50',
            'medical_notes' => 'sometimes|string|max:1000',
            'special_requests' => 'sometimes|string|max:500',
        ]);

        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->where('type', 'spay')
                ->where('status', 'pending')
                ->with('spayBooking')
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spay/Neuter booking not found or cannot be modified'
                ], 404);
            }

            DB::beginTransaction();

            $updateBookingData = [];
            $updateSpayData = [];

            if ($request->has('procedure_date')) {
                // Check availability for new date
                $isAvailable = $this->availabilityService->checkSpayAvailability(
                    $booking->spayBooking->spay_package_id,
                    $request->procedure_date,
                    $booking->id
                );

                if (!$isAvailable) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No slots available for the selected date'
                    ], 400);
                }

                $updateBookingData['check_in_date'] = $request->procedure_date;
                $updateBookingData['check_out_date'] = $request->procedure_date;
                $updateSpayData['procedure_date'] = $request->procedure_date;
            }

            foreach (['pet_name', 'pet_age', 'pet_weight', 'medical_notes'] as $field) {
                if ($request->has($field)) {
                    $updateSpayData[$field] = $request->$field;
                }
            }

            if ($request->has('special_requests')) {
                $updateBookingData['special_requests'] = $request->special_requests;
            }

            if (!empty($updateBookingData)) {
                $booking->update($updateBookingData);
            }

            if (!empty($updateSpayData)) {
                $booking->spayBooking->update($updateSpayData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Spay/Neuter booking updated successfully',
                'data' => new BookingResource($booking->load(['spayBooking.spayPackage', 'addons.addonService']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update spay/neuter booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
