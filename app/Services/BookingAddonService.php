<?php

namespace App\Services;

use App\Models\{Booking, BookingAddon, AddonService};
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\DB;

class BookingAddonService
{
    public function __construct(
        private BookingRepository $bookingRepo
    ) {}

    public function getBookingAddons(int $bookingId): array
    {
        return BookingAddon::where('booking_id', $bookingId)
            ->with('addonService')
            ->get()
            ->map(function ($addon) {
                return [
                    'id' => $addon->id,
                    'quantity' => $addon->quantity,
                    'unit_price' => $addon->unit_price,
                    'total_price' => $addon->total_price,
                    'service' => [
                        'id' => $addon->addonService->id,
                        'name' => $addon->addonService->name,
                        'description' => $addon->addonService->description,
                        'category' => $addon->addonService->category,
                        'current_price' => $addon->addonService->price,
                    ],
                    'created_at' => $addon->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray();
    }

    public function addAddonToBooking(int $bookingId, int $addonServiceId, int $quantity): array
    {
        $addonService = AddonService::active()->find($addonServiceId);

        if (!$addonService) {
            throw new \InvalidArgumentException('Addon service not found or inactive');
        }

        if ($quantity < 1 || $quantity > 10) {
            throw new \InvalidArgumentException('Quantity must be between 1 and 10');
        }

        DB::beginTransaction();
        try {
            // Check if addon already exists for this booking
            $existingAddon = BookingAddon::where('booking_id', $bookingId)
                ->where('addon_service_id', $addonServiceId)
                ->first();

            if ($existingAddon) {
                // Update existing addon quantity
                $newQuantity = $existingAddon->quantity + $quantity;
                $existingAddon->update([
                    'quantity' => $newQuantity,
                    'total_price' => $addonService->price * $newQuantity,
                ]);
                $addon = $existingAddon;
            } else {
                // Create new addon
                $addon = BookingAddon::create([
                    'booking_id' => $bookingId,
                    'addon_service_id' => $addonServiceId,
                    'quantity' => $quantity,
                    'unit_price' => $addonService->price,
                    'total_price' => $addonService->price * $quantity,
                ]);
            }

            // Recalculate booking total
            $newBookingTotal = $this->recalculateBookingTotal($bookingId);

            DB::commit();

            return [
                'addon' => [
                    'id' => $addon->id,
                    'service_name' => $addonService->name,
                    'quantity' => $addon->quantity,
                    'unit_price' => $addon->unit_price,
                    'total_price' => $addon->total_price,
                ],
                'booking_total' => $newBookingTotal,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateBookingAddon(int $bookingId, int $addonId, int $quantity): ?array
    {
        if ($quantity < 1 || $quantity > 10) {
            throw new \InvalidArgumentException('Quantity must be between 1 and 10');
        }

        $addon = BookingAddon::where('booking_id', $bookingId)
            ->where('id', $addonId)
            ->with('addonService')
            ->first();

        if (!$addon) {
            return null;
        }

        DB::beginTransaction();
        try {
            $addon->update([
                'quantity' => $quantity,
                'total_price' => $addon->unit_price * $quantity,
            ]);

            // Recalculate booking total
            $newBookingTotal = $this->recalculateBookingTotal($bookingId);

            DB::commit();

            return [
                'addon' => [
                    'id' => $addon->id,
                    'service_name' => $addon->addonService->name,
                    'quantity' => $addon->quantity,
                    'unit_price' => $addon->unit_price,
                    'total_price' => $addon->total_price,
                ],
                'booking_total' => $newBookingTotal,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function removeAddonFromBooking(int $bookingId, int $addonId): ?array
    {
        $addon = BookingAddon::where('booking_id', $bookingId)
            ->where('id', $addonId)
            ->first();

        if (!$addon) {
            return null;
        }

        DB::beginTransaction();
        try {
            $addon->delete();

            // Recalculate booking total
            $newBookingTotal = $this->recalculateBookingTotal($bookingId);
            $newAddonsTotal = $this->getAddonsTotal($bookingId);

            DB::commit();

            return [
                'new_booking_total' => $newBookingTotal,
                'new_addons_total' => $newAddonsTotal,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getAvailableAddons(string $bookingType): array
    {
        $query = AddonService::active()->orderBy('name');

        // Filter by category based on booking type
        if ($bookingType === 'hotel') {
            $query->whereIn('category', ['hotel', 'general']);
        } elseif ($bookingType === 'spa') {
            $query->whereIn('category', ['spa', 'general']);
        } else {
            $query->where('category', 'general');
        }

        return $query->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'category' => $service->category,
                'price' => $service->price,
            ];
        })->toArray();
    }

    public function getAddonsTotal(int $bookingId): float
    {
        return BookingAddon::where('booking_id', $bookingId)->sum('total_price');
    }

    private function recalculateBookingTotal(int $bookingId): float
    {
        $booking = $this->bookingRepo->findOrFail($bookingId);
        $addonsTotal = $this->getAddonsTotal($bookingId);

        // Calculate base price based on booking type
        if ($booking->type === 'hotel') {
            $basePrice = $booking->roomType->getPriceForDuration($booking->total_days);
        } elseif ($booking->type === 'spa') {
            $basePrice = $booking->spaBooking->spaPackage->price;
        } elseif ($booking->type === 'spay') {
            $basePrice = $booking->spayBooking->spayPackage->price;
        } else {
            $basePrice = 0;
        }

        $subtotal = $basePrice + $addonsTotal;
        $finalAmount = $subtotal - $booking->discount_amount;

        // Update booking totals
        $booking->update([
            'total_amount' => $subtotal,
            'final_amount' => $finalAmount,
        ]);

        return $finalAmount;
    }

    public function clearBookingAddons(int $bookingId): bool
    {
        DB::beginTransaction();
        try {
            BookingAddon::where('booking_id', $bookingId)->delete();
            $this->recalculateBookingTotal($bookingId);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getAddonsByCategory(int $bookingId): array
    {
        $addons = BookingAddon::where('booking_id', $bookingId)
            ->with('addonService')
            ->get()
            ->groupBy('addonService.category');

        $result = [];
        foreach ($addons as $category => $categoryAddons) {
            $result[$category] = [
                'category_name' => ucfirst($category),
                'items' => $categoryAddons->map(function ($addon) {
                    return [
                        'id' => $addon->id,
                        'name' => $addon->addonService->name,
                        'quantity' => $addon->quantity,
                        'unit_price' => $addon->unit_price,
                        'total_price' => $addon->total_price,
                    ];
                })->toArray(),
                'category_total' => $categoryAddons->sum('total_price'),
            ];
        }

        return $result;
    }
}
