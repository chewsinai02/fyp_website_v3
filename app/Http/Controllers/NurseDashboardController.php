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

class NurseDashboardController extends Controller
{
    public function index()
    {
        // Get assigned rooms for today
        $assignedRoomIds = NurseSchedule::where('nurse_id', auth()->id())
            ->whereDate('date', today())
            ->pluck('room_id');
        
        // Get patients in assigned rooms
        $patientIds = Bed::whereIn('room_id', $assignedRoomIds)
            ->where('status', 'occupied')
            ->pluck('patient_id');
        
        // Get task counts
        $taskCount = Task::whereIn('patient_id', $patientIds)
            ->whereDate('created_at', today())
            ->count();
        
        $completedTaskCount = Task::whereIn('patient_id', $patientIds)
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();
        
        return view('nurse.nurseDashboard', compact(
            'taskCount',
            'completedTaskCount'
        ));
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
    public function patientTasks(Patient $patient)
    {
        $tasks = Task::with(['nurse', 'patient'])
            ->where('patient_id', $patient->id)
            ->whereDate('due_date', '>=', now()->startOfMonth())
            ->whereDate('due_date', '<=', now()->endOfMonth())
            ->get();

        $events = $tasks->map(function($task) {
            $colors = $this->getTaskPriorityColor($task->priority);
            return [
                'title' => $task->title ?? 'Untitled Task',
                'start' => $task->due_date,
                'backgroundColor' => $colors['bg'],
                'borderColor' => $colors['border'],
                'textColor' => $colors['text'],
                'extendedProps' => [
                    'description' => $task->description ?? 'No description',
                    'priority' => $task->priority ?? 'normal',
                    'status' => $task->status ?? 'pending',
                    'nurse' => $task->nurse ? $task->nurse->name : 'Unassigned'
                ]
            ];
        });

        $nurses = User::where('role', 'nurse')->orderBy('name')->get();
        
        return view('nurse.patientTasks', compact('patient', 'events', 'nurses'));
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'required|date'
        ]);

        $task = Task::create([
            'patient_id' => $patient->id,
            'nurse_id' => auth()->id(),
            ...$validated
        ]);

        return response()->json($task);
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
        return match($priority ?? 'normal') {
            'urgent' => [
                'bg' => '#dc3545',    // Red background
                'border' => '#bd2130', // Darker red border
                'text' => '#ffffff'    // White text
            ],
            'high' => [
                'bg' => '#ffc107',    // Yellow background
                'border' => '#d39e00', // Darker yellow border
                'text' => '#000000'    // Black text
            ],
            'medium' => [
                'bg' => '#17a2b8',    // Cyan background
                'border' => '#138496', // Darker cyan border
                'text' => '#ffffff'    // White text
            ],
            'low' => [
                'bg' => '#28a745',    // Green background
                'border' => '#1e7e34', // Darker green border
                'text' => '#ffffff'    // White text
            ],
            default => [
                'bg' => '#6c757d',    // Grey background
                'border' => '#545b62', // Darker grey border
                'text' => '#ffffff'    // White text
            ]
        };
    }

    public function schedule()
    {
        $schedules = NurseSchedule::where('nurse_id', auth()->id())
            ->with(['room'])
            ->orderBy('date')
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
}


