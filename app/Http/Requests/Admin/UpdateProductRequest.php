<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'sku' => 'required|string|max:100|unique:products,sku,' . $this->route('id'),
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'specifications' => 'nullable|array',
            'status' => 'required|in:active,inactive,out_of_stock',
            'featured' => 'boolean',
        ];
    }
}
