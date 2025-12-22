<?php

namespace App\Services;

use App\Models\RoomType;
use App\Models\SpaPackage;
use App\Models\SpayPackage;
use App\Models\AddonService;
use App\Models\Booking;
use Carbon\Carbon;

class PricingService
{
    /**
     * Check if a user is a resident (has active hotel booking)
     */
    public function isUserResident(int $userId, ?string $checkDate = null): bool
    {
        $date = $checkDate ? Carbon::parse($checkDate) : Carbon::now();
        
        return Booking::where('user_id', $userId)
            ->where('type', 'hotel')
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in_date', '<=', $date)
            ->where('check_out_date', '>=', $date)
            ->exists();
    }

    public function calculateHotelBooking(
        int $roomTypeId,
        string $checkInDate,
        string $checkOutDate,
        array $addons = [],
        ?float $customMonthlyDiscount = null
    ): array {
        $roomType = RoomType::find($roomTypeId);

        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);
        $totalDays = $checkIn->diffInDays($checkOut) + 1;

        // Validate minimum stay of 3 days
        if ($totalDays < 3) {
            throw new \Exception('Minimum stay is 3 days');
        }

        // Calculate room price using tiered pricing
        $roomPrice = $roomType->getPriceForDuration($totalDays, $customMonthlyDiscount);

        // Calculate addons price
        $addonsTotal = $this->calculateAddonsTotal($addons);

        $subtotal = $roomPrice + $addonsTotal;
        // No additional discount calculation - pricing is handled by tiered rates
        $discount = 0;
        $finalAmount = $subtotal - $discount;

        return [
            'total_days' => $totalDays,
            'room_price' => $roomPrice,
            'addons_total' => $addonsTotal,
            'total_amount' => $subtotal,
            'discount_amount' => $discount,
            'final_amount' => $finalAmount,
        ];
    }

    public function calculateSpaBooking(
        int $spaPackageId,
        array $addons = [],
        bool $isResident = false
    ): array {
        $spaPackage = SpaPackage::find($spaPackageId);
        
        // Use resident price if user is resident, otherwise use regular price
        $servicePrice = $isResident && $spaPackage->resident_price 
            ? $spaPackage->resident_price 
            : $spaPackage->price;

        // Calculate addons price
        $addonsTotal = $this->calculateAddonsTotal($addons);

        $subtotal = $servicePrice + $addonsTotal;
        $discount = 0;
        $finalAmount = $subtotal - $discount;

        return [
            'service_price' => $servicePrice,
            'addons_total' => $addonsTotal,
            'total_amount' => $subtotal,
            'discount_amount' => $discount,
            'final_amount' => $finalAmount,
            'is_resident' => $isResident,
        ];
    }

    public function calculateSpayBooking(
        int $spayPackageId,
        array $addons = [],
        bool $isResident = false,
        ?int $postCareDays = null
    ): array {
        $spayPackage = SpayPackage::find($spayPackageId);
        
        // Use resident price if user is resident, otherwise use regular price
        $servicePrice = $isResident && $spayPackage->resident_price 
            ? $spayPackage->resident_price 
            : $spayPackage->price;

        // Calculate post-operative care pricing if applicable
        $postCareTotal = 0;
        if ($postCareDays && $postCareDays > 0) {
            $postCareTotal = $this->calculatePostCarePricing($spayPackage, $postCareDays);
        }

        // Calculate addons price
        $addonsTotal = $this->calculateAddonsTotal($addons);

        $subtotal = $servicePrice + $postCareTotal + $addonsTotal;
        $discount = 0;
        $finalAmount = $subtotal - $discount;

        return [
            'service_price' => $servicePrice,
            'post_care_total' => $postCareTotal,
            'post_care_days' => $postCareDays ?? 0,
            'addons_total' => $addonsTotal,
            'total_amount' => $subtotal,
            'discount_amount' => $discount,
            'final_amount' => $finalAmount,
            'is_resident' => $isResident,
        ];
    }

    /**
     * Calculate post-operative care pricing with tiered rates
     * First 3 days: post_care_rate_first_3_days per day
     * Next 4 days: post_care_rate_next_4_days per day
     * Second week: post_care_rate_second_week per day
     */
    private function calculatePostCarePricing(SpayPackage $spayPackage, int $days): float
    {
        $total = 0;
        $remainingDays = $days;

        // First 3 days
        if ($remainingDays > 0 && $spayPackage->post_care_rate_first_3_days) {
            $first3Days = min(3, $remainingDays);
            $total += $spayPackage->post_care_rate_first_3_days * $first3Days;
            $remainingDays -= $first3Days;
        }

        // Next 4 days (days 4-7)
        if ($remainingDays > 0 && $spayPackage->post_care_rate_next_4_days) {
            $next4Days = min(4, $remainingDays);
            $total += $spayPackage->post_care_rate_next_4_days * $next4Days;
            $remainingDays -= $next4Days;
        }

        // Second week (days 8-14)
        if ($remainingDays > 0 && $spayPackage->post_care_rate_second_week) {
            $secondWeekDays = min(7, $remainingDays);
            $total += $spayPackage->post_care_rate_second_week * $secondWeekDays;
            $remainingDays -= $secondWeekDays;
        }

        // If there are more days beyond 14, use second week rate
        if ($remainingDays > 0 && $spayPackage->post_care_rate_second_week) {
            $total += $spayPackage->post_care_rate_second_week * $remainingDays;
        }

        return $total;
    }

    private function calculateAddonsTotal(array $addons): float
    {
        $total = 0;

        foreach ($addons as $addon) {
            $addonService = AddonService::find($addon['addon_service_id']);
            if ($addonService) {
                $total += $addonService->price * $addon['quantity'];
            }
        }

        return $total;
    }
}
