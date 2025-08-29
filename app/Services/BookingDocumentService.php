<?php

namespace App\Services;

use App\Models\{Booking, BookingDocument};
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BookingDocumentService
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    public function getBookingDocuments(int $bookingId): array
    {
        return BookingDocument::where('booking_id', $bookingId)
            ->get()
            ->map(function ($document) {
                return [
                    'id' => $document->id,
                    'document_type' => $document->document_type,
                    'original_name' => $document->original_name,
                    'file_url' => $document->getUrl(),
                    'uploaded_at' => $document->uploaded_at,
                    'verified_at' => $document->verified_at,
                    'is_verified' => $document->isVerified(),
                ];
            })->toArray();
    }

    public function uploadDocument(int $bookingId, UploadedFile $file, string $documentType): array
    {
        // Check if maximum documents reached (2 required)
        $currentCount = $this->getDocumentsCount($bookingId);
        if ($currentCount >= 2) {
            throw new \InvalidArgumentException('Maximum 2 documents allowed per booking');
        }

        // Check if document type already exists
        $existingDoc = BookingDocument::where('booking_id', $bookingId)
            ->where('document_type', $documentType)
            ->first();

        if ($existingDoc) {
            // Delete old file and record
            Storage::delete($existingDoc->file_path);
            $existingDoc->delete();
        }

        // Upload new file
        $uploadResult = $this->fileUploadService->uploadBookingDocument(
            $file,
            $bookingId,
            $documentType
        );

        // Create document record
        $document = BookingDocument::create([
            'booking_id' => $bookingId,
            'document_type' => $documentType,
            'file_path' => $uploadResult['file_path'],
            'original_name' => $uploadResult['original_name'],
            'uploaded_at' => now(),
        ]);

        return [
            'document' => [
                'id' => $document->id,
                'document_type' => $document->document_type,
                'original_name' => $document->original_name,
                'file_url' => $document->getUrl(),
                'uploaded_at' => $document->uploaded_at,
                'is_verified' => false,
            ],
            'documents_count' => $this->getDocumentsCount($bookingId),
            'has_required_documents' => $this->hasRequiredDocuments($bookingId),
        ];
    }

    public function deleteDocument(int $bookingId, int $documentId): bool
    {
        $document = BookingDocument::where('booking_id', $bookingId)
            ->where('id', $documentId)
            ->first();

        if (!$document) {
            return false;
        }

        // Delete file from storage
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        $document->delete();
        return true;
    }

    public function getDocumentsCount(int $bookingId): int
    {
        return BookingDocument::where('booking_id', $bookingId)->count();
    }

    public function hasRequiredDocuments(int $bookingId): bool
    {
        return $this->getDocumentsCount($bookingId) >= 2;
    }

    public function verifyDocument(int $documentId): bool
    {
        $document = BookingDocument::find($documentId);

        if (!$document) {
            return false;
        }

        $document->verify();
        return true;
    }

    public function getVerifiedDocuments(int $bookingId): array
    {
        return BookingDocument::where('booking_id', $bookingId)
            ->whereNotNull('verified_at')
            ->get()
            ->toArray();
    }

    public function getPendingDocuments(int $bookingId): array
    {
        return BookingDocument::where('booking_id', $bookingId)
            ->whereNull('verified_at')
            ->get()
            ->toArray();
    }
}
