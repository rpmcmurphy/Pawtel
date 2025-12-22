<?php

namespace App\Http\Controllers\API\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateHotelBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
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

class HotelBookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private AvailabilityService $availabilityService,
        private PricingService $pricingService,
        private NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $bookings = $user->bookings()
                ->ofType('hotel')
                ->with(['roomType', 'addons.addonService', 'documents'])
                ->when($request->status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($request->from_date, function ($query, $fromDate) {
                    return $query->where('check_in_date', '>=', $fromDate);
                })
                ->when($request->to_date, function ($query, $toDate) {
                    return $query->where('check_out_date', '<=', $toDate);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

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

    public function show(string $bookingNumber): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->with(['roomType', 'rooms', 'addons.addonService', 'documents'])
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

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

    public function store(CreateHotelBookingRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Check availability
            $isAvailable = $this->availabilityService->checkHotelAvailability(
                $request->room_type_id,
                $request->check_in_date,
                $request->check_out_date
            );

            if (!$isAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'No vacancy available for the selected dates'
                ], 400);
            }

            // Check if user is a resident (has active hotel booking)
            $isResident = $this->pricingService->isUserResident($user->id, $request->check_in_date);

            // Calculate pricing
            $pricing = $this->pricingService->calculateHotelBooking(
                $request->room_type_id,
                $request->check_in_date,
                $request->check_out_date,
                $request->addons ?? [],
                $request->custom_monthly_discount ?? null
            );

            // Create booking
            $booking = $this->bookingService->createHotelBooking([
                'user_id' => $user->id,
                'type' => 'hotel',
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'room_type_id' => $request->room_type_id,
                'total_days' => $pricing['total_days'],
                'total_amount' => $pricing['total_amount'],
                'discount_amount' => $pricing['discount_amount'],
                'final_amount' => $pricing['final_amount'],
                'special_requests' => $request->special_requests,
                'is_resident' => $isResident,
                'addons' => $request->addons ?? [],
            ]);

            // Send confirmation email
            $this->notificationService->sendBookingConfirmation($booking);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hotel booking created successfully',
                'data' => new BookingResource($booking->load(['roomType', 'addons.addonService']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(string $bookingNumber, UpdateBookingRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->where('status', 'pending')
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found or cannot be modified'
                ], 404);
            }

            DB::beginTransaction();

            $updateData = [];
            $recalculatePrice = false;

            if ($request->has('check_in_date') || $request->has('check_out_date')) {
                $checkInDate = $request->check_in_date ?? $booking->check_in_date;
                $checkOutDate = $request->check_out_date ?? $booking->check_out_date;

                // Check availability for new dates
                $isAvailable = $this->availabilityService->checkHotelAvailability(
                    $booking->room_type_id,
                    $checkInDate,
                    $checkOutDate,
                    $booking->id
                );

                if (!$isAvailable) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No vacancy available for the selected dates'
                    ], 400);
                }

                $updateData['check_in_date'] = $checkInDate;
                $updateData['check_out_date'] = $checkOutDate;
                $recalculatePrice = true;
            }

            if ($request->has('special_requests')) {
                $updateData['special_requests'] = $request->special_requests;
            }

            if ($recalculatePrice) {
                // Check if user is still a resident
                $isResident = $this->pricingService->isUserResident($user->id, $updateData['check_in_date']);
                
                $pricing = $this->pricingService->calculateHotelBooking(
                    $booking->room_type_id,
                    $updateData['check_in_date'],
                    $updateData['check_out_date'],
                    $booking->addons->map(function ($addon) {
                        return [
                            'addon_service_id' => $addon->addon_service_id,
                            'quantity' => $addon->quantity
                        ];
                    })->toArray()
                );

                $updateData = array_merge($updateData, [
                    'total_days' => $pricing['total_days'],
                    'total_amount' => $pricing['total_amount'],
                    'discount_amount' => $pricing['discount_amount'],
                    'final_amount' => $pricing['final_amount'],
                    'is_resident' => $isResident,
                ]);
            }

            $booking->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'data' => new BookingResource($booking->load(['roomType', 'addons.addonService']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancel(string $bookingNumber, Request $request): JsonResponse
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found or cannot be cancelled'
                ], 404);
            }

            if (!$booking->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking cannot be cancelled (check-in date has passed)'
                ], 400);
            }

            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
            ]);

            // Send cancellation email
            $this->notificationService->sendBookingCancellation($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully',
                'data' => new BookingResource($booking)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function invoice(string $bookingNumber): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->with(['roomType', 'addons.addonService', 'user'])
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $invoice = $this->bookingService->generateInvoice($booking);

            return response()->json([
                'success' => true,
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
