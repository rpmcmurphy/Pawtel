<?php
// app/Http/Resources/ProductResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'stock_quantity' => $this->stock_quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
            'images' => $this->images,
            'specifications' => $this->specifications,
            'status' => $this->status,
            'featured' => $this->featured,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // Calculated fields
            'in_stock' => $this->isInStock(),
            'low_stock' => $this->isLowStock(),
            'discount_percentage' => $this->getDiscountPercentage(),

            // Category information
            'category' => $this->when(
                $this->relationLoaded('category'),
                [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                    'full_path' => $this->category->getFullPath(),
                ]
            ),

            // For cart items
            'cart_quantity' => $this->when(
                isset($this->cart_quantity),
                $this->cart_quantity
            ),
        ];
    }
}
