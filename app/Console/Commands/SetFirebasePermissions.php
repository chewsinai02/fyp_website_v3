<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetFirebasePermissions extends Command
{
    protected $signature = 'firebase:permissions';
    protected $description = 'Set proper permissions for Firebase credentials';

    public function handle()
    {
        $path = storage_path('app/firebase');
        $file = $path . DIRECTORY_SEPARATOR . 'fyptestv2-37c45-firebase-adminsdk-tu0u8-2ebcfea884.json';

        // Create directory if it doesn't exist
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
            $this->info('Created firebase directory');
        }

        // Set directory permissions
        chmod($path, 0777);
        $this->info('Set directory permissions');

        // Set file permissions if file exists
        if (file_exists($file)) {
            chmod($file, 0666);
            $this->info('Set file permissions');
        } else {
            $this->error('Credentials file not found');
        }
    }
} 