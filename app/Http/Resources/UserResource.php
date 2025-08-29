<?php
// app/Http/Resources/UserResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'emergency_contact' => $this->emergency_contact,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'phone_verified_at' => $this->phone_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),

            // Verification status
            'email_verified' => $this->hasVerifiedEmail(),
            'phone_verified' => $this->hasVerifiedPhone(),

            // Roles and permissions
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name'),

            // Statistics (admin view)
            'bookings_count' => $this->when(
                auth()->user()?->hasRole('admin') && $this->relationLoaded('bookings'),
                $this->bookings->count()
            ),
            'orders_count' => $this->when(
                auth()->user()?->hasRole('admin') && $this->relationLoaded('orders'),
                $this->orders->count()
            ),
            'total_spent' => $this->when(
                auth()->user()?->hasRole('admin') && $this->relationLoaded('orders'),
                $this->orders->where('status', 'delivered')->sum('total_amount')
            ),
        ];
    }
}
