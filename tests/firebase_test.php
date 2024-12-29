<?php

require __DIR__.'/../vendor/autoload.php';

use Kreait\Firebase\Factory;

$credentialsPath = __DIR__.'/../storage/app/firebase/fyptestv2-37c45-firebase-adminsdk-tu0u8-caf619423c.json';

try {
    // Read and decode the credentials file
    if (!file_exists($credentialsPath)) {
        throw new Exception("Credentials file not found at: {$credentialsPath}");
    }

    $credentials = json_decode(file_get_contents($credentialsPath), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON in credentials file: " . json_last_error_msg());
    }

    echo "Step 1: Credentials file loaded successfully\n";
    
    // Print credentials structure (without sensitive data)
    echo "\nCredentials structure:\n";
    echo "Project ID: " . $credentials['project_id'] . "\n";
    echo "Client Email: " . $credentials['client_email'] . "\n";
    
    // Check private key format
    $privateKey = $credentials['private_key'] ?? '';
    if (empty($privateKey)) {
        throw new Exception("Private key is missing");
    }

    echo "\nStep 2: Private key found\n";

    // Clean private key format
    $privateKey = str_replace(['\n', "\r\n"], "\n", $privateKey);
    
    // Check key structure
    if (!str_contains($privateKey, "-----BEGIN PRIVATE KEY-----")) {
        throw new Exception("Private key is missing header");
    }
    
    if (!str_contains($privateKey, "-----END PRIVATE KEY-----")) {
        throw new Exception("Private key is missing footer");
    }

    echo "Step 3: Private key format validated\n";
    
    // Create Firebase instance
    echo "\nAttempting to connect to Firebase...\n";
    
    $firebase = (new Factory)
        ->withServiceAccount($credentialsPath)
        ->withDefaultStorageBucket('fyptestv2-37c45.firebasestorage.app');
    
    $storage = $firebase->createStorage();
    $bucket = $storage->getBucket();
    
    echo "\nFirebase connection successful!\n";
    echo "Bucket name: " . $bucket->name() . "\n";
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
} 