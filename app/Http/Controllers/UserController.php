<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Display the edit form for user details
    public function adminedit($id)
    {
        $user = User::findOrFail($id); // Find the user by ID
        return view('admin.edit', compact('user'));
    }
    
    // Update method for updating user details via an admin
    public function adminupdate(Request $request, $id)
    {
        Log::info('Update method called for user ID: ' . $id);

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|string',
            'gender' => 'required|string',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'ic_number' => 'required|string',
            'address' => 'required|string',
            'blood_type' => 'required|string',
            'contact_number' => 'required|string',
            'medical_history' => 'nullable|array',
            'medical_history.*' => 'string',
            'other_medical_conditions' => 'nullable|string',
            'description' => 'nullable|string',
            'emergency_contact' => 'required|string',
            'relation' => 'required|string',
        ]);

        $user = User::findOrFail($id);

        // Update basic user details
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->gender = $request->gender;

        // Handle profile image upload
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $imageName = $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);

            if ($user->profile_picture && file_exists(public_path('images/' . $user->profile_picture))) {
                unlink(public_path('images/' . $user->profile_picture));
            }

            $user->profile_picture = 'images/' . $imageName;
        }

        // Handle medical history
        if ($request->has('medical_history')) {
            $medicalHistory = $request->medical_history;
            // If only 'none' is selected or no selection, store as null
            if (count($medicalHistory) === 1 && in_array('none', $medicalHistory)) {
                $user->medical_history = null;
            } else {
                // Filter out 'none' if other options are selected
                $medicalHistory = array_filter($medicalHistory, function($value) {
                    return $value !== 'none';
                });
                $user->medical_history = implode(',', $medicalHistory);
            }
        } else {
            $user->medical_history = null;
        }

        // Update all other fields
        $user->ic_number = $request->ic_number;
        $user->address = $request->address;
        $user->blood_type = $request->blood_type;
        $user->contact_number = $request->contact_number;
        $user->description = $request->description;
        $user->emergency_contact = $request->emergency_contact;
        $user->relation = $request->relation;

        // Save updated user details
        $user->save();

        Log::info('User updated successfully', [$user]);

        return redirect()->route('adminDashboard')->with('success', 'User updated successfully!');
    }   

    // Method to delete a user
    public function admindestroy($id)
    {
        $user = User::findOrFail($id);
        
        // Delete the profile picture if it exists
        if ($user->profile_picture && file_exists(public_path('images/' . $user->profile_picture))) {
            unlink(public_path('images/' . $user->profile_picture));
        }

        // Delete the user
        $user->delete();
    
        return redirect()->route('adminDashboard')->with('success', 'User deleted successfully!');
    }
}
