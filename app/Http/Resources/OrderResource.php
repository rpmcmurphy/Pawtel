<?php
// app/Http/Resources/OrderResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'delivery_charge' => $this->delivery_charge,
            'total_amount' => $this->total_amount,
            'delivery_address' => $this->delivery_address,
            'delivery_phone' => $this->delivery_phone,
            'delivery_notes' => $this->delivery_notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'delivered_at' => $this->delivered_at?->format('Y-m-d H:i:s'),
            'cancelled_at' => $this->cancelled_at?->format('Y-m-d H:i:s'),

            // Order items
            'items' => $this->when(
                $this->relationLoaded('orderItems'),
                function () {
                    return $this->orderItems->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->total_price,
                            'product' => $this->when(
                                $item->relationLoaded('product'),
                                [
                                    'id' => $item->product->id,
                                    'name' => $item->product->name,
                                    'slug' => $item->product->slug,
                                    'sku' => $item->product->sku,
                                    'images' => $item->product->images,
                                ]
                            ),
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
