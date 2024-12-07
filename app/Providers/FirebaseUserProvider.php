<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\User;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class FirebaseUserProvider implements UserProvider
{
    protected $auth;

    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }

    public function retrieveById($identifier)
    {
        try {
            $firebaseUser = $this->auth->getUser($identifier);
            return $this->mapFirebaseUserToModel($firebaseUser);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Not implemented for Firebase
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials['firebase_uid'])) {
            return null;
        }

        return $this->retrieveById($credentials['firebase_uid']);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return false;
    }

    protected function mapFirebaseUserToModel($firebaseUser)
    {
        $user = new User();
        $user->uid = $firebaseUser->uid;
        $user->email = $firebaseUser->email;
        $user->name = $firebaseUser->displayName;
        return $user;
    }
} 