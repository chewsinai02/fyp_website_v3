<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Room;
use App\Models\NurseSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Bed;

class NurseAdminDashboardController extends Controller
{
    public function nurseAdminindex()
    {
        $data = [
            'totalNurses' => User::where('role', 'nurse')->count(),
            'onDutyNurses' => NurseSchedule::whereDate('date', today())
                ->where('status', 'scheduled')
                ->count(),
            'assignedRooms' => Room::whereHas('schedules', function($query) {
                $query->whereDate('date', today());
            })->count(),
            'availableNurses' => User::where('role', 'nurse')
                ->whereDoesntHave('schedules', function($query) {
                    $query->whereDate('date', today())
                        ->where('status', 'scheduled');
                })->count(),
            'todaySchedules' => NurseSchedule::with(['nurse', 'room'])
                ->whereDate('date', today())
                ->orderBy('shift')
                ->get()
        ];

        $nurses = User::where('role', 'nurse')->orderBy('name')->get();

        return view('nurseAdmin.nurseAdminDashboard',  compact('data', 'nurses'));
    }

    // Nurse Management
    public function nurseList()
    {
        $nurses = User::where('role', 'nurse')
            ->with(['schedules' => function($query) {
                $query->whereDate('date', now()->toDateString())
                      ->with('room');
            }])
            ->get();

        // Add this for debugging
        \Log::info('Nurses with schedules:', [
            'count' => $nurses->count(),
            'sample' => $nurses->first()?->schedules?->toArray()
        ]);

        return view('nurseAdmin.nurseList', compact('nurses'));
    }

    public function showNurse($id)
    {
        $nurse = User::with([
            'schedules' => function($query) {
                $query->with('room')
                      ->where('date', '>=', today())
                      ->orderBy('date', 'asc');
            },
        ])->findOrFail($id);

        return view('nurseAdmin.showNurse', compact('nurse'));
    }

