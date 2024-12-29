<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Helpers\ImageUploadHelper;

class UserController extends Controller
{
    protected $imageUploader;

    public function __construct(ImageUploadHelper $imageUploader)
    {
        $this->imageUploader = $imageUploader;
    }

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

        // Separate validation rules for profile picture
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|string',
            'gender' => 'required|string',
            'ic_number' => 'required|string',
            'address' => 'required|string',
            'blood_type' => 'required|in:rh+ a,rh- a,rh+ b,rh- b,rh+ ab,rh- ab,rh+ o,rh- o',
            'contact_number' => 'required|string',
            'medical_history' => 'nullable|array',
            'medical_history.*' => 'string',
            'other_medical_conditions' => 'nullable|string',
            'description' => 'nullable|string',
            'emergency_contact' => 'required|string',
            'relation' => 'required|string',
        ];

        // Add profile_picture validation only if a file was uploaded
        if ($request->hasFile('profile_picture')) {
            $validationRules['profile_picture'] = 'image|mimes:jpg,jpeg,png,gif|max:5120'; // 5MB max
        }

        // Validate the request data
        $request->validate($validationRules);

        $user = User::findOrFail($id);

        // Update basic user details
        $user->fill($request->except('profile_picture', 'medical_history'));

        // Handle profile image upload
        if ($request->hasFile('profile_picture')) {
            try {
                $url = $this->imageUploader->uploadImage(
                    $request->file('profile_picture'),
                    $user->role,
                    $user->id
                );
                
                // Delete old image if exists and not a default image
                if ($user->profile_picture && !str_starts_with($user->profile_picture, 'images/')) {
                    $this->imageUploader->deleteImage($user->profile_picture);
                }
                
                $user->profile_picture = $url;
            } catch (\Exception $e) {
                Log::error('Profile image upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withErrors(['error' => 'Failed to upload image: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        // Handle medical history
        if ($request->has('medical_history')) {
            $medicalHistory = $request->medical_history;
            if (count($medicalHistory) === 1 && in_array('none', $medicalHistory)) {
                $user->medical_history = null;
            } else {
                $medicalHistory = array_filter($medicalHistory, function($value) {
                    return $value !== 'none';
                });
                $user->medical_history = implode(',', $medicalHistory);
            }
        } else {
            $user->medical_history = null;
        }

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
