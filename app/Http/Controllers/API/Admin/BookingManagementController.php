<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Requests\Admin\{ConfirmBookingRequest, ManualBookingRequest};
use App\Services\Admin\BookingManagementService;
use App\Services\PricingService;
use App\Repositories\BookingRepository;
use Illuminate\Http\{JsonResponse, Request};

class BookingManagementController extends Controller
{
    public function __construct(
        private BookingManagementService $bookingService,
        private BookingRepository $bookingRepo,
        private PricingService $pricingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $bookings = $this->bookingRepo->getWithFilters(
                $request->only(['status', 'type', 'date_from', 'date_to', 'search']),
                $request->get('per_page', 15)
            );

            return response()->json([
                'success' => true,
                'data' => BookingResource::collection($bookings->items()),
                'pagination' => [
                    'current_page' => $bookings->currentPage(),
                    'per_page' => $bookings->perPage(),
                    'total' => $bookings->total(),
                    'last_page' => $bookings->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $booking = $this->bookingRepo->findWithRelations($id, [
                'user',
                'roomType',
                'rooms',
                'addons.addonService',
                'documents',
                'spaBooking.spaPackage',
                'spayBooking.spayPackage'
            ]);

            return response()->json([
                'success' => true,
                'data' => new BookingResource($booking)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function confirm(int $id, ConfirmBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->confirmBooking($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully',
                'data' => new BookingResource($booking)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function cancel(int $id, Request $request): JsonResponse
    {
        $request->validate(['reason' => 'required|string|max:500']);

        try {
            $booking = $this->bookingService->cancelBooking($id, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully',
                'data' => new BookingResource($booking)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function createManualBooking(ManualBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->createManualBooking($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Manual booking created successfully',
                'data' => new BookingResource($booking)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            'special_requests' => 'sometimes|nullable|string|max:1000',
            'final_amount' => 'sometimes|numeric|min:0',
        ]);

        try {
            $booking = $this->bookingService->updateBooking($id, $request->only(['status', 'special_requests', 'final_amount']));

            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'data' => new BookingResource($booking)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function calculatePrice(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:hotel,spa,spay',
            'user_id' => 'nullable|exists:users,id',
        ]);

        try {
            $type = $request->type;
            $userId = $request->user_id;
            $isResident = false;

            // Check if user is resident (for spa/spay discounts)
            if ($userId) {
                $isResident = $this->pricingService->isUserResident($userId, $request->check_in_date ?? null);
            }

            $pricing = null;

            if ($type === 'hotel') {
                $request->validate([
                    'room_type_id' => 'required|exists:room_types,id',
                    'check_in_date' => 'required|date',
                    'check_out_date' => 'required|date|after:check_in_date',
                    'addons' => 'nullable|array',
                    'addons.*.addon_service_id' => 'required|exists:addon_services,id',
                    'addons.*.quantity' => 'required|integer|min:1',
                    'custom_monthly_discount' => 'nullable|numeric|min:0',
                ]);

                $pricing = $this->pricingService->calculateHotelBooking(
                    $request->room_type_id,
                    $request->check_in_date,
                    $request->check_out_date,
                    $request->addons ?? [],
                    $request->custom_monthly_discount
                );

            } elseif ($type === 'spa') {
                $request->validate([
                    'spa_package_id' => 'required|exists:spa_packages,id',
                    'addons' => 'nullable|array',
                    'addons.*.addon_service_id' => 'required|exists:addon_services,id',
                    'addons.*.quantity' => 'required|integer|min:1',
                    'is_resident' => 'nullable|boolean',
                ]);

                $isResident = $request->has('is_resident') ? (bool)$request->is_resident : $isResident;

                $pricing = $this->pricingService->calculateSpaBooking(
                    $request->spa_package_id,
                    $request->addons ?? [],
                    $isResident
                );

            } elseif ($type === 'spay') {
                $request->validate([
                    'spay_package_id' => 'required|exists:spay_packages,id',
                    'addons' => 'nullable|array',
                    'addons.*.addon_service_id' => 'required|exists:addon_services,id',
                    'addons.*.quantity' => 'required|integer|min:1',
                    'post_care_days' => 'nullable|integer|min:0',
                    'is_resident' => 'nullable|boolean',
                ]);

                $isResident = $request->has('is_resident') ? (bool)$request->is_resident : $isResident;

                $pricing = $this->pricingService->calculateSpayBooking(
                    $request->spay_package_id,
                    $request->addons ?? [],
                    $isResident,
                    $request->post_care_days
                );
            }

            return response()->json([
                'success' => true,
                'data' => $pricing
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