    // Schedule Management
    public function scheduleList(Request $request)
    {
        // Get the date from the request or default to today
        $date = $request->input('date', date('Y-m-d')); // Default to today if no date is provided

        // Retrieve schedules for the specified date
        $schedules = NurseSchedule::whereDate('date', $date)
            ->with(['nurse', 'room'])
            ->orderBy('date', 'asc')
            ->orderByRaw("CASE 
                WHEN shift = 'morning' THEN 1 
                WHEN shift = 'afternoon' THEN 2 
                WHEN shift = 'night' THEN 3 
                END")
            ->get();

        // Check if the request is an AJAX call
        if ($request->ajax()) {
            return response()->json($schedules);
        }

        // Get all nurses
        $nurses = User::where('role', 'nurse')
            ->orderBy('name')
            ->get();

        // Return the view with schedules and nurses
        return view('nurseAdmin.scheduleList', compact('schedules', 'nurses'));
    }

    public function storeSchedule(Request $request)
{
    DB::beginTransaction();

    try {
        // Validate request
        $validatedData = $request->validate([
            'nurse_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date|after_or_equal:today',
            'shift' => 'required|in:morning,afternoon,night',
            'notes' => 'nullable|string|max:255'
        ]);

        // Check for nurse schedule conflicts
        $nurseConflict = NurseSchedule::where('nurse_id', $validatedData['nurse_id'])
            ->whereDate('date', $validatedData['date'])
            ->where('shift', $validatedData['shift'])
            ->exists();

        if ($nurseConflict) {
            return response()->json([
                'success' => false,
                'message' => 'Nurse is already scheduled for this shift on the selected date.'
            ], 422);
        }

        // Create schedule
        NurseSchedule::create($validatedData);

        DB::commit();

        return response()->json(['success' => true, 'message' => 'Schedule created successfully']);
    } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['success' => false, 'message' => 'Failed to create schedule: ' . $e->getMessage()], 500);
    }
}

    public function getSchedule(NurseSchedule $schedule)
    {
        try {
            return response()->json([
                'success' => true,
                'id' => $schedule->id,
                'nurse_id' => $schedule->nurse_id,
                'room_id' => $schedule->room_id,
                'date' => $schedule->date->format('Y-m-d'),
                'shift' => $schedule->shift,
                'notes' => $schedule->notes
            ]);
        } catch (\Exception $e) {
            \Log::error('Get schedule error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get schedule details'
            ], 500);
        }
    }

    public function deleteSchedule($id)
{
    try {
        $schedule = NurseSchedule::findOrFail($id);
        $schedule->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully.'
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Schedule deleted successfully.');

    } catch (\Exception $e) {
        if (request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete schedule: ' . $e->getMessage()
            ], 500);
        }

        return back()->with('error', 'Failed to delete schedule: ' . $e->getMessage());
    }
}

    // Room Management
    public function roomList()
    {
        $rooms = Room::with(['schedules.nurse' => function($query) {
            $query->whereDate('date', today());
        }])->get();
        return view('nurseAdmin.roomList', compact('rooms'));
    }

    public function checkScheduleConflict(Request $request)
    {
        try {
            $request->validate([
                'nurse_id' => 'required|exists:users,id',
                'date' => 'required|date',
                'shift' => 'required|in:morning,afternoon,night'
            ]);

            $conflict = NurseSchedule::where('nurse_id', $request->nurse_id)
                ->whereDate('date', $request->date)
                ->where('shift', $request->shift)
                ->exists();

            return response()->json(['conflict' => $conflict]);
        } catch (\Exception $e) {
            \Log::error('Schedule conflict check error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to check schedule conflict'], 500);
        }
    }

    // Reports
    public function reports()
    {
        // Get schedule statistics
        $scheduleStats = [
            [
                'status' => 'Scheduled',
                'count' => NurseSchedule::where('status', 'scheduled')->count()
            ],
            [
                'status' => 'Completed',
                'count' => NurseSchedule::where('status', 'completed')->count()
            ],
            [
                'status' => 'Cancelled',
                'count' => NurseSchedule::where('status', 'cancelled')->count()
            ]
        ];

        // Get room statistics
        $roomStats = [
            [
                'status' => 'Available',
                'count' => Room::whereDoesntHave('schedules', function($query) {
                    $query->whereDate('date', today());
                })->count()
            ],
            [
                'status' => 'Occupied',
                'count' => Room::whereHas('schedules', function($query) {
                    $query->whereDate('date', today());
                })->count()
            ]
        ];

        // Get nurse workload statistics
        $nurseStats = User::where('role', 'nurse')
            ->withCount(['schedules' => function($query) {
                $query->whereMonth('date', now()->month);
            }])
            ->get()
            ->map(function($nurse) {
                return [
                    'name' => $nurse->name,
                    'schedules_count' => $nurse->schedules_count
                ];
            })
            ->toArray();

        return view('nurseAdmin.reports', compact('scheduleStats', 'roomStats', 'nurseStats'));
    }

    public function scheduleReport()
    {
        $schedules = NurseSchedule::with(['nurse', 'room'])
            ->whereDate('date', '>=', Carbon::now()->startOfMonth())
            ->orderBy('date')
            ->orderBy('shift')
            ->get();

        return view('nurseAdmin.scheduleReport', compact('schedules'));
    }

    public function exportReport(Request $request)
    {
        $type = $request->query('type', 'schedule');
        
        if ($type === 'schedule') {
            $data = NurseSchedule::with(['nurse'])
                ->whereDate('date', '>=', Carbon::now()->startOfMonth())
                ->orderBy('date')
                ->get();
        } else {
            $data = NurseSchedule::with(['nurse', 'room'])
                ->whereDate('date', '>=', Carbon::now()->startOfMonth())
                ->orderBy('date')
                ->get();
        }

        // Export logic here (you can use Laravel Excel or generate PDF)
        // For now, we'll just download as JSON
        return response()->json($data);
    }

    public function filterSchedules(Request $request)
    {
        try {
            \Log::info('Filter request received:', ['date' => $request->date]); // Debug log

            $date = $request->date ?? today();

            $schedules = NurseSchedule::with(['nurse', 'room'])
                ->whereDate('date', $date)
                ->orderByRaw("CASE 
                    WHEN shift = 'morning' THEN 1 
                    WHEN shift = 'afternoon' THEN 2 
                    WHEN shift = 'night' THEN 3 
                    END")
                ->get();

            \Log::info('Schedules found:', ['count' => $schedules->count()]); // Debug log

            return view('nurseAdmin.partials.scheduleRows', compact('schedules'))->render();
        } catch (\Exception $e) {
            \Log::error('Filter schedules error:', ['error' => $e->getMessage()]); // Debug log
            return response()->json(['error' => 'Failed to filter schedules'], 500);
        }
    }

    public function getCurrentAssignment($nurseId)
    {
        $currentSchedule = NurseSchedule::with('room')
            ->where('nurse_id', $nurseId)
            ->whereDate('date', today())
            ->first();

        return response()->json([
            'current_assignment' => $currentSchedule ? [
                'room_number' => $currentSchedule->room->room_number,
                'room_id' => $currentSchedule->room_id
            ] : null
        ]);
    }

    public function getAvailableRooms(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $shift = $request->shift ?? 'morning';

        $rooms = Room::select('rooms.*')
            ->selectRaw('(
                SELECT COUNT(*) 
                FROM nurse_schedules
                WHERE nurse_schedules.room_id = rooms.id
                AND DATE(nurse_schedules.date) = ?
                AND nurse_schedules.shift = ?
            ) as current_nurses', [$date, $shift])
            ->get()
            ->map(function($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'type' => $room->type,
                    'current_nurses' => $room->current_nurses,
                    'max_nurses' => $room->max_nurses_per_shift,
                    'available_slots' => $room->max_nurses_per_shift - $room->current_nurses
                ];
            });

        return response()->json($rooms);
    }

    public function editSchedule(NurseSchedule $schedule)
    {
        // Get all nurses and rooms for the dropdowns
        $nurses = User::where('role', 'nurse')
            ->orderBy('name')
            ->get();
    
        $rooms = Room::orderBy('room_number')
            ->get();

        return view('nurseAdmin.editSchedule', compact('schedule', 'nurses', 'rooms'));
    }

    public function updateSchedule(Request $request, NurseSchedule $schedule)
    {
        $validated = $request->validate([
            'nurse_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'shift' => 'required|in:morning,evening,night',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        try {
            $schedule->update($validated);

            return redirect()
                ->route('nurseadminDashboard')
                ->with('success', 'Schedule updated successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update schedule: ' . $e->getMessage());
        }
    } 

    public function nurseAdminManageProfile()
    {
        $user = Auth::user();
        return view('nurseAdmin.manageProfile', compact('user'));
    }

    public function nurseAdminChangePassword()
    {
        $user = Auth::user();
        return view('nurseAdmin.changePassword', compact('user'));
    }

    public function nurseAdminCheckCurrentPassword(Request $request)
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

    public function nurseAdminEditProfile()
    {
        $user = Auth::user();
        return view('nurseAdmin.editProfile', compact('user'));
    }

    public function nurseAdminUpdateProfilePicture(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        // Get the currently authenticated user
        $user = Auth::user();
    
        // Handle profile image upload
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $imageName = $originalName . '.' . $extension;
    
            // Check if file already exists and add a unique suffix if necessary
            $counter = 1;
            while (file_exists(public_path('images/' . $imageName))) {
                $imageName = $originalName . "($counter)." . $extension;
                $counter++;
            }
    
            // Move the image to 'public/images' directory
            $image->move(public_path('images'), $imageName);
    
            // Update with new image path
            $user->profile_picture = 'images/' . $imageName;
            $user->save(); // Save the user record
        }
    
        return redirect()->back()->with('success', 'Profile picture updated successfully!');
    } 

    public function getRoomBeds($roomId)
    {
        try {
            $beds = Bed::where('room_id', $roomId)
                ->with(['patient' => function($query) {
                    $query->select('id', 'name', 'ic_number', 'contact_number', 'blood_type');
                }])
                ->get();

            return response()->json([
                'success' => true,
                'beds' => $beds
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch beds'
            ], 500);
        }
    }
}
