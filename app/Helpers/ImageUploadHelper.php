<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Kreait\Firebase\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageUploadHelper
{
    protected $storage;
    protected $defaultImages = [
        'profile' => 'images/profile.png',
        'nurse' => 'images/nurse.png',
        'nurse_alt1' => 'images/nurse(1).png',
        'nurse_alt2' => 'images/nurse(2).png',
        'patient' => 'images/p2.jpg'
    ];

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
        
        // Verify storage connection
        try {
            $this->storage->getBucket()->exists();
        } catch (\Exception $e) {
            Log::error('Firebase Storage connection failed: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Firebase Storage');
        }
    }

    /**
     * Upload image to Firebase Storage
     */
    public function uploadImage(UploadedFile $file, string $userType, string $userId): string
    {
        try {
            // Validate file
            $this->validateFile($file);

            // Generate very short unique filename (8 chars + extension)
            $filename = substr(md5(uniqid() . time()), 0, 8) . '.' . $file->getClientOriginalExtension();
            $path = "img/{$filename}"; // Shorter path

            // Get bucket
            $bucket = $this->storage->getBucket();

            // Upload file
            $object = $bucket->upload(
                $file->get(),
                [
                    'name' => $path,
                    'predefinedAcl' => 'publicRead', // Make it public
                    'metadata' => [
                        'contentType' => $file->getMimeType(),
                        'metadata' => [
                            'userType' => $userType,
                            'userId' => $userId
                        ]
                    ]
                ]
            );

            // Use direct storage URL format
            return "https://storage.googleapis.com/{$bucket->name()}/{$path}";

        } catch (\Exception $e) {
            Log::error("Image upload failed: " . $e->getMessage());
            return $this->getDefaultImage($userType);
        }
    }

    /**
     * Delete old image from Firebase Storage
     */
    public function deleteImage(string $url): void
    {
        try {
            // Don't delete default images
            if (in_array($url, $this->defaultImages)) {
                return;
            }

            // Extract path from URL
            $path = str_replace(
                "https://storage.googleapis.com/{$this->storage->getBucket()->name()}/",
                '',
                $url
            );

            $bucket = $this->storage->getBucket();
            if ($bucket->object($path)->exists()) {
                $bucket->object($path)->delete();
            }
        } catch (\Exception $e) {
            Log::error("Image deletion failed: " . $e->getMessage());
        }
    }

    /**
     * Get default image for user type
     */
    protected function getDefaultImage(string $userType): string
    {
        return $this->defaultImages[$userType] ?? $this->defaultImages['profile'];
    }

    /**
     * Validate uploaded file
     */
    protected function validateFile(UploadedFile $file): void
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \Exception('Invalid file type. Only images are allowed.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
            throw new \Exception('File size exceeds 5MB limit.');
        }
    }
} 