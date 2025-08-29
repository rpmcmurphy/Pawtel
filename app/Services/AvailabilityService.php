<?php

namespace App\Services;

use App\Models\RoomType;
use App\Models\Booking;
use App\Models\BlockedDate;
use App\Models\SpaPackage;
use App\Models\SpaBooking;
use App\Models\SpayPackage;
use App\Models\SpayBooking;
use Carbon\Carbon;

class AvailabilityService
{
    public function checkHotelAvailability(
        int $roomTypeId,
        string $checkInDate,
        string $checkOutDate,
        ?int $excludeBookingId = null
    ): bool {
        $roomType = RoomType::find($roomTypeId);
        if (!$roomType || $roomType->status !== 'active') {
            return false;
        }

        $startDate = Carbon::parse($checkInDate);
        $endDate = Carbon::parse($checkOutDate);

        // Check for blocked dates
        $blockedDatesCount = BlockedDate::where('room_type_id', $roomTypeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->when($excludeBookingId, function ($query, $excludeBookingId) {
                return $query->where(function ($q) use ($excludeBookingId) {
                    $q->where('reference_type', '!=', 'booking')
                        ->orWhere('reference_id', '!=', $excludeBookingId);
                });
            })
            ->count();

        if ($blockedDatesCount > 0) {
            return false;
        }

        // Check for overlapping bookings
        $overlappingBookings = Booking::where('room_type_id', $roomTypeId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in_date', [$startDate, $endDate])
                    ->orWhereBetween('check_out_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('check_in_date', '<=', $startDate)
                            ->where('check_out_date', '>=', $endDate);
                    });
            })
            ->when($excludeBookingId, function ($query, $excludeBookingId) {
                return $query->where('id', '!=', $excludeBookingId);
            })
            ->count();

        // Check if there are available rooms
        $totalCapacity = $roomType->rooms()->where('status', 'available')->count();
        return $overlappingBookings < $totalCapacity;
    }

    public function checkSpaAvailability(
        int $spaPackageId,
        string $appointmentDate,
        string $appointmentTime,
        ?int $excludeBookingId = null
    ): bool {
        $spaPackage = SpaPackage::find($spaPackageId);
        if (!$spaPackage || $spaPackage->status !== 'active') {
            return false;
        }

        $bookedSlots = SpaBooking::where('spa_package_id', $spaPackageId)
            ->where('appointment_date', $appointmentDate)
            ->where('appointment_time', $appointmentTime)
            ->whereHas('booking', function ($query) use ($excludeBookingId) {
                $query->where('status', '!=', 'cancelled');
                if ($excludeBookingId) {
                    $query->where('id', '!=', $excludeBookingId);
                }
            })
            ->count();

        return $bookedSlots < $spaPackage->max_daily_bookings;
    }

    public function checkSpayAvailability(
        int $spayPackageId,
        string $procedureDate,
        ?int $excludeBookingId = null
    ): bool {
        $spayPackage = SpayPackage::find($spayPackageId);
        if (!$spayPackage || $spayPackage->status !== 'active') {
            return false;
        }

        $bookedSlots = SpayBooking::where('spay_package_id', $spayPackageId)
            ->where('procedure_date', $procedureDate)
            ->whereHas('booking', function ($query) use ($excludeBookingId) {
                $query->where('status', '!=', 'cancelled');
                if ($excludeBookingId) {
                    $query->where('id', '!=', $excludeBookingId);
                }
            })
            ->count();

        return $bookedSlots < $spayPackage->max_daily_slots;
    }

    public function getSpaAvailableSlots(int $spaPackageId, string $date): array
    {
        $spaPackage = SpaPackage::find($spaPackageId);
        if (!$spaPackage) {
            return [];
        }

        // Define time slots (9 AM to 5 PM, 1-hour slots)
        $timeSlots = [
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00'
        ];

        $availableSlots = [];

        foreach ($timeSlots as $time) {
            $isAvailable = $this->checkSpaAvailability($spaPackageId, $date, $time);

            if ($isAvailable) {
                $availableSlots[] = [
                    'time' => $time,
                    'formatted_time' => Carbon::createFromFormat('H:i', $time)->format('g:i A'),
                ];
            }
        }

        return $availableSlots;
    }

    public function getSpayAvailability(int $spayPackageId, string $date): array
    {
        $spayPackage = SpayPackage::find($spayPackageId);
        if (!$spayPackage) {
            return [
                'available' => false,
                'available_slots' => 0,
                'total_slots' => 0,
            ];
        }

        $bookedSlots = SpayBooking::where('spay_package_id', $spayPackageId)
            ->where('procedure_date', $date)
            ->whereHas('booking', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->count();

        $availableSlots = max(0, $spayPackage->max_daily_slots - $bookedSlots);

        return [
            'available' => $availableSlots > 0,
            'available_slots' => $availableSlots,
            'total_slots' => $spayPackage->max_daily_slots,
        ];
    }

    public function calculatePricing(RoomType $roomType, int $totalDays): array
    {
        $basePrice = $roomType->getPriceForDuration($totalDays);

        return [
            'base_daily_rate' => $roomType->base_daily_rate,
            'weekly_rate' => $roomType->weekly_rate,
            'ten_day_rate' => $roomType->ten_day_rate,
            'monthly_rate' => $roomType->monthly_rate,
            'total_days' => $totalDays,
            'applicable_price' => $basePrice,
            'price_type' => $this->getPriceType($totalDays),
        ];
    }

    private function getPriceType(int $totalDays): string
    {
        if ($totalDays >= 30) {
            return 'monthly';
        } elseif ($totalDays >= 10) {
            return 'ten_day';
        } elseif ($totalDays >= 7) {
            return 'weekly';
        } else {
            return 'daily';
        }
    }
}
