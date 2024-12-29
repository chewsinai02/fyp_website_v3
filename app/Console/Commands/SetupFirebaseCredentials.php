<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupFirebaseCredentials extends Command
{
    protected $signature = 'firebase:setup';
    protected $description = 'Setup Firebase credentials file';

    public function handle()
    {
        $sourcePath = base_path('fyptestv2-37c45-firebase-adminsdk-tu0u8-2ebcfea884.json');
        $targetDir = storage_path('app/firebase');
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . 'fyptestv2-37c45-firebase-adminsdk-tu0u8-2ebcfea884.json';

        try {
            // Create directory if it doesn't exist
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
                $this->info('Created firebase directory');
            }

            // Copy file if it exists in root
            if (file_exists($sourcePath)) {
                copy($sourcePath, $targetPath);
                $this->info('Copied credentials file to storage');
            }

            // Set permissions using icacls for Windows
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Set directory permissions
                exec('icacls "' . $targetDir . '" /grant Everyone:(OI)(CI)F /T');
                $this->info('Set directory permissions');

                // Set file permissions
                if (file_exists($targetPath)) {
                    exec('icacls "' . $targetPath . '" /grant Everyone:F');
                    $this->info('Set file permissions');
                }
            } else {
                // For non-Windows systems
                chmod($targetDir, 0777);
                if (file_exists($targetPath)) {
                    chmod($targetPath, 0666);
                }
                $this->info('Set permissions successfully');
            }

            $this->info('Firebase credentials setup complete!');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Please run this command as Administrator/root');
        }
    }
} 