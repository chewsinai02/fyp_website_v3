<?php

namespace App\Http\Controllers;

use App\Models\User; // Import the User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Helpers\FirebaseStorage;
use Google\Cloud\Storage\StorageClient;
use App\Helpers\ImageUploadHelper;

class AdminDashboardController extends Controller
{
    protected $imageUploader;

    public function __construct(ImageUploadHelper $imageUploader)
    {
        $this->imageUploader = $imageUploader;
    }

    public function adminindex()
    {
        // Fetch all users from the database
        $users = User::all();

        // Return the admin dashboard view with the users
        return view('admin.adminDashboard', ['users' => $users]);
    }

    public function admindetailshow($id) // Correct method for showing details
    {
        $user = User::findOrFail($id);
        return view('admin.allDetails', compact('user'));
    }

    public function adminManageProfile()
    {
        $user = Auth::user();
        return view('admin.adminManageProfile', compact('user'));
    }

    public function adminChangePassword()
    {
        $user = Auth::user();
        return view('admin.adminChangePassword', compact('user'));
    }

    public function adminCheckCurrentPassword(Request $request)
    {
        // Validate the input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Check if the new password is the same as the current password
        if (Hash::check($request->new_password, $user->password)) {
            return redirect()->back()->withErrors(['new_password' => 'The new password cannot be the same as the current password.']);
        }

        // Update the password
        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Redirect back with a success message
            return redirect()->route('adminChangePassword')->with('success', 'Password changed successfully! Please log in again.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['update_failed' => 'Failed to update password. Please try again.']);
        }
    }        

    public function adminEditProfile()
    {
        $user = Auth::user();
        return view('admin.adminEditProfile', compact('user'));
    }

    public function adminUpdateProfilePicture(Request $request)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Validate all fields
        $request->validate([
            'profile_picture_url' => 'nullable|string|url', // For Firebase URL
            'contact_number' => 'nullable|string',
            'address' => 'nullable|string',
            'blood_type' => 'nullable|string',
            'gender' => 'nullable|string',
            'medical_history' => 'nullable|array',
            'medical_history.*' => 'string',
            'description' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'relation' => 'nullable|string',
        ]);

        // Update profile picture URL
        if ($request->has('profile_picture_url')) {
            $user->profile_picture = $request->profile_picture_url;
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
        }

        // Update other fields if they are present in the request
        $fields = [
            'contact_number',
            'address',
            'blood_type',
            'gender',
            'description',
            'emergency_contact',
            'relation'
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $user->$field = $request->$field;
            }
        }

        // Save all changes
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }  
    
    public function searchUser(Request $request)
    {
        $query = $request->input('queryUser');
        
        // Fetching users matching the search query without filtering by role
        $users = User::where(function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%")
                          ->orWhere('role', 'LIKE', "%{$query}%")
                          ->orWhere('gender', 'LIKE', "%{$query}%")
                          ->orWhere('staff_id', 'LIKE', "%{$query}%")
                          ->orWhereRaw("CAST(ic_number AS CHAR) LIKE ?", ["%{$query}%"])
                          ->orWhereRaw("CAST(contact_number AS CHAR) LIKE ?", ["%{$query}%"])
                          ->orWhere('address', 'LIKE', "%{$query}%")
                          ->orWhere('blood_type', 'LIKE', "%{$query}%");
                    })
                    ->get();
    
        return view('admin.searchUserResult', compact('users'));
    }
    
    public function nurseadminList(Request $request)
    {
        return view('admin.nurseadminList');
    }

    public function nurseList(Request $request)
    {
        return view('admin.nurseList');
    }

    public function patientList(Request $request)
    {
        return view('admin.patientList');
    }
}
