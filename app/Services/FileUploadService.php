<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    public function uploadBookingDocument(UploadedFile $file, int $bookingId, string $documentType): array
    {
        // Validate file
        $this->validateFile($file, ['pdf', 'jpg', 'jpeg', 'png'], 5120); // 5MB max

        $fileName = $this->generateFileName($file, 'booking_doc');
        $directory = "booking_documents/{$bookingId}";

        // Store file
        $filePath = Storage::putFileAs($directory, $file, $fileName);

        return [
            'file_path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ];
    }

    public function uploadProductImage(UploadedFile $file, string $productSlug): array
    {
        // Validate file
        $this->validateFile($file, ['jpg', 'jpeg', 'png'], 2048); // 2MB max

        $fileName = $this->generateFileName($file, 'product_img');
        $directory = "product_images/{$productSlug}";

        // Process and resize image
        $image = Image::make($file);

        // Create multiple sizes
        $sizes = [
            'thumbnail' => [300, 300],
            'medium' => [600, 600],
            'large' => [1200, 1200],
        ];

        $uploadedImages = [];

        foreach ($sizes as $sizeName => $dimensions) {
            $resizedImage = $image->fit($dimensions[0], $dimensions[1]);
            $sizeFileName = $sizeName . '_' . $fileName;

            $resizedImage->encode($file->getClientOriginalExtension(), 85);
            Storage::put($directory . '/' . $sizeFileName, $resizedImage->encoded);

            $uploadedImages[$sizeName] = Storage::url($directory . '/' . $sizeFileName);
        }

        return [
            'images' => $uploadedImages,
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    public function uploadPostImage(UploadedFile $file, string $postSlug): array
    {
        // Validate file
        $this->validateFile($file, ['jpg', 'jpeg', 'png'], 3072); // 3MB max

        $fileName = $this->generateFileName($file, 'post_img');
        $directory = "post_images/{$postSlug}";

        // Process and resize image
        $image = Image::make($file);

        // Create featured image (optimize for web)
        $featuredImage = $image->fit(800, 600);
        $featuredImage->encode($file->getClientOriginalExtension(), 85);
        Storage::put($directory . '/' . $fileName, $featuredImage->encoded);

        // Create thumbnail
        $thumbnail = $image->fit(300, 200);
        $thumbnailFileName = 'thumb_' . $fileName;
        $thumbnail->encode($file->getClientOriginalExtension(), 85);
        Storage::put($directory . '/' . $thumbnailFileName, $thumbnail->encoded);

        return [
            'featured_image' => Storage::url($directory . '/' . $fileName),
            'thumbnail' => Storage::url($directory . '/' . $thumbnailFileName),
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    public function uploadProfilePicture(UploadedFile $file, int $userId): array
    {
        // Validate file
        $this->validateFile($file, ['jpg', 'jpeg', 'png'], 1024); // 1MB max

        $fileName = $this->generateFileName($file, 'avatar');
        $directory = "profile_pictures/{$userId}";

        // Delete old profile pictures
        Storage::deleteDirectory($directory);

        // Process and resize image to square
        $image = Image::make($file);
        $image->fit(300, 300);
        $image->encode($file->getClientOriginalExtension(), 90);

        Storage::put($directory . '/' . $fileName, $image->encoded);

        return [
            'profile_picture' => Storage::url($directory . '/' . $fileName),
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    public function deleteFile(string $filePath): bool
    {
        try {
            if (Storage::exists($filePath)) {
                return Storage::delete($filePath);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteDirectory(string $directory): bool
    {
        try {
            return Storage::deleteDirectory($directory);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function validateFile(UploadedFile $file, array $allowedExtensions, int $maxSizeKb): void
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            throw new \InvalidArgumentException(
                'Invalid file type. Allowed types: ' . implode(', ', $allowedExtensions)
            );
        }

        if ($file->getSize() > ($maxSizeKb * 1024)) {
            throw new \InvalidArgumentException(
                "File size too large. Maximum size: {$maxSizeKb}KB"
            );
        }
    }

    private function generateFileName(UploadedFile $file, string $prefix): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);

        return "{$prefix}_{$timestamp}_{$random}.{$extension}";
    }

    public function getFileSize(string $filePath): ?int
    {
        try {
            return Storage::exists($filePath) ? Storage::size($filePath) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFileUrl(string $filePath): ?string
    {
        try {
            return Storage::exists($filePath) ? Storage::url($filePath) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
