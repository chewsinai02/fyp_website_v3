<?php

return [
    'credentials' => storage_path('app/firebase/firebase-credentials.json'),
    'database_url' => env('FIREBASE_DATABASE_URL', 'https://fyptestv2-37c45.firebaseio.com'),
    'config' => [
        'apiKey' => env('FIREBASE_API_KEY', 'AIzaSyAiElkmNSl0K-N0Rz4kuqKAXrr6Eg7oo64'),
        'authDomain' => env('FIREBASE_AUTH_DOMAIN', 'fyptestv2-37c45.firebaseapp.com'),
        'projectId' => env('FIREBASE_PROJECT_ID', 'fyptestv2-37c45'),
        'storageBucket' => env('FIREBASE_STORAGE_BUCKET', 'fyptestv2-37c45.firebasestorage.app'),
        'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID', '500961952253'),
        'appId' => env('FIREBASE_APP_ID', '1:500961952253:web:a846193490974d3667d994'),
    ]
]; 
