<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Simplified resource for product listings
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'compare_price' => $this->compare_price ? (float) $this->compare_price : null,
            'stock_quantity' => $this->stock_quantity,
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'images' => $this->images ? array_slice($this->images, 0, 1) : [], // Only first image

            // Computed fields
            'is_in_stock' => $this->isInStock(),
            'is_low_stock' => $this->isLowStock(),
            'discount_percentage' => $this->getDiscountPercentage(),

            // Category
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
