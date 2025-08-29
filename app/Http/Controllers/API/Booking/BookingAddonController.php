<?php

namespace App\Http\Controllers\API\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\AddBookingAddonRequest;
use App\Services\BookingAddonService;
use App\Repositories\BookingRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BookingAddonController extends Controller
{
    public function __construct(
        private BookingAddonService $addonService,
        private BookingRepository $bookingRepo
    ) {}

    public function index(string $bookingNumber): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->with('addons.addonService')
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $addons = $this->addonService->getBookingAddons($booking->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'booking_number' => $booking->booking_number,
                    'addons' => $addons,
                    'addons_count' => count($addons),
                    'addons_total' => array_sum(array_column($addons, 'total_price')),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch addons',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(string $bookingNumber, AddBookingAddonRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found or cannot add addons'
                ], 404);
            }

            $result = $this->addonService->addAddonToBooking(
                $booking->id,
                $request->addon_service_id,
                $request->quantity
            );

            return response()->json([
                'success' => true,
                'message' => 'Addon added successfully',
                'data' => $result
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add addon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(string $bookingNumber, int $addonId, AddBookingAddonRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $result = $this->addonService->updateBookingAddon(
                $booking->id,
                $addonId,
                $request->quantity
            );

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Addon not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Addon updated successfully',
                'data' => $result
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update addon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $bookingNumber, int $addonId): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $result = $this->addonService->removeAddonFromBooking($booking->id, $addonId);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Addon not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Addon removed successfully',
                'data' => [
                    'booking_total' => $result['new_booking_total'],
                    'addons_total' => $result['new_addons_total'],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove addon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableAddons(string $bookingNumber): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $availableAddons = $this->addonService->getAvailableAddons($booking->type);

            return response()->json([
                'success' => true,
                'data' => $availableAddons
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch available addons',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
