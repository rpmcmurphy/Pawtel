<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gt:price',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'string|max:255',
            'specifications' => 'nullable|array',
            'status' => 'required|in:active,inactive,out_of_stock',
            'featured' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Product category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'sku.unique' => 'This SKU is already taken.',
            'price.required' => 'Product price is required.',
            'price.min' => 'Product price must be at least 0.',
            'compare_price.gt' => 'Compare price must be greater than regular price.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.integer' => 'Stock quantity must be a whole number.',
            'stock_quantity.min' => 'Stock quantity cannot be negative.',
            'status.required' => 'Product status is required.',
            'status.in' => 'Invalid product status.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->input('name'))
            ]);
        }

        if ($this->has('sku')) {
            $this->merge([
                'sku' => strtoupper($this->input('sku'))
            ]);
        }

        // Set default values
        $this->merge([
            'low_stock_threshold' => $this->input('low_stock_threshold', 5),
            'featured' => (bool) $this->input('featured', false),
            'status' => $this->input('status', 'active'),
        ]);
    }
}
