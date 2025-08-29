<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileUploadController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|string|in:profile_picture,booking_document,product_image,post_image',
            'context_id' => 'nullable|integer', // booking_id, product_id, etc.
            'document_type' => 'required_if:type,booking_document|string|in:vaccination_card,health_certificate,id_document,other',
        ]);

        try {
            $user = Auth::user();
            $file = $request->file('file');
            $type = $request->type;
            $contextId = $request->context_id;

            switch ($type) {
                case 'profile_picture':
                    $this->validateImageFile($file, 1024); // 1MB max
                    $result = $this->fileUploadService->uploadProfilePicture($file, $user->id);

                    // Update user profile picture
                    $user->update(['profile_picture' => $result['profile_picture']]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Profile picture uploaded successfully',
                        'data' => $result
                    ]);

                case 'booking_document':
                    if (!$contextId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Booking ID is required for document upload'
                        ], 400);
                    }

                    // Verify booking belongs to user
                    $booking = $user->bookings()->find($contextId);
                    if (!$booking) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Booking not found or unauthorized'
                        ], 404);
                    }

                    $this->validateDocumentFile($file, 5120); // 5MB max
                    $result = $this->fileUploadService->uploadBookingDocument(
                        $file,
                        $contextId,
                        $request->document_type
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Document uploaded successfully',
                        'data' => $result
                    ]);

                case 'product_image':
                    if (!$user->hasRole('admin')) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized access'
                        ], 403);
                    }

                    if (!$contextId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Product ID is required'
                        ], 400);
                    }

                    $product = \App\Models\Product::find($contextId);
                    if (!$product) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Product not found'
                        ], 404);
                    }

                    $this->validateImageFile($file, 2048); // 2MB max
                    $result = $this->fileUploadService->uploadProductImage($file, $product->slug);

                    return response()->json([
                        'success' => true,
                        'message' => 'Product image uploaded successfully',
                        'data' => $result
                    ]);

                case 'post_image':
                    if (!$user->hasRole('admin')) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized access'
                        ], 403);
                    }

                    $postSlug = $request->get('post_slug', 'temp_' . time());
                    $this->validateImageFile($file, 3072); // 3MB max
                    $result = $this->fileUploadService->uploadPostImage($file, $postSlug);

                    return response()->json([
                        'success' => true,
                        'message' => 'Post image uploaded successfully',
                        'data' => $result
                    ]);

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid upload type'
                    ], 400);
            }
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function validateImageFile($file, int $maxSizeKb): void
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException(
                'Invalid image format. Only JPEG and PNG files are allowed.'
            );
        }

        if ($file->getSize() > ($maxSizeKb * 1024)) {
            throw new \InvalidArgumentException(
                "Image size too large. Maximum size: {$maxSizeKb}KB"
            );
        }
    }

    private function validateDocumentFile($file, int $maxSizeKb): void
    {
        $allowedMimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/jpg'
        ];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException(
                'Invalid document format. Only PDF, JPEG and PNG files are allowed.'
            );
        }

        if ($file->getSize() > ($maxSizeKb * 1024)) {
            throw new \InvalidArgumentException(
                "Document size too large. Maximum size: {$maxSizeKb}KB"
            );
        }
    }
}
