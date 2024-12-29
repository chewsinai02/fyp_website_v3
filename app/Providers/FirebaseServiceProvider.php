<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;
use Illuminate\Support\Facades\Log;
use App\Helpers\FirebaseCredentialsHelper;

class FirebaseServiceProvider extends ServiceProvider
{
    private const STORAGE_BUCKET = 'fyptestv2-37c45.firebasestorage.app';
    private const CLIENT_ID = '113589557894904691705';
    private const CREDENTIALS_FILE = 'fyptestv2-37c45-firebase-adminsdk-tu0u8-2ebcfea884.json';
    
    public function register()
    {
        $this->app->singleton(Storage::class, function ($app) {
            try {
                // Get credentials path
                $credentialsPath = storage_path('app/firebase/' . self::CREDENTIALS_FILE);
                
                // Ensure directory exists with proper permissions
                $directory = dirname($credentialsPath);
                if (!file_exists($directory)) {
                    mkdir($directory, 0777, true);
                } else {
                    chmod($directory, 0777);
                }

                // Set file permissions if file exists
                if (file_exists($credentialsPath)) {
                    chmod($credentialsPath, 0666);
                } else {
                    throw new \Exception("Credentials file not found at: {$credentialsPath}");
                }
                
                // Log the path for debugging
                Log::info('Looking for credentials at: ' . $credentialsPath);
                
                // Validate and clean credentials
                $credentials = FirebaseCredentialsHelper::validateAndCleanCredentials($credentialsPath);
                
                // Verify client ID
                if ($credentials['client_id'] !== self::CLIENT_ID) {
                    throw new \Exception('Invalid client ID in credentials file');
                }

                // Create Firebase instance
                $factory = (new Factory)
                    ->withServiceAccount($credentialsPath)
                    ->withDefaultStorageBucket(self::STORAGE_BUCKET);
                
                $storage = $factory->createStorage();
                
                // Test connection
                $bucket = $storage->getBucket();
                $bucketName = $bucket->name();
                
                Log::info("Firebase Storage connected successfully! Bucket: {$bucketName}");
                session()->flash('firebase_success', "Connected to Firebase Storage bucket: {$bucketName}");
                
                return $storage;
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                Log::error('Firebase initialization error: ' . $errorMessage);
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                session()->flash('firebase_error', 'Failed to connect to Firebase Storage: ' . $errorMessage);
                throw $e;
            }
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
