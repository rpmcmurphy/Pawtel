<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Requests\Admin\{ConfirmBookingRequest, ManualBookingRequest};
use App\Services\Admin\BookingManagementService;
use App\Repositories\BookingRepository;
use Illuminate\Http\{JsonResponse, Request};

class BookingManagementController extends Controller
{
    public function __construct(
        private BookingManagementService $bookingService,
        private BookingRepository $bookingRepo
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
}
