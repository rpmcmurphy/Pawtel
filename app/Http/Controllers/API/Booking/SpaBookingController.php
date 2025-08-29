<?php

namespace App\Http\Controllers\API\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateSpaBookingRequest;
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

class SpaBookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private AvailabilityService $availabilityService,
        private PricingService $pricingService,
        private NotificationService $notificationService
    ) {}

    public function store(CreateSpaBookingRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Check spa availability
            $isAvailable = $this->availabilityService->checkSpaAvailability(
                $request->spa_package_id,
                $request->appointment_date,
                $request->appointment_time
            );

            if (!$isAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'No slots available for the selected date and time'
                ], 400);
            }

            // Calculate pricing
            $pricing = $this->pricingService->calculateSpaBooking(
                $request->spa_package_id,
                $request->addons ?? []
            );

            // Create booking
            $booking = $this->bookingService->createSpaBooking([
                'user_id' => $user->id,
                'type' => 'spa',
                'check_in_date' => $request->appointment_date,
                'check_out_date' => $request->appointment_date,
                'total_days' => 1,
                'total_amount' => $pricing['total_amount'],
                'discount_amount' => $pricing['discount_amount'],
                'final_amount' => $pricing['final_amount'],
                'special_requests' => $request->special_requests,
                'addons' => $request->addons ?? [],
                'spa_details' => [
                    'spa_package_id' => $request->spa_package_id,
                    'appointment_date' => $request->appointment_date,
                    'appointment_time' => $request->appointment_time,
                    'notes' => $request->notes,
                ]
            ]);

            // Send confirmation email
            $this->notificationService->sendBookingConfirmation($booking);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Spa booking created successfully',
                'data' => new BookingResource($booking->load(['spaBooking.spaPackage', 'addons.addonService']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create spa booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(string $bookingNumber, Request $request): JsonResponse
    {
        $request->validate([
            'appointment_date' => 'sometimes|date|after_or_equal:today',
            'appointment_time' => 'sometimes|date_format:H:i',
            'notes' => 'sometimes|string|max:500',
            'special_requests' => 'sometimes|string|max:500',
        ]);

        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->where('type', 'spa')
                ->where('status', 'pending')
                ->with('spaBooking')
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spa booking not found or cannot be modified'
                ], 404);
            }

            DB::beginTransaction();

            $updateBookingData = [];
            $updateSpaData = [];

            if ($request->has('appointment_date')) {
                // Check availability for new date
                $isAvailable = $this->availabilityService->checkSpaAvailability(
                    $booking->spaBooking->spa_package_id,
                    $request->appointment_date,
                    $request->appointment_time ?? $booking->spaBooking->appointment_time,
                    $booking->id
                );

                if (!$isAvailable) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No slots available for the selected date and time'
                    ], 400);
                }

                $updateBookingData['check_in_date'] = $request->appointment_date;
                $updateBookingData['check_out_date'] = $request->appointment_date;
                $updateSpaData['appointment_date'] = $request->appointment_date;
            }

            if ($request->has('appointment_time')) {
                $updateSpaData['appointment_time'] = $request->appointment_time;
            }

            if ($request->has('notes')) {
                $updateSpaData['notes'] = $request->notes;
            }

            if ($request->has('special_requests')) {
                $updateBookingData['special_requests'] = $request->special_requests;
            }

            if (!empty($updateBookingData)) {
                $booking->update($updateBookingData);
            }

            if (!empty($updateSpaData)) {
                $booking->spaBooking->update($updateSpaData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Spa booking updated successfully',
                'data' => new BookingResource($booking->load(['spaBooking.spaPackage', 'addons.addonService']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update spa booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
