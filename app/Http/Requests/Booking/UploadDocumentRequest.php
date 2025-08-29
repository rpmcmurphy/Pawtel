<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'document_type' => 'required|string|in:vaccination_card,health_certificate,id_document,other',
        ];
    }

    public function messages(): array
    {
        return [
            'document.required' => 'Please select a document to upload',
            'document.file' => 'Invalid file format',
            'document.mimes' => 'Document must be a PDF, JPG, JPEG, or PNG file',
            'document.max' => 'Document size cannot exceed 5MB',
            'document_type.required' => 'Please select document type',
            'document_type.in' => 'Invalid document type selected',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
