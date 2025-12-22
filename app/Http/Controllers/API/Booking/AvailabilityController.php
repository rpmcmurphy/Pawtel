<?php

namespace App\Http\Controllers\API\Booking;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\SpaPackage;
use App\Models\SpayPackage;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function __construct(
        private AvailabilityService $availabilityService
    ) {}

    public function roomTypes(): JsonResponse
    {
        try {
            $roomTypes = RoomType::active()
                ->orderBy('sort_order')
                ->get()
                ->map(function ($roomType) {
                    return [
                        'id' => $roomType->id,
                        'name' => $roomType->name,
                        'slug' => $roomType->slug,
                        'base_daily_rate' => $roomType->base_daily_rate,
                        'rate_7plus_days' => $roomType->rate_7plus_days,
                        'rate_10plus_days' => $roomType->rate_10plus_days,
                        'monthly_package_price' => $roomType->monthly_package_price,
                        'monthly_custom_discount_enabled' => $roomType->monthly_custom_discount_enabled,
                        'max_capacity' => $roomType->max_capacity,
                        'amenities' => $roomType->amenities,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $roomTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch room types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function check(Request $request): JsonResponse
    {
        // Support both GET (query params) and POST (JSON body)
        $roomTypeId = $request->input('room_type_id') ?? $request->get('room_type_id');
        $checkInDate = $request->input('check_in_date') ?? $request->get('check_in_date');
        $checkOutDate = $request->input('check_out_date') ?? $request->get('check_out_date');
        
        $request->merge([
            'room_type_id' => $roomTypeId,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
        ]);
        
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        try {
            $checkInDate = $request->check_in_date;
            $checkOutDate = $request->check_out_date;
            $roomTypeId = $request->room_type_id;

            // Calculate total days
            $totalDays = \Carbon\Carbon::parse($checkInDate)
                ->diffInDays(\Carbon\Carbon::parse($checkOutDate)) + 1;

            // Check minimum stay requirement
            if ($totalDays < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimum stay is 3 days',
                    'data' => [
                        'available' => false,
                        'reason' => 'minimum_stay_not_met',
                        'minimum_stay_days' => 3,
                        'selected_days' => $totalDays
                    ]
                ]);
            }

            $availability = $this->availabilityService->checkHotelAvailability(
                $roomTypeId,
                $checkInDate,
                $checkOutDate
            );

            $roomType = RoomType::find($roomTypeId);
            $pricing = $this->availabilityService->calculatePricing(
                $roomType,
                $totalDays
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'available' => $availability,
                    'total_days' => $totalDays,
                    'room_type' => [
                        'id' => $roomType->id,
                        'name' => $roomType->name,
                        'max_capacity' => $roomType->max_capacity,
                    ],
                    'pricing' => $pricing,
                    'available_rooms' => $availability ?
                        $roomType->getAvailableRoomsCount($checkInDate) : 0,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function spaPackages(): JsonResponse
    {
        try {
            $packages = SpaPackage::active()
                ->orderBy('sort_order')
                ->get()
                ->map(function ($package) {
                    return [
                        'id' => $package->id,
                        'name' => $package->name,
                        'slug' => $package->slug,
                        'description' => $package->description,
                        'duration_minutes' => $package->duration_minutes,
                        'price' => $package->price,
                        'max_daily_bookings' => $package->max_daily_bookings,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $packages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch spa packages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function spaSlots(Request $request): JsonResponse
    {
        $request->validate([
            'spa_package_id' => 'required|exists:spa_packages,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        try {
            $packageId = $request->spa_package_id;
            $date = $request->date;

            $slots = $this->availabilityService->getSpaAvailableSlots($packageId, $date);

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $date,
                    'available_slots' => $slots,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch spa slots',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function spayPackages(): JsonResponse
    {
        try {
            $packages = SpayPackage::active()
                ->get()
                ->map(function ($package) {
                    return [
                        'id' => $package->id,
                        'name' => $package->name,
                        'type' => $package->type,
                        'description' => $package->description,
                        'price' => $package->price,
                        'post_care_days' => $package->post_care_days,
                        'max_daily_slots' => $package->max_daily_slots,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $packages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch spay packages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function spaySlots(Request $request): JsonResponse
    {
        $request->validate([
            'spay_package_id' => 'required|exists:spay_packages,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        try {
            $packageId = $request->spay_package_id;
            $date = $request->date;

            $availability = $this->availabilityService->getSpayAvailability($packageId, $date);

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $date,
                    'available' => $availability['available'],
                    'available_slots' => $availability['available_slots'],
                    'total_slots' => $availability['total_slots'],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch spay slots',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
