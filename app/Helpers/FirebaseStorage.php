<?php

namespace App\Helpers;

use Kreait\Firebase\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FirebaseStorage
{
    protected $storage;
    protected $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/jpg'
    ];

    // Simple path for all images
    private const IMAGE_PATH = 'assets/images';

    // Default images
    private const DEFAULT_IMAGES = [
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
     * Upload profile picture
     */
    public function uploadProfilePicture(UploadedFile $file, string $userId): string
    {
        try {
            $url = $this->uploadImage($file, self::IMAGE_PATH);
            return str_replace(url('/'), '', $url); // Store relative path
        } catch (\Exception $e) {
            Log::error('Profile picture upload failed: ' . $e->getMessage());
            return self::DEFAULT_IMAGES['profile'];
        }
    }

    /**
     * Upload staff image
     */
    public function uploadStaffImage(UploadedFile $file, string $staffId, string $type): string
    {
        try {
            $url = $this->uploadImage($file, self::IMAGE_PATH);
            return str_replace(url('/'), '', $url);
        } catch (\Exception $e) {
            Log::error("Staff image upload failed for type {$type}: " . $e->getMessage());
            return self::DEFAULT_IMAGES[$type] ?? self::DEFAULT_IMAGES['profile'];
        }
    }

    /**
     * Upload patient document
     */
    public function uploadPatientDocument(UploadedFile $file, string $patientId): string
    {
        try {
            $url = $this->uploadImage($file, self::IMAGE_PATH);
            return str_replace(url('/'), '', $url);
        } catch (\Exception $e) {
            Log::error('Patient document upload failed: ' . $e->getMessage());
            return self::DEFAULT_IMAGES['patient'];
        }
    }

    /**
     * Upload medical report image
     */
    public function uploadMedicalReport(UploadedFile $file, string $reportId): string
    {
        try {
            $url = $this->uploadImage($file, self::IMAGE_PATH);
            return str_replace(url('/'), '', $url);
        } catch (\Exception $e) {
            Log::error('Medical report upload failed: ' . $e->getMessage());
            return self::DEFAULT_IMAGES['profile'];
        }
    }

    /**
     * Generic image upload method
     */
    protected function uploadImage(UploadedFile $file, string $path): string
    {
        try {
            // Validate file type
            if (!in_array($file->getMimeType(), $this->allowedTypes)) {
                throw new \Exception('Invalid file type. Only images (JPG, PNG, GIF) are allowed.');
            }

            // Validate file size (5MB max)
            if ($file->getSize() > 5 * 1024 * 1024) {
                throw new \Exception('File size exceeds 5MB limit.');
            }

            $bucket = $this->storage->getBucket();
            
            // Generate unique filename
            $filename = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $fullPath = $path . '/' . $filename;
            
            // Set metadata
            $metadata = [
                'name' => $fullPath,
                'metadata' => [
                    'contentType' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'uploadTime' => time(),
                    'originalName' => $file->getClientOriginalName()
                ]
            ];
            
            // Upload file
            $bucket->upload(
                $file->get(),
                $metadata
            );
            
            // Get public URL with 10 years expiration
            return $bucket->object($fullPath)->signedUrl(
                new \DateTime('+ 10 years')
            );
            
        } catch (\Exception $e) {
            Log::error('Firebase Storage upload error: ' . $e->getMessage());
            throw new \Exception('Failed to upload image: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete file from storage
     */
    public function delete(string $path): void
    {
        // Don't delete default images
        if (in_array($path, self::DEFAULT_IMAGES)) {
            return;
        }

        try {
            $bucket = $this->storage->getBucket();
            
            // Extract path from URL if full URL is provided
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                $parsedUrl = parse_url($path);
                $path = trim($parsedUrl['path'], '/');
            }
            
            if ($bucket->object($path)->exists()) {
                $bucket->object($path)->delete();
            }
        } catch (\Exception $e) {
            Log::error('Firebase Storage delete error: ' . $e->getMessage());
            throw new \Exception('Failed to delete file: ' . $e->getMessage());
        }
    }

    /**
     * Test Firebase Storage connection
     */
    public function testConnection(): bool
    {
        try {
            $bucket = $this->storage->getBucket();
            $bucket->object('test.txt')->exists();
            return true;
        } catch (\Exception $e) {
            Log::error('Firebase Storage connection test failed: ' . $e->getMessage());
            throw new \Exception('Failed to connect to Firebase Storage: ' . $e->getMessage());
        }
    }
} 