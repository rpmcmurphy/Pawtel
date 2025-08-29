<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\SpaBooking;
use App\Models\SpayBooking;
use App\Models\AddonService;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function createHotelBooking(array $data): Booking
    {
        DB::beginTransaction();

        try {
            // Create main booking
            $booking = Booking::create([
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'total_days' => $data['total_days'],
                'room_type_id' => $data['room_type_id'],
                'total_amount' => $data['total_amount'],
                'discount_amount' => $data['discount_amount'],
                'final_amount' => $data['final_amount'],
                'special_requests' => $data['special_requests'] ?? null,
                'status' => 'pending',
            ]);

            // Add addon services if provided
            if (!empty($data['addons'])) {
                $this->addBookingAddons($booking, $data['addons']);
            }

            // Block dates for this booking
            $this->blockBookingDates($booking);

            DB::commit();
            return $booking;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createSpaBooking(array $data): Booking
    {
        DB::beginTransaction();

        try {
            // Create main booking
            $booking = Booking::create([
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'total_days' => $data['total_days'],
                'total_amount' => $data['total_amount'],
                'discount_amount' => $data['discount_amount'],
                'final_amount' => $data['final_amount'],
                'special_requests' => $data['special_requests'] ?? null,
                'status' => 'pending',
            ]);

            // Create spa booking details
            SpaBooking::create([
                'booking_id' => $booking->id,
                'spa_package_id' => $data['spa_details']['spa_package_id'],
                'appointment_date' => $data['spa_details']['appointment_date'],
                'appointment_time' => $data['spa_details']['appointment_time'],
                'notes' => $data['spa_details']['notes'] ?? null,
                'status' => 'scheduled',
            ]);

            // Add addon services if provided
            if (!empty($data['addons'])) {
                $this->addBookingAddons($booking, $data['addons']);
            }

            DB::commit();
            return $booking;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createSpayBooking(array $data): Booking
    {
        DB::beginTransaction();

        try {
            // Create main booking
            $booking = Booking::create([
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'total_days' => $data['total_days'],
                'total_amount' => $data['total_amount'],
                'discount_amount' => $data['discount_amount'],
                'final_amount' => $data['final_amount'],
                'special_requests' => $data['special_requests'] ?? null,
                'status' => 'pending',
            ]);

            // Create spay booking details
            SpayBooking::create([
                'booking_id' => $booking->id,
                'spay_package_id' => $data['spay_details']['spay_package_id'],
                'procedure_date' => $data['spay_details']['procedure_date'],
                'pet_name' => $data['spay_details']['pet_name'],
                'pet_age' => $data['spay_details']['pet_age'] ?? null,
                'pet_weight' => $data['spay_details']['pet_weight'] ?? null,
                'medical_notes' => $data['spay_details']['medical_notes'] ?? null,
                'status' => 'scheduled',
            ]);

            // Add addon services if provided
            if (!empty($data['addons'])) {
                $this->addBookingAddons($booking, $data['addons']);
            }

            DB::commit();
            return $booking;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function addBookingAddons(Booking $booking, array $addons): void
    {
        foreach ($addons as $addon) {
            $addonService = AddonService::find($addon['addon_service_id']);

            BookingAddon::create([
                'booking_id' => $booking->id,
                'addon_service_id' => $addon['addon_service_id'],
                'quantity' => $addon['quantity'],
                'unit_price' => $addonService->price,
                'total_price' => $addonService->price * $addon['quantity'],
            ]);
        }
    }

    private function blockBookingDates(Booking $booking): void
    {
        $currentDate = $booking->check_in_date;
        $endDate = $booking->check_out_date;

        while ($currentDate <= $endDate) {
            \App\Models\BlockedDate::create([
                'room_type_id' => $booking->room_type_id,
                'date' => $currentDate,
                'reason' => 'Booked by customer',
                'is_manual' => false,
                'reference_type' => 'booking',
                'reference_id' => $booking->id,
            ]);

            $currentDate = $currentDate->addDay();
        }
    }

    public function generateInvoice(Booking $booking): array
    {
        $invoice = [
            'booking_number' => $booking->booking_number,
            'booking_type' => $booking->type,
            'customer' => [
                'name' => $booking->user->name,
                'email' => $booking->user->email,
                'phone' => $booking->user->phone,
                'address' => $booking->user->address,
            ],
            'booking_details' => [
                'check_in_date' => $booking->check_in_date->format('Y-m-d'),
                'check_out_date' => $booking->check_out_date->format('Y-m-d'),
                'total_days' => $booking->total_days,
                'status' => $booking->status,
                'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
            ],
            'items' => [],
            'summary' => [
                'subtotal' => 0,
                'discount' => $booking->discount_amount,
                'total' => $booking->final_amount,
            ],
        ];

        // Add main service item
        if ($booking->type === 'hotel') {
            $roomType = $booking->roomType;
            $invoice['items'][] = [
                'description' => "Hotel Stay - {$roomType->name}",
                'quantity' => $booking->total_days,
                'unit_price' => $roomType->base_daily_rate,
                'total' => $roomType->base_daily_rate * $booking->total_days,
            ];
            $invoice['summary']['subtotal'] += $roomType->base_daily_rate * $booking->total_days;
        } elseif ($booking->type === 'spa') {
            $spaPackage = $booking->spaBooking->spaPackage;
            $invoice['items'][] = [
                'description' => "Spa Service - {$spaPackage->name}",
                'quantity' => 1,
                'unit_price' => $spaPackage->price,
                'total' => $spaPackage->price,
            ];
            $invoice['summary']['subtotal'] += $spaPackage->price;
        } elseif ($booking->type === 'spay') {
            $spayPackage = $booking->spayBooking->spayPackage;
            $invoice['items'][] = [
                'description' => "Spay/Neuter - {$spayPackage->name}",
                'quantity' => 1,
                'unit_price' => $spayPackage->price,
                'total' => $spayPackage->price,
            ];
            $invoice['summary']['subtotal'] += $spayPackage->price;
        }

        // Add addon items
        foreach ($booking->addons as $addon) {
            $invoice['items'][] = [
                'description' => "Add-on: {$addon->addonService->name}",
                'quantity' => $addon->quantity,
                'unit_price' => $addon->unit_price,
                'total' => $addon->total_price,
            ];
            $invoice['summary']['subtotal'] += $addon->total_price;
        }

        return $invoice;
    }
}
