<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserDataController extends Controller
{
    public function adminshow($id)
    {
        $userToEdit = User::findOrFail($id); // Retrieve the user by ID
    
        // Pass the user data to the view
        return view('auth.userData', compact('userToEdit')); // Pass the variable
    }
}


