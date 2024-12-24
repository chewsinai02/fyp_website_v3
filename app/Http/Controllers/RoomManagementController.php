<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Bed;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class RoomManagementController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['beds' => function($query) {
            $query->orderBy('bed_number');
        }])
        ->withCount(['beds as available_beds' => function($query) {
            $query->where('status', 'available');
        }])
        ->orderBy('room_number')
        ->get();

        // Get all nurses for the schedule form
        $nurses = User::where('role', 'nurse')->get();
        
        return view('nurseAdmin.roomManagement', compact('rooms', 'nurses'));
    }

    public function editBeds(Request $request)
    {
        $search = $request->input('search');
        
        $patients = User::where('role', 'patient')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('ic_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('gender', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('blood_type', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('emergency_contact', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'ic_number', 'email', 'gender', 'address', 'blood_type', 'contact_number', 'emergency_contact')
            ->orderBy('name')
            ->get();

        $beds = Bed::with('patient')->get(); // Assuming you have a relationship set up
        dd($beds);

        return view('nurseAdmin.editBeds', compact('patients', 'beds', 'search'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate request
            $validated = $request->validate([
                'room_number' => 'required|string|unique:rooms,room_number',
                'floor' => 'required|integer|min:1',
                'type' => 'required|in:ward,private,icu',
                'total_beds' => 'required|integer|min:1',
                'notes' => 'nullable|string|max:255'
            ]);

            // Create room
            $room = Room::create([
                'room_number' => $validated['room_number'],
                'floor' => $validated['floor'],
                'type' => $validated['type'],
                'total_beds' => $validated['total_beds'],
                'notes' => $validated['notes']
            ]);

            // Create beds for the room
            for ($i = 1; $i <= $validated['total_beds']; $i++) {
                Bed::create([
                    'room_id' => $room->id,
                    'bed_number' => $i,
                    'patient_id' => null,
                    'status' => 'available'
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Room created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create room: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $id,
            'floor' => 'required|integer|min:1',
            'type' => 'required|string',
            'total_beds' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();
            
            $room = Room::findOrFail($id);
            $currentBedCount = $room->beds()->count();
            $newTotalBeds = $request->input('total_beds');

            // Update room details
            $room->update([
                'room_number' => $request->input('room_number'),
                'floor' => $request->input('floor'),
                'type' => $request->input('type'),
                'total_beds' => $newTotalBeds,
                'notes' => $request->input('notes'),
            ]);

            // Handle bed capacity changes
            if ($newTotalBeds > $currentBedCount) {
                // Add new beds
                for ($i = $currentBedCount + 1; $i <= $newTotalBeds; $i++) {
                    Bed::create([
                        'room_id' => $room->id,
                        'bed_number' => $i,
                        'patient_id' => null,
                        'status' => 'available'
                    ]);
                }
            } elseif ($newTotalBeds < $currentBedCount) {
                // Remove excess beds (only if they're not occupied)
                $bedsToRemove = $room->beds()
                    ->where('status', '!=', 'occupied')
                    ->orderByDesc('bed_number')
                    ->limit($currentBedCount - $newTotalBeds)
                    ->get();

                if ($bedsToRemove->count() < ($currentBedCount - $newTotalBeds)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot reduce beds: Some beds are currently occupied'
                    ], 422);
                }

                foreach ($bedsToRemove as $bed) {
                    $bed->delete();
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update room: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update room: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Find the room
            $room = Room::findOrFail($id);
            
            // Check for occupied beds
            if ($room->beds()->where('status', 'occupied')->exists()) {
                return back()->with('error', 'Cannot delete room: Some beds are currently occupied');
            }
            
            // Delete all beds associated with the room
            $room->beds()->delete();
            
            // Delete the room
            $room->forceDelete();
            
            DB::commit();
            return redirect()->route('nurseadmin.roomList')
                ->with('success', 'Room and associated beds deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting room: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete room. Please try again later.');
        }
    }

    public function addBed(Request $request, Room $room)
    {
        try {
            // Get the next bed number
            $nextBedNumber = $room->beds()->max('bed_number') + 1;

            // Create new bed
            $bed = Bed::create([
                'room_id' => $room->id,
                'bed_number' => $nextBedNumber,
                'patient_id' => null,
                'status' => 'available'
            ]);

            // Update room's total beds
            $room->increment('total_beds');

            return response()->json([
                'success' => true,
                'bed' => $bed,
                'message' => 'Bed added successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add bed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateBed(Request $request, Bed $bed)
    {   
        // Validate request
        $validated = $request->validate([
                'status' => 'required|in:available,occupied,maintenance',
                'patient_id' => 'nullable|exists:users,id'
            ]);

            // If setting to maintenance, clear patient_id
            if ($validated['status'] === 'maintenance') {
                $validated['patient_id'] = null;
            }

            // If setting to available, clear patient_id
            if ($validated['status'] === 'available') {
                $validated['patient_id'] = null;
            }

            // If setting to occupied, require patient_id
            if ($validated['status'] === 'occupied' && !$validated['patient_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient ID is required when status is occupied'
                ], 422);
            }

            $bed->update($validated);

            if ($bed->save()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bed updated successfully',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update bed in the database.',
                ], 500);
            }
    }

    public function removeBed(Bed $bed)
    {
        try {
            // Check if bed is occupied
            if ($bed->status === 'occupied') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove occupied bed'
                ], 400);
            }

            DB::beginTransaction();
            $bed->delete();
            $bed->room()->decrement('total_beds');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bed removed successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove bed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $room = Room::findOrFail($id);
            
            // If the request wants JSON (AJAX request)
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'floor' => $room->floor,
                    'total_beds' => $room->total_beds,
                    'available_beds' => $room->available_beds,
                    'type' => $room->type,
                    'notes' => $room->notes
                ]);
            }
        } catch (Exception $e) {
            \Log::error('Error fetching room: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch room details'
                ], 500);
            }

            return back()->with('error', 'Failed to fetch room details');
        }
    }

    public function getBeds($roomId)
    {
        try {
            $room = Room::findOrFail($roomId);
            $beds = $room->beds()
                ->with('patient:id,name') // Only get necessary patient fields
                ->orderBy('bed_number')
                ->get();
            
            return response()->json([
                'success' => true,
                'beds' => $beds,
                'room' => [
                    'id' => $room->id,
                    'room_number' => $room->room_number
                ]
            ]);
        } catch (Exception $e) {
            \Log::error('Error fetching beds: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch beds'
            ], 500);
        }
    }

    public function searchPatients(Request $request)
    {
        $term = $request->input('term');
        
        $patients = User::where('role', 'patient')
            ->where(function($query) use ($term) {
                $query->where('name', 'LIKE', "%{$term}%")
                      ->orWhere('email', 'LIKE', "%{$term}%")
                      ->orWhere('gender', 'LIKE', "%{$term}%")
                      ->orWhere('address', 'LIKE', "%{$term}%")
                      ->orWhere('blood_type', 'LIKE', "%{$term}%")
                      ->orWhere('contact_number', 'LIKE', "%{$term}%")
                      ->orWhere('emergency_contact', 'LIKE', "%{$term}%");
            })
            ->select('id', 'name', 'email', 'gender', 'blood_type', 'contact_number')
            ->limit(10)
            ->get();
        
        return response()->json($patients);
    }

    public function getPatientDetails($id)
    {
        try {
            $patient = User::where('id', $id)
                          ->where('role', 'patient')
                          ->select(
                              'id',
                              'name',
                              'staff_id',
                              'gender',
                              'email',
                              'ic_number',
                              'address',
                              'blood_type',
                              'contact_number',
                              'emergency_contact'
                          )
                          ->firstOrFail();

            return response()->json($patient);
        } catch (ModelNotFoundException $e) {
            \Log::error('Patient not found:', ['id' => $id]);
            return response()->json([
                'error' => 'Patient not found'
            ], 404);
        } catch (Exception $e) {
            \Log::error('Error fetching patient details:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'error' => 'Failed to fetch patient details'
            ], 500);
        }
    }

    public function manageBed(Request $request)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'id' => 'required|exists:beds,id',
                'patient_id' => 'required|exists:users,id'
            ]);

            // Check if patient is already assigned to any bed
            $existingAssignment = DB::table('beds')
                ->join('rooms', 'beds.room_id', '=', 'rooms.id')
                ->where('beds.patient_id', $request->patient_id)
                ->where('beds.status', 'occupied')
                ->select('beds.bed_number', 'rooms.room_number')
                ->first();

            if ($existingAssignment) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Patient Already Assigned',
                    'message' => "This patient is already assigned to Room {$existingAssignment->room_number}, Bed {$existingAssignment->bed_number}.",
                    'currentAssignment' => [
                        'room' => $existingAssignment->room_number,
                        'bed' => $existingAssignment->bed_number
                    ]
                ], 422);
            }

            // Find and update the bed
            $bed = Bed::findOrFail($request->id);
            $bed->patient_id = $request->patient_id;
            $bed->status = 'occupied';
            $bed->save();

            // Get room details for the response
            $room = $bed->room;

            return response()->json([
                'status' => 'success',
                'title' => 'Success!',
                'message' => 'Patient has been successfully assigned to the bed.',
                'data' => [
                    'bed_number' => $bed->bed_number,
                    'room_number' => $room->room_number
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'title' => 'Validation Error',
                'message' => 'Please check your input.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            \Log::error('Bed Management Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'title' => 'Error',
                'message' => 'An error occurred while managing the bed assignment.',
            ], 500);
        }
    }

    // Helper method to check if patient is assigned
    private function isPatientAssigned($patientId)
    {
        return Bed::where('patient_id', $patientId)
            ->where('status', 'occupied')
            ->exists();
    }

    // Helper method to get unassigned patients
    private function getUnassignedPatients()
    {
        return DB::table('users')
            ->leftJoin('beds', 'beds.patient_id', '=', 'users.id')
            ->select('users.id as patient_id', 'users.name', 'users.ic_number', 'users.email')
            ->where('users.role', 'patient')
            ->whereNull('beds.patient_id')
            ->get();
    }

    public function dischargeBed($bedId)
    {
        try {
            $bed = Bed::findOrFail($bedId);
            $bed->patient_id = null;
            $bed->status = 'maintenance';
            $bed->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Patient discharged and bed set to maintenance'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to discharge patient: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchUnassignedPatients(Request $request)
    {
        try {
            $search = $request->search;
            
            $patients = User::where('role', 'patient')
                ->whereNotExists(function ($query) {
                    $query->select('patient_id')
                        ->from('beds')
                        ->whereColumn('users.id', 'beds.patient_id')
                        ->whereNotNull('patient_id');
                })
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('ic_number', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                })
                ->select([
                    'id',
                    'name',
                    'ic_number',
                    'contact_number',
                    'gender',
                    'blood_type'
                ])
                ->orderBy('name')
                ->paginate(10);

            return response()->json([
                'patients' => $patients
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch patients: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllPatients()
    {
        try {
            $patients = DB::select("SELECT id, name, ic_number, contact_number, gender, blood_type, address, email, emergency_contact FROM users WHERE role = 'patient' ORDER BY name ASC");
            return response()->json($patients);
        } catch (Exception $e) {
            \Log::error('Error fetching patients: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch patients'
            ], 500);
        }
    }

    public function checkPatientAssignment($patientId)
    {
        // Check if the patient is already assigned to any bed
        $isAssigned = Bed::where('patient_id', $patientId)->exists();

        return response()->json(['isAssigned' => $isAssigned]);
    }

    public function editBedsPage(Request $request)
    {
        $beds = Bed::all();
        $patients = User::where('role', 'patient')->get();
        
        // Get the bed ID from the request
        $currentBedId = $request->input('id');

        // Retrieve the bed from the database
        $bed = Bed::find($currentBedId);

        // Check if the bed exists
        if (!$bed) {
            // Handle the case where the bed is not found (e.g., redirect or show an error)
            return redirect()->back()->with('error', 'Bed not found.');
        }

        // Get the current bed status from the retrieved bed
        $currentBedStatus = $bed->status; // Assuming 'status' is the column name in the beds table

        // Pass the variables to the view
        return view('nurseAdmin.editBeds', compact('patients', 'currentBedId', 'currentBedStatus'));
    }

    public function changeStatus(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'bed_id' => 'required|exists:beds,id',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Find the bed by ID
        $bed = Bed::find($request->bed_id);
        if (!$bed) {
            return response()->json(['success' => false, 'message' => 'Resource not found.'], 404);
        }

        // Update the bed status
        $bed->status = $request->status;
        $bed->notes = $request->notes;
        $bed->save();

        return response()->json(['success' => true, 'message' => 'Bed status updated successfully!']);
    }

    public function getAvailableBeds($roomId)
    {
        // Validate the room ID
        $room = Room::find($roomId);
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        // Fetch available beds for the specified room
        $availableBeds = Bed::where('room_id', $roomId)
            ->where('status', 'available') // Assuming 'available' is the status for available beds
            ->get();

        // Return the available beds as a JSON response
        return response()->json(['beds' => $availableBeds]);
    }

    public function transferPatient(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'bed_id' => 'required|exists:beds,id', // New bed ID
            'current_bed_id' => 'required|exists:beds,id', // Current bed ID must be required
        ]);

        // Find the current bed that needs to be updated
        $currentBed = Bed::find($request->current_bed_id); // Use current_bed_id from the request
        if (!$currentBed) {
            return response()->json(['success' => false, 'message' => 'Current bed not found.'], 404);
        }

        // Find the new bed where the patient will be transferred
        $newBed = Bed::find($request->bed_id); // Use bed_id from the request
        if (!$newBed) {
            return response()->json(['success' => false, 'message' => 'New bed not found.'], 404);
        }

        try {
            // Update the current bed's status to maintenance and clear the patient_id
            $currentBed->status = 'maintenance'; // Change status to maintenance
            $currentBed->patient_id = null; // Set patient_id to null
            $currentBed->updated_at = now(); // Update the timestamp
            $currentBed->save(); // Save the changes
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating current bed: ' . $e->getMessage()], 500);
        }

        try {
            // Update the new bed with the patient_id and set status to occupied
            $newBed->patient_id = $request->patient_id; // Assign the patient to the new bed
            $newBed->status = 'occupied'; // Change status to occupied
            $newBed->updated_at = now(); // Update the timestamp
            $newBed->save(); // Save the changes
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error saving patient to new bed: ' . $e->getMessage()], 500);
        }

        // Return a success response
        return response()->json(['success' => true, 'message' => 'Patient transferred successfully!']);
    }

    public function getBedsForRoom($roomId)
    {
        try {
            // Authorization is now handled by middleware, so we can remove the manual checks
            $room = Room::findOrFail($roomId);
            $beds = $room->beds()
                ->with(['patient' => function($query) {
                    $query->select('id', 'name', 'ic_number', 'gender', 'blood_type', 'contact_number');
                }])
                ->orderBy('bed_number')
                ->get();
            
            return response()->json([
                'success' => true,
                'beds' => $beds,
                'room' => [
                    'id' => $room->id,
                    'room_number' => $room->room_number
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        } catch (Exception $e) {
            \Log::error('Error fetching beds:', [
                'room_id' => $roomId,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch beds'
            ], 500);
        }
    }
} 