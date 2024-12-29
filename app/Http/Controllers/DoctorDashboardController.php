<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Appointment;
use App\Models\Message;
use App\Models\Conversation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DoctorDashboardController extends Controller
{
    public function doctorindex()
    {
        $currentDateTime = now();

        // First update status of passed appointments
        Appointment::where('status', 'active')
            ->where(function ($query) use ($currentDateTime) {
                $query->whereDate('appointment_date', '<', $currentDateTime->toDateString())
                    ->orWhere(function ($q) use ($currentDateTime) {
                        $q->whereDate('appointment_date', '=', $currentDateTime->toDateString())
                            ->whereTime('appointment_time', '<', $currentDateTime->toTimeString());
                    });
            })
            ->update(['status' => 'pass']);

        // Fetch only active appointments count (after updating statuses)
        $activeAppointmentsCount = Appointment::where('status', 'active')->count();
        
        // Fetch active appointments ordered by appointment date and time
        $activeAppointments = Appointment::with(['patient', 'doctor'])
                                         ->where('status', 'active')
                                         ->orderBy('appointment_date')   // Order by date
                                         ->orderBy('appointment_time')   // Order by time
                                         ->get();
        
        // Return the view with both variables
        return view('doctor.doctorDashboard', [
            'appointments' => $activeAppointments,
            'activeAppointmentsCount' => $activeAppointmentsCount
        ]);
    }      

    public function doctorManageProfile()
    {
        $user = Auth::user();
        return view('doctor.doctorManageProfile', compact('user'));
    }

    public function doctorChangePassword()
    {
        $user = Auth::user();
        return view('doctor.doctorChangePassword', compact('user'));
    }

    public function doctorCheckCurrentPassword(Request $request)
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
            return redirect()->route('doctorChangePassword')->with('success', 'Password changed successfully! Please log in again.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['update_failed' => 'Failed to update password. Please try again.']);
        }
    }            

    public function doctorEditProfile()
    {
        $user = Auth::user();
        return view('doctor.doctorEditProfile', compact('user'));
    }

    public function doctorUpdateProfilePicture(Request $request)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Validate all fields
        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120', // 5MB max
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

        // Handle profile image upload to Firebase
        if ($request->hasFile('profile_picture')) {
            try {
                $file = $request->file('profile_picture');
                
                // Create Firebase Storage URL format
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $path = "assets/images/{$filename}";
                
                // Get Firebase bucket
                $storage = app(\Kreait\Firebase\Storage::class);
                $bucket = $storage->getBucket();
                
                // Upload file
                $bucket->upload(
                    $file->get(),
                    [
                        'name' => $path,
                        'metadata' => [
                            'contentType' => $file->getMimeType(),
                        ]
                    ]
                );

                // Generate Firebase Storage URL
                $url = "https://firebasestorage.googleapis.com/v0/b/fyptestv2-37c45.firebasestorage.app/o/" . 
                       urlencode($path) . "?alt=media";

                // Update user's profile picture URL
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

    // Appointment list
    public function doctorAppointmentIndex()
    {
        $currentDateTime = now();

        // Update status of passed appointments
        Appointment::where('status', 'active')
            ->where(function ($query) use ($currentDateTime) {
                $query->whereDate('appointment_date', '<', $currentDateTime->toDateString())
                    ->orWhere(function ($q) use ($currentDateTime) {
                        $q->whereDate('appointment_date', '=', $currentDateTime->toDateString())
                            ->whereTime('appointment_time', '<', $currentDateTime->toTimeString());
                    });
            })
            ->update(['status' => 'pass']);

        // Fetch all appointments with proper ordering and relationships
        $appointments = Appointment::with(['patient', 'doctor'])
            ->where('doctor_id', auth()->id())
            ->orderByRaw("
                CASE 
                    WHEN status = 'active' THEN 1
                    WHEN status = 'done' THEN 2
                    WHEN status = 'pass' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        return view('doctor.doctorAppointmentList', compact('appointments'));
    }    

    // Show the details of a specific appointment
    public function doctorAppointmentShow($id)
    {
        $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($id);
        return view('doctor.patientDetails', compact('appointment'));
    }

    // Show the form for editing a specific appointment
    public function doctorAppointmentEdit($id)
    {
        $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($id);
        return view('doctor.doctorAppointmentList', compact('appointment'));
    }

    // Update a specific appointment
    public function doctorAppointmentUpdate(Request $request, $id)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'status' => 'required|in:done,pass,active',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->all());

        return redirect()->route('doctorAppointment.index')->with('success', 'Appointment updated successfully');
    }

    // Delete a specific appointment
    public function doctorAppointmentDestroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->route('doctorAppointment.index')->with('success', 'Appointment deleted successfully');
    }

    public function searchActiveAppointments(Request $request)
    {
        $query = $request->input('queryActiveAppointments');
    
        // Perform a join between appointments and users on patient_id and user id
        $appointments = Appointment::join('users', 'appointments.patient_id', '=', 'users.id')
            ->where('appointments.status', 'active') // Only fetch active appointments
            ->where(function ($q) use ($query) {
                // Search both user and appointment fields
                $q->where('users.name', 'LIKE', "%{$query}%")
                  ->orWhere('users.ic_number', 'LIKE', "%{$query}%")
                  ->orWhere('users.contact_number', 'LIKE', "%{$query}%")
                  ->orWhere('users.gender', 'LIKE', "%{$query}%")
                  ->orWhere('appointments.appointment_date', 'LIKE', "%{$query}%")
                  ->orWhere('appointments.appointment_time', 'LIKE', "%{$query}%")
                  ->orWhere('appointments.notes', 'LIKE', "%{$query}%");
            })
            ->select('appointments.*', 'users.name as patient_name', 'users.ic_number', 'users.contact_number', 'users.profile_picture')
            ->orderBy('appointment_date')   // Order by date
            ->orderBy('appointment_time')  
            ->get();
    
        // Pass the filtered appointments to the view
        return view('doctor.searchActiveAppointments', compact('appointments'));
    }    

    public function searchAppointments(Request $request)
    {
        $query = $request->input('queryAppointments');
    
        // Perform a join between appointments and users on patient_id and user id
        $appointments = Appointment::join('users', 'appointments.patient_id', '=', 'users.id')
            ->where(function ($q) use ($query) {
                // Search both user and appointment fields
                $q->where('users.name', 'LIKE', "%{$query}%")
                  ->orWhere('users.ic_number', 'LIKE', "%{$query}%")
                  ->orWhere('users.contact_number', 'LIKE', "%{$query}%")
                  ->orWhere('users.gender', 'LIKE', "%{$query}%")
                  ->orWhere('appointments.status', 'LIKE', "%{$query}%")
                  ->orWhere('appointments.appointment_date', 'LIKE', "%{$query}%")
                  ->orWhere('appointments.appointment_time', 'LIKE', "%{$query}%")
                  ->orWhere('appointments.notes', 'LIKE', "%{$query}%");
            })
            ->select('appointments.*', 'users.name as patient_name', 'users.ic_number', 'users.contact_number', 'users.gender','users.profile_picture')
            ->orderBy('appointment_date')   // Order by date
            ->orderBy('appointment_time')   // Order by time
            ->get();
    
        return view('doctor.searchAppointments', compact('appointments'));
    }           

    //messages and chatting
    // Display all patients with whom the doctor has exchanged messages
    public function doctorMessage()
    {
        $doctorId = auth()->id();
        
        // Fetch all conversations where the doctor is either the sender or receiver
        $messages = Message::with(['sender', 'receiver'])
            ->where('sender_id', $doctorId)
            ->orWhere('receiver_id', $doctorId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($doctorId) {
                return $message->sender_id === $doctorId ? $message->receiver_id : $message->sender_id;
            });
        
        // Get the last message for each conversation (patient)
        $lastMessages = $messages->map(function ($messageGroup) {
            // Get the latest message in the conversation
            $lastMessage = $messageGroup->first();
            $lastMessage->last_message = $lastMessage->message;
            $lastMessage->last_message_time = $lastMessage->created_at->format('h:i A');
            return $lastMessage;
        });
        
        return view('doctor.doctorMessage', compact('lastMessages'));
    }       

     // Display the chat history between doctor and a specific patient
    // Controller: doctorshow
    public function doctorshow($patientId)
    {
        $doctorId = auth()->id();
        
        // Mark messages as read
        Message::where('sender_id', $patientId)
            ->where('receiver_id', $doctorId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($doctorId, $patientId) {
                $query->where('sender_id', $doctorId)
                      ->where('receiver_id', $patientId);
            })
            ->orWhere(function ($query) use ($doctorId, $patientId) {
                $query->where('sender_id', $patientId)
                      ->where('receiver_id', $doctorId);
            })
            ->orderBy('created_at')
            ->get();
    
        $patient = User::find($patientId);
    
        return view('doctor.doctorChat', compact('messages', 'patient'));
    }
    
    public function destroy($id)
    {
        try {
            $message = Message::findOrFail($id);
            
            // Check if user is authorized to delete this message
            if ($message->sender_id != auth()->id() && $message->receiver_id != auth()->id()) {
                return back()->with('error', 'You are not authorized to delete this message.');
            }

            // Delete all messages in the conversation between these two users
            Message::where(function($query) use ($message) {
                $query->where([
                    ['sender_id', $message->sender_id],
                    ['receiver_id', $message->receiver_id]
                ])->orWhere([
                    ['sender_id', $message->receiver_id],
                    ['receiver_id', $message->sender_id]
                ]);
            })->delete();

            return back()->with('success', 'Conversation deleted successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Error deleting message:', [
                'message_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Could not delete the conversation.');
            }
        }
    
     // Store a new message from the doctor
     public function doctorstore(Request $request, $receiverId)
     {
        try {
            $request->validate([
                'message' => 'nullable|string|max:500',
                'image' => 'nullable|image|max:5120', // 5MB max
            ]);

            // Initialize message text
            $messageText = $request->message;

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                try {
                    $file = $request->file('image');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('chat_images'), $fileName);
                    $imagePath = 'chat_images/' . $fileName;
                    
                    // If no text message, set a default message for image
                    if (empty($messageText)) {
                        $messageText = '[Image]';
                    }
                } catch (\Exception $e) {
                    \Log::error('Error uploading image:', [
                        'error' => $e->getMessage()
                    ]);
                    throw new \Exception('Failed to upload image. Please try again.');
                }
            }

            // Ensure there's either a message or an image
            if (!$messageText && !$imagePath) {
                throw new \Exception('Please provide a message or image.');
            }

            $message = Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $receiverId,
                'message' => $messageText,
                'image' => $imagePath,
                'created_at' => now('Asia/Kuala_Lumpur'),
            ]);

            // Load relationships for the response
            $message->load('sender', 'receiver');

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending message:', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
 
     // Delete a message
     public function doctordestroy($messageId)
     {
         $message = Message::findOrFail($messageId);
     
         if ($message->sender_id == auth()->id()) {
             $message->delete();
         }
     
         return back();
     }
     
     public function editPatientDetails($id)
     {
        // Get the patient
        $patient = User::findOrFail($id);
    
        // Create a simple object with patient data
        $appointment = (object)[
            'patient' => $patient
        ];

        return view('doctor.doctorEditPatientDetails', compact('appointment'));
    }

    public function doctorUpdatePatientDetails(Request $request, $id)
    {
        // Validate only the medical information fields
        $request->validate([
            'blood_type' => 'required|string',
            'medical_history' => 'nullable|array',
            'medical_history.*' => 'string',
            'description' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);

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

        // Update only medical-related fields
        $user->blood_type = $request->blood_type;
        $user->description = $request->description;

        // Save updated user details
        $user->save();

        return redirect()->route('doctorDashboard')->with('success', 'Medical information updated successfully!');
    } 

    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }

    public function sendMessage(Request $request, $receiverId)
    {
        try {
            $request->validate([
                'message' => 'nullable|string|max:500',
                'image' => 'nullable|string', // Changed to string to accept Firebase URLs
            ]);

            // Initialize message text
            $messageText = $request->message;
            $imagePath = null;

            // Handle image URL from Firebase
            if ($request->image && str_contains($request->image, 'firebasestorage.googleapis.com')) {
                $imagePath = $request->image; // Store the full Firebase URL
                
                // If no text message, set a default message for image
                if (empty($messageText)) {
                    $messageText = '[Image]';
                }
            }

            // Ensure there's either a message or an image
            if (!$messageText && !$imagePath) {
                throw new \Exception('Please provide a message or image.');
            }

            $message = Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $receiverId,
                'message' => $messageText,
                'image' => $imagePath,
                'created_at' => now('Asia/Kuala_Lumpur'),
            ]);

            // Load relationships for the response
            $message->load('sender', 'receiver');

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending message:', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
