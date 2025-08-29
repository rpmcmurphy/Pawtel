<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:adoption,story,news,job',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'images' => 'nullable|array',
            'status' => 'required|in:draft,published',
            'adoption.cat_name' => 'required_if:type,adoption|string|max:100',
            'adoption.age' => 'nullable|string|max:50',
            'adoption.gender' => 'nullable|in:male,female,unknown',
            'adoption.breed' => 'nullable|string|max:100',
            'adoption.health_status' => 'nullable|string',
            'adoption.adoption_fee' => 'nullable|numeric|min:0',
            'adoption.contact_info' => 'nullable|array',
        ];
    }
}
