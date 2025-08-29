<?php

namespace App\Http\Controllers\API\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\UploadDocumentRequest;
use App\Services\BookingDocumentService;
use App\Repositories\BookingRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BookingDocumentController extends Controller
{
    public function __construct(
        private BookingDocumentService $documentService,
        private BookingRepository $bookingRepo
    ) {}

    public function index(string $bookingNumber): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $documents = $this->documentService->getBookingDocuments($booking->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'booking_number' => $booking->booking_number,
                    'documents' => $documents,
                    'documents_count' => count($documents),
                    'required_documents' => 2,
                    'has_required_documents' => count($documents) >= 2,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(string $bookingNumber, UploadDocumentRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found or cannot upload documents'
                ], 404);
            }

            $result = $this->documentService->uploadDocument(
                $booking->id,
                $request->file('document'),
                $request->document_type
            );

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'data' => $result
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $bookingNumber, int $documentId): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = $user->bookings()
                ->where('booking_number', $bookingNumber)
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $result = $this->documentService->deleteDocument($booking->id, $documentId);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully',
                'data' => [
                    'documents_count' => $this->documentService->getDocumentsCount($booking->id),
                    'has_required_documents' => $this->documentService->hasRequiredDocuments($booking->id),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
