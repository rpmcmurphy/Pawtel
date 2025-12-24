<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'type' => 'sometimes|required|in:adoption,story,news,job',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'images' => 'nullable|array',
            'status' => 'required|in:draft,published,archived',
            'adoption.cat_name' => 'required_if:type,adoption|nullable|string|max:255',
            'adoption.age' => 'nullable|string|max:50',
            'adoption.gender' => 'nullable|in:male,female,unknown',
            'adoption.breed' => 'nullable|string|max:255',
            'adoption.health_status' => 'nullable|string',
            'adoption.adoption_fee' => 'nullable|numeric|min:0',
            'adoption.contact_info' => 'nullable|array',
            'adoption.status' => 'nullable|in:available,pending,adopted',
        ];
    }
}
