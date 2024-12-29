<?php

namespace App\Helpers;

class FirebaseHelper
{
    public static function validateCredentials($path)
    {
        if (!file_exists($path)) {
            throw new \Exception("Credentials file not found at: {$path}");
        }

        $credentials = json_decode(file_get_contents($path), true);
        if (!$credentials) {
            throw new \Exception("Invalid JSON in credentials file");
        }

        // Validate private key format
        $privateKey = $credentials['private_key'] ?? '';
        if (empty($privateKey)) {
            throw new \Exception("Private key is missing");
        }

        // Clean and normalize private key format
        $privateKey = str_replace([
            '\n',
            "\r\n",
            '\\n'
        ], "\n", $privateKey);

        // Ensure proper key structure
        if (!preg_match('/^-----BEGIN PRIVATE KEY-----\n/', $privateKey)) {
            $privateKey = "-----BEGIN PRIVATE KEY-----\n" . trim($privateKey, "-----BEGIN PRIVATE KEY-----");
        }
        
        if (!preg_match('/\n-----END PRIVATE KEY-----\n$/', $privateKey)) {
            $privateKey = rtrim($privateKey, "-----END PRIVATE KEY-----") . "\n-----END PRIVATE KEY-----\n";
        }

        // Validate the complete key structure
        if (!preg_match('/^-----BEGIN PRIVATE KEY-----\n[\s\S]+\n-----END PRIVATE KEY-----\n$/', $privateKey)) {
            throw new \Exception("Invalid private key structure after normalization");
        }

        // Update credentials with normalized private key
        $credentials['private_key'] = $privateKey;
        
        // Write back cleaned credentials with proper formatting
        file_put_contents(
            $path, 
            json_encode(
                $credentials, 
                JSON_PRETTY_PRINT | 
                JSON_UNESCAPED_SLASHES | 
                JSON_UNESCAPED_UNICODE
            )
        );

        // Double-check the written file
        $writtenCredentials = json_decode(file_get_contents($path), true);
        if (!$writtenCredentials || !isset($writtenCredentials['private_key'])) {
            throw new \Exception("Failed to write credentials file properly");
        }

        return $credentials;
    }

    /**
     * Helper method to verify private key format
     */
    private static function verifyPrivateKey($key)
    {
        $lines = explode("\n", $key);
        
        // Check header and footer
        if ($lines[0] !== "-----BEGIN PRIVATE KEY-----") {
            return false;
        }
        
        if (end($lines) !== "-----END PRIVATE KEY-----") {
            return false;
        }
        
        // Check that the key content is base64 encoded
        $keyContent = implode('', array_slice($lines, 1, -1));
        return base64_decode($keyContent, true) !== false;
    }
} 