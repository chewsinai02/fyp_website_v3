<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\NurseSchedule;
use App\Models\User;
use App\Models\VitalSign;
use App\Models\Bed;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\TaskStatusCheck;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class NurseDashboardController extends Controller
{
    use TaskStatusCheck;
    public function index()
    {
        // Get assigned rooms for today with proper date filtering
        $assignedRoomIds = NurseSchedule::where('nurse_id', auth()->id())
            ->whereDate('date', today())
            ->pluck('room_id');
        
        // Get patients in assigned rooms with proper relationship loading
        $patients = Bed::whereIn('room_id', $assignedRoomIds)
            ->where('status', 'occupied')
            ->whereNotNull('patient_id')
            ->with(['patient', 'room'])
            ->get();
        
        // Debug information
        \Log::info('Nurse Dashboard Debug:', [
            'nurse_id' => auth()->id(),
            'date' => today(),
            'assigned_rooms' => $assignedRoomIds,
            'patient_count' => $patients->count(),
            'room_details' => $patients->pluck('room_id'),
            'patient_ids' => $patients->pluck('patient_id')
        ]);
        
        // Get task counts for these patients
        $taskCount = Task::whereIn('patient_id', $patients->pluck('patient_id'))
            ->whereDate('due_date', today())
            ->count();
        
        $completedTaskCount = Task::whereIn('patient_id', $patients->pluck('patient_id'))
            ->whereDate('due_date', today())
            ->where('status', 'completed')
            ->count();
        
        return view('nurse.nurseDashboard', compact(
            'taskCount',
            'completedTaskCount',
            'patients'
        ));
    }

    public function nurseManageProfile()
    {
        $user = Auth::user();
        return view('nurse.manageProfile', compact('user'));
    }

    public function nurseChangePassword()
    {
        $user = Auth::user();
        return view('nurse.changePassword', compact('user'));
    }

    public function nurseCheckCurrentPassword(Request $request)
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

    public function nurseEditProfile()
    {
        $user = Auth::user();
        return view('nurse.editProfile', compact('user'));
    }

    public function nurseUpdateProfilePicture(Request $request)
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

    public function show(User $user)
    {
        // Verify this is a patient
        if ($user->role !== 'patient') {
            abort(404);
        }

        $user->load(['bed.room']);
        $bed = Bed::where('patient_id', $user->id)
              ->with(['room'])
              ->first();
        
        $notes = DB::table('notes')
            ->join('users', 'notes.nurse_id', '=', 'users.id')
            ->select('notes.*', 'users.name as nurse_name')
            ->where('notes.patient_id', $user->id)
            ->orderBy('notes.created_at', 'desc')
            ->get();

        return view('nurse.patientView', compact('user', 'notes', 'bed'));
    }

    public function storeVitals(Request $request, User $user)
    {
        try {
            $request->validate([
                'temperature' => 'required|numeric|between:35,42',
                'blood_pressure' => 'required|string',
                'heart_rate' => 'required|integer|between:40,200',
                'respiratory_rate' => 'required|integer|between:8,40',
            ]);

            DB::table('vital_signs')->insert([
                'temperature' => $request->temperature,
                'blood_pressure' => $request->blood_pressure,
                'heart_rate' => $request->heart_rate,
                'respiratory_rate' => $request->respiratory_rate,
                'patient_id' => $user->id,
                'nurse_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return back()->with('success', 'Vital signs recorded successfully');
        } catch (\Exception $e) {
            \Log::error('Error storing vital signs: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function storeNote(Request $request, User $user)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            DB::table('notes')->insert([
                'content' => $request->content,
                'patient_id' => $user->id,
                'nurse_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return back()->with('success', 'Note added successfully');
        } catch (\Exception $e) {
            \Log::error('Error storing note: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Task-related methods
    public function patientTasks(User $patient, Request $request)
    {
        $year = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);
        
        $date = Carbon::createFromDate($year, $month, 1);

        // Get start and end dates for calendar
        $startDate = $date->copy()->firstOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endDate = $date->copy()->lastOfMonth()->endOfWeek(Carbon::SATURDAY);

        // Get tasks for this patient
        $tasks = Task::where('patient_id', $patient->id)
                     ->whereBetween('due_date', [$startDate, $endDate])
                     ->orderBy('due_date')
                     ->get();

        // Define the priority color function
        $getPriorityColor = function($priority) {
            return match(strtolower($priority)) {
                'low' => 'success',
                'medium' => 'warning',
                'high' => 'orange',
                'urgent' => 'danger',
                default => 'secondary'
            };
        };

        return view('nurse.patientTasks', compact(
            'patient',
            'tasks',
            'date',
            'startDate',
            'endDate',
            'getPriorityColor'  // Pass the function to the view
        ));
    }

    public function getTaskEvents(User $patient)
    {
        $tasks = Task::where('patient_id', $patient->id)
            ->get()
            ->map(function($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->due_date,
                    'backgroundColor' => $this->getTaskPriorityColor($task->priority),
                    'borderColor' => $this->getTaskPriorityColor($task->priority),
                    'textColor' => '#fff'
                ];
            });

        return response()->json($tasks);
    }

    public function getPatientTasks(User $patient, $date)
    {
        $tasks = Task::where('patient_id', $patient->id)
            ->whereDate('due_date', $date)
            ->orderBy('due_date')
            ->get();

        return response()->json($tasks);
    }

    public function storeTask(Request $request, User $patient)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'required|in:low,medium,high,urgent',
                'due_date' => 'required|date_format:Y-m-d\TH:i'
            ]);

            // Convert the datetime-local format to database format
            $dueDateTime = Carbon::createFromFormat(
                'Y-m-d\TH:i', 
                $validated['due_date']
            );

            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'due_date' => $dueDateTime,
                'patient_id' => $patient->id,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            \Log::error('Task creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateTask(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'required|date'
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function updateTaskStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroyTask(Task $task)
    {
        $task->delete();
        return response()->json(['success' => true]);
    }

    private function getTaskPriorityColor($priority)
    {
        return [
            'low' => '#0dcaf0',
            'medium' => '#ffc107',
            'high' => '#dc3545',
            'urgent' => '#212529'
        ][$priority] ?? '#6c757d';
    }

    public function schedule()
    {
        $today = now()->startOfDay();
        
        $schedules = NurseSchedule::where('nurse_id', auth()->id())
            ->with('room')  // Eager load room relationship
            ->orderByRaw('CASE WHEN date >= ? THEN 0 ELSE 1 END', [$today])  // Upcoming first
            ->orderBy('date', 'asc')  // Then by date ascending
            ->get();

        return view('nurse.schedule', compact('schedules'));
    }

    public function patients()
    {
        // Get today's schedule for the logged-in nurse
        $todaySchedule = NurseSchedule::where('nurse_id', auth()->id())
            ->whereDate('date', today())
            ->with(['room.beds.patient' => function($query) {
                $query->where('role', 'patient')
                      ->with(['vital_signs' => function($q) {
                          $q->latest();
                      }]);
            }])
            ->get();

        // Collect all patients from assigned rooms
        $patients = collect();
        foreach ($todaySchedule as $schedule) {
            $roomPatients = $schedule->room->beds
                ->whereNotNull('patient')
                ->pluck('patient');
            $patients = $patients->concat($roomPatients);
        }

        // Debug information
        \Log::info('Nurse Schedule:', [
            'nurse_id' => auth()->id(),
            'schedule_count' => $todaySchedule->count(),
            'patient_count' => $patients->count()
        ]);

        return view('nurse.patients', [
            'patients' => $patients,
            'schedule' => $todaySchedule
        ]);
    }

    public function tasks()
    {
        // Get rooms assigned to the nurse for today
        $assignedRoomIds = NurseSchedule::where('nurse_id', auth()->id())
            ->whereDate('date', today())
            ->pluck('room_id');

        // Get tasks for patients in assigned rooms
        $tasks = Task::with(['patient'])
            ->whereHas('patient', function($query) use ($assignedRoomIds) {
                $query->whereHas('bed', function($q) use ($assignedRoomIds) {
                    $q->whereIn('room_id', $assignedRoomIds);
                });
            })
            ->orderBy('due_date')
            ->get()
            ->groupBy(function($task) {
                return $task->due_date->format('Y-m-d');
            });

        // Add debug information
        \Log::info('Nurse Tasks:', [
            'nurse_id' => auth()->id(),
            'assigned_rooms' => $assignedRoomIds,
            'task_count' => $tasks->count()
        ]);

        return view('nurse.tasks', compact('tasks'));
    }

    public function patientTaskEvents(Patient $patient)
    {
        $tasks = Task::where('patient_id', $patient->id)
            ->get()
            ->map(function($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->due_date->format('Y-m-d H:i:s'),
                    'backgroundColor' => $this->getPriorityColor($task->priority),
                    'borderColor' => $this->getPriorityColor($task->priority),
                    'textColor' => '#fff',
                    'extendedProps' => [
                        'status' => $task->status,
                        'priority' => $task->priority,
                        'description' => $task->description
                    ]
                ];
            });

        return response()->json($tasks);
    }

    private function getPriorityColor($priority)
    {
        return [
            'low' => '#0dcaf0',    // info
            'medium' => '#ffc107', // warning
            'high' => '#dc3545',   // danger
            'urgent' => '#212529'  // dark
        ][$priority] ?? '#6c757d'; // secondary
    }

    public function showTasksList(Request $request)
    {
        // Get the currently authenticated nurse's ID
        $nurseId = auth()->id();
    
        // Retrieve the room assigned to the nurse for today
        $roomId = NurseSchedule::where('nurse_id', $nurseId)
            ->whereDate('date', today())
            ->pluck('room_id')
            ->first(); // Get the first room ID assigned for today
    
        // Check if a room ID was found
        if (!$roomId) {
            return redirect()->back()->with('error', 'No room assigned for today.');
        }
    
        // Fetch tasks associated with the room_id
        $tasks = Task::where('room_id', $roomId)
            ->whereDate('due_date', Carbon::today())
            ->orderByRaw("FIELD(status, 'pending', 'completed', 'passed')")
            ->orderBy('due_date')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->get();
        
        // Check if tasks were found
        if ($tasks->isEmpty()) {
            return view('nurse.tasks', compact('tasks', 'roomId'))->with('message', 'No tasks found for this room.');
        }
    
        // Pass both tasks and roomId to the view
        return view('nurse.tasks', compact('tasks', 'roomId'));
    }

    public function getTaskDetails($id)
    {
        try {
            \Log::info('Fetching task details for ID: ' . $id);
            
            $task = Task::with(['patient.bed'])
                ->where('id', $id)
                ->first();

            if (!$task) {
                \Log::error('Task not found with ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            \Log::info('Task details found:', $task->toArray());

            return response()->json([
                'success' => true,
                'task' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'due_date' => $task->due_date,
                    'patient' => [
                        'id' => $task->patient->id,
                        'name' => $task->patient->name
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching task details:', [
                'task_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching task details'
            ], 500);
        }
    }

    public function deleteTask($id)
    {
        \Log::info('Attempting to delete task with ID:', ['id' => $id]);
        $task = Task::find($id);

        if (!$task) {
            \Log::error('Task not found for ID:', ['id' => $id]);
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }

        $task->delete();
        \Log::info('Task deleted successfully:', ['id' => $id]);

        // Return a response indicating success
        return response()->json(['success' => true]);
    }

    public function updateStatus(Task $task, Request $request)
    {
        try {
            // Check if the due date has passed
            $now = now();
            $dueDate = \Carbon\Carbon::parse($task->due_date);
            
            if ($dueDate->isPast()) {
                $status = 'passed';
            } else {
                $status = $request->status;
            }

            $task->update([
                'status' => $status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully',
                'status' => $status // Return the actual status that was set
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task status'
            ], 500);
        }
    }
}


