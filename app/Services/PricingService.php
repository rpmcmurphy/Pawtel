<?php

namespace App\Services;

use App\Models\RoomType;
use App\Models\SpaPackage;
use App\Models\SpayPackage;
use App\Models\AddonService;
use Carbon\Carbon;

class PricingService
{
    public function calculateHotelBooking(
        int $roomTypeId,
        string $checkInDate,
        string $checkOutDate,
        array $addons = []
    ): array {
        $roomType = RoomType::find($roomTypeId);

        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);
        $totalDays = $checkIn->diffInDays($checkOut) + 1;

        // Calculate room price
        $roomPrice = $roomType->getPriceForDuration($totalDays);

        // Calculate addons price
        $addonsTotal = $this->calculateAddonsTotal($addons);

        $subtotal = $roomPrice + $addonsTotal;
        $discount = $this->calculateDiscount($subtotal, $totalDays);
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
        array $addons = []
    ): array {
        $spaPackage = SpaPackage::find($spaPackageId);
        $servicePrice = $spaPackage->price;

        // Calculate addons price
        $addonsTotal = $this->calculateAddonsTotal($addons);

        $subtotal = $servicePrice + $addonsTotal;
        $discount = 0; // No discount for spa services currently
        $finalAmount = $subtotal - $discount;

        return [
            'service_price' => $servicePrice,
            'addons_total' => $addonsTotal,
            'total_amount' => $subtotal,
            'discount_amount' => $discount,
            'final_amount' => $finalAmount,
        ];
    }

    public function calculateSpayBooking(
        int $spayPackageId,
        array $addons = []
    ): array {
        $spayPackage = SpayPackage::find($spayPackageId);
        $servicePrice = $spayPackage->price;

        // Calculate addons price
        $addonsTotal = $this->calculateAddonsTotal($addons);

        $subtotal = $servicePrice + $addonsTotal;
        $discount = 0; // No discount for spay services currently
        $finalAmount = $subtotal - $discount;

        return [
            'service_price' => $servicePrice,
            'addons_total' => $addonsTotal,
            'total_amount' => $subtotal,
            'discount_amount' => $discount,
            'final_amount' => $finalAmount,
        ];
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

    private function calculateDiscount(float $amount, int $totalDays): float
    {
        // Apply discount based on stay duration
        if ($totalDays >= 30) {
            return $amount * 0.15; // 15% discount for monthly stays
        } elseif ($totalDays >= 14) {
            return $amount * 0.10; // 10% discount for 2+ weeks
        } elseif ($totalDays >= 7) {
            return $amount * 0.05; // 5% discount for weekly stays
        }

        return 0;
    }
}
