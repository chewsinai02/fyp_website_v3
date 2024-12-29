<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Kreait\Firebase\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageUploadHelper
{
    protected $storage;
    protected const FIREBASE_BUCKET = 'fyptestv2-37c45.firebasestorage.app';
    protected const CREDENTIALS_FILE = 'fyptestv2-37c45-firebase-adminsdk-tu0u8-caf619423c.json';
    
    protected $defaultImages = [
        'profile' => 'images/profile.png',
        'nurse' => 'images/nurse.png',
        'doctor' => 'images/doctor.png',
        'patient' => 'images/p2.jpg'
    ];

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Upload image to Firebase Storage
     */
    public function uploadImage(UploadedFile $file, string $userType, string $userId): string
    {
        try {
            // Validate file
            $this->validateFile($file);

            // Generate filename with timestamp
            $timestamp = time();
            $filename = "image_{$timestamp}_{$file->getClientOriginalName()}";
            $path = "assets/images/{$filename}";

            // Get bucket
            $bucket = $this->storage->getBucket();

            // Upload file
            $object = $bucket->upload(
                $file->get(),
                [
                    'name' => $path,
                    'metadata' => [
                        'contentType' => $file->getMimeType(),
                        'firebaseStorageDownloadTokens' => Str::uuid(), // Add download token
                        'metadata' => [
                            'userType' => $userType,
                            'userId' => $userId,
                            'uploadTime' => $timestamp
                        ]
                    ]
                ]
            );

            // Get download URL with token
            $downloadToken = $object->info()['metadata']['firebaseStorageDownloadTokens'];
            
            // Generate Firebase Storage URL
            $encodedPath = urlencode($path);
            $url = "https://firebasestorage.googleapis.com/v0/b/" . self::FIREBASE_BUCKET . "/o/{$encodedPath}?alt=media&token={$downloadToken}";

            return $url;

        } catch (\Exception $e) {
            Log::error("Image upload failed: " . $e->getMessage());
            throw new \Exception('Failed to upload image: ' . $e->getMessage());
        }
    }

    /**
     * Delete old image from Firebase Storage
     */
    public function deleteImage(string $url): void
    {
        try {
            // Don't delete default images
            if (in_array($url, array_values($this->defaultImages))) {
                return;
            }

            // Extract path from Firebase URL
            $path = explode('?', explode('/o/', $url)[1])[0];
            $path = urldecode($path);

            $bucket = $this->storage->getBucket();
            if ($bucket->object($path)->exists()) {
                $bucket->object($path)->delete();
            }
        } catch (\Exception $e) {
            Log::error("Image deletion failed: " . $e->getMessage());
        }
    }

    /**
     * Validate uploaded file
     */
    protected function validateFile(UploadedFile $file): void
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \Exception('Invalid file type. Only images (JPG, PNG, GIF) are allowed.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
            throw new \Exception('File size exceeds 5MB limit.');
        }
    }

    /**
     * Get Firebase Storage URL for a path
     */
    protected function getFirebaseUrl(string $path, string $token): string
    {
        $encodedPath = urlencode($path);
        return "https://firebasestorage.googleapis.com/v0/b/" . self::FIREBASE_BUCKET . 
               "/o/{$encodedPath}?alt=media&token={$token}";
    }
} 