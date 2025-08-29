<?php
// app/Http/Resources/BookingResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_number' => $this->booking_number,
            'type' => $this->type,
            'status' => $this->status,
            'check_in_date' => $this->check_in_date->format('Y-m-d'),
            'check_out_date' => $this->check_out_date->format('Y-m-d'),
            'total_days' => $this->total_days,
            'total_amount' => $this->total_amount,
            'discount_amount' => $this->discount_amount,
            'final_amount' => $this->final_amount,
            'special_requests' => $this->special_requests,
            'cancellation_reason' => $this->cancellation_reason,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'confirmed_at' => $this->confirmed_at?->format('Y-m-d H:i:s'),
            'cancelled_at' => $this->cancelled_at?->format('Y-m-d H:i:s'),

            // Room type for hotel bookings
            'room_type' => $this->when(
                $this->relationLoaded('roomType') && $this->roomType,
                function () {
                    return [
                        'id' => $this->roomType->id,
                        'name' => $this->roomType->name,
                        'slug' => $this->roomType->slug,
                        'max_capacity' => $this->roomType->max_capacity,
                        'amenities' => $this->roomType->amenities,
                    ];
                }
            ),

            // Spa booking details
            'spa_booking' => $this->when(
                $this->relationLoaded('spaBooking') && $this->spaBooking,
                function () {
                    return [
                        'appointment_date' => $this->spaBooking->appointment_date->format('Y-m-d'),
                        'appointment_time' => $this->spaBooking->appointment_time->format('H:i'),
                        'status' => $this->spaBooking->status,
                        'notes' => $this->spaBooking->notes,
                        'spa_package' => $this->when(
                            $this->spaBooking->relationLoaded('spaPackage'),
                            [
                                'id' => $this->spaBooking->spaPackage->id,
                                'name' => $this->spaBooking->spaPackage->name,
                                'description' => $this->spaBooking->spaPackage->description,
                                'duration_minutes' => $this->spaBooking->spaPackage->duration_minutes,
                                'price' => $this->spaBooking->spaPackage->price,
                            ]
                        ),
                    ];
                }
            ),

            // Spay booking details
            'spay_booking' => $this->when(
                $this->relationLoaded('spayBooking') && $this->spayBooking,
                function () {
                    return [
                        'procedure_date' => $this->spayBooking->procedure_date->format('Y-m-d'),
                        'pet_name' => $this->spayBooking->pet_name,
                        'pet_age' => $this->spayBooking->pet_age,
                        'pet_weight' => $this->spayBooking->pet_weight,
                        'medical_notes' => $this->spayBooking->medical_notes,
                        'vet_assigned' => $this->spayBooking->vet_assigned,
                        'status' => $this->spayBooking->status,
                        'spay_package' => $this->when(
                            $this->spayBooking->relationLoaded('spayPackage'),
                            [
                                'id' => $this->spayBooking->spayPackage->id,
                                'name' => $this->spayBooking->spayPackage->name,
                                'type' => $this->spayBooking->spayPackage->type,
                                'description' => $this->spayBooking->spayPackage->description,
                                'price' => $this->spayBooking->spayPackage->price,
                                'post_care_days' => $this->spayBooking->spayPackage->post_care_days,
                            ]
                        ),
                    ];
                }
            ),

            // Assigned rooms
            'rooms' => $this->when(
                $this->relationLoaded('rooms'),
                function () {
                    return $this->rooms->map(function ($room) {
                        return [
                            'id' => $room->id,
                            'room_number' => $room->room_number,
                            'floor' => $room->floor,
                            'assigned_at' => $room->pivot->assigned_at,
                        ];
                    });
                }
            ),

            // Add-on services
            'addons' => $this->when(
                $this->relationLoaded('addons'),
                function () {
                    return $this->addons->map(function ($addon) {
                        return [
                            'id' => $addon->id,
                            'quantity' => $addon->quantity,
                            'unit_price' => $addon->unit_price,
                            'total_price' => $addon->total_price,
                            'service' => $this->when(
                                $addon->relationLoaded('addonService'),
                                [
                                    'id' => $addon->addonService->id,
                                    'name' => $addon->addonService->name,
                                    'description' => $addon->addonService->description,
                                    'category' => $addon->addonService->category,
                                ]
                            ),
                        ];
                    });
                }
            ),

            // Documents
            'documents' => $this->when(
                $this->relationLoaded('documents'),
                function () {
                    return $this->documents->map(function ($document) {
                        return [
                            'id' => $document->id,
                            'document_type' => $document->document_type,
                            'original_name' => $document->original_name,
                            'file_url' => $document->getUrl(),
                            'uploaded_at' => $document->uploaded_at->format('Y-m-d H:i:s'),
                            'verified_at' => $document->verified_at?->format('Y-m-d H:i:s'),
                            'is_verified' => $document->isVerified(),
                        ];
                    });
                }
            ),

            // Customer info (for admin views)
            'customer' => $this->when(
                $this->relationLoaded('user') && auth()->user()?->hasRole('admin'),
                [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,
                ]
            ),
        ];
    }
}
