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
    private const CREDENTIALS_FILE = 'fyptestv2-37c45-firebase-adminsdk-tu0u8-caf619423c.json';
    
    public function register()
    {
        $this->app->singleton(Storage::class, function ($app) {
            try {
                $factory = (new Factory)
                    ->withServiceAccount(storage_path('app/firebase/' . self::CREDENTIALS_FILE))
                    ->withDefaultStorageBucket(self::STORAGE_BUCKET);
                
                return $factory->createStorage();
            } catch (\Exception $e) {
                Log::error('Firebase initialization error: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function boot()
    {
        //
    }
}
