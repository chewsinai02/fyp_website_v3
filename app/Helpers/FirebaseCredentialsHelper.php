<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class FirebaseCredentialsHelper
{
    public static function validateAndCleanCredentials($path)
    {
        // Normalize path for Windows
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        if (!file_exists($path)) {
            throw new \Exception("Firebase credentials file not found at: {$path}");
        }

        // Check file permissions
        try {
            // Try to make file writable if it isn't
            if (!is_writable($path)) {
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    exec('icacls "' . $path . '" /grant Everyone:F');
                } else {
                    chmod($path, 0666);
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to set permissions: " . $e->getMessage());
            throw new \Exception("Cannot write to credentials file. Please check file permissions.");
        }

        try {
            $credentials = json_decode(file_get_contents($path), true);
            if (!$credentials) {
                throw new \Exception("Invalid JSON in credentials file");
            }

            // Clean private key
            if (isset($credentials['private_key'])) {
                // Remove any escaped newlines and normalize line endings
                $privateKey = str_replace(
                    ['\\n', '\n', "\r\n"],
                    "\n",
                    $credentials['private_key']
                );

                // Ensure proper PEM format
                if (!str_starts_with($privateKey, "-----BEGIN PRIVATE KEY-----\n")) {
                    $privateKey = "-----BEGIN PRIVATE KEY-----\n" . trim($privateKey, "-----BEGIN PRIVATE KEY-----");
                }
                if (!str_ends_with($privateKey, "\n-----END PRIVATE KEY-----\n")) {
                    $privateKey = rtrim($privateKey, "-----END PRIVATE KEY-----") . "\n-----END PRIVATE KEY-----\n";
                }

                $credentials['private_key'] = $privateKey;

                // Write back the cleaned credentials
                $result = file_put_contents(
                    $path, 
                    json_encode($credentials, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                );

                if ($result === false) {
                    throw new \Exception("Failed to write credentials file");
                }
            }

            return $credentials;
        } catch (\Exception $e) {
            Log::error("Failed to process credentials: " . $e->getMessage());
            throw new \Exception("Error processing credentials file: " . $e->getMessage());
        }
    }
} 