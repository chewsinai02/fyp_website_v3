<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NurseSchedule;
use App\Models\User;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Task;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use App\Models\Bed;
use App\Traits\TaskStatusCheck;

class NurseCalendarController extends Controller
{
    use TaskStatusCheck;

    public function index(Request $request, $patientId)
    {
        $this->updatePassedTasks();
        
        $today = Carbon::now();
        $month = $request->get('month', $today->month);
        $year = $request->get('year', $today->year);
        $date = Carbon::createFromDate($year, $month, 1);
        
        // Get the start and end dates for the calendar
        $startDate = $date->copy()->startOfMonth()->startOfWeek();
        $endDate = $date->copy()->endOfMonth()->endOfWeek();
        
        // Get patient details
        $patient = Patient::findOrFail($patientId);
        
        // Get tasks for the selected month
        $tasks = Task::where('patient_id', $patientId)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->orderBy('due_date')
            ->get();

        return view('nurse.patientTasks', compact(
            'patient',
            'tasks',
            'date',
            'startDate',
            'endDate',
            'today'
        ));
    }

    public function getTaskDetails(Request $request, $patientId)
    {
        $this->updatePassedTasks();
        
        try {
            // Explicitly select all columns from the tasks table
            $task = Task::select('*')
                        ->where('id', $request->task_id)
                        ->where('patient_id', $patientId)
                        ->firstOrFail();

            return response()->json($task);
        } catch (\Exception $e) {
            \Log::error('Error fetching task details:', [
                'task_id' => $request->task_id,
                'patient_id' => $patientId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Task not found'
            ], 404);
        }
    }

    public function updateTaskStatus(Request $request, $patientId, $taskId)
    {
        $this->updatePassedTasks();
        
        $validatedData = $request->validate([
            'status' => 'required|string|in:completed,pending,cancelled,passed',
        ]);

        try {
            $task = Task::where('id', $taskId)
                       ->where('patient_id', $patientId)
                       ->firstOrFail();

            // Check if the task's due date has passed
            if (Carbon::parse($task->due_date)->isPast() && $validatedData['status'] === 'pending') {
                $task->status = 'passed';
            } else {
                $task->status = $validatedData['status'];
            }

            $task->save();

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully',
                'status' => $task->status // Return the actual status that was set
            ]);
        } catch (\Exception $e) {
            Log::error('Task Status Update Error:', [
                'task_id' => $taskId,
                'patient_id' => $patientId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update task status'
            ], 422);
        }
    }
    public function store(Request $request, $patientId)
    {
        $this->updatePassedTasks();
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string',
            'due_date' => 'required|date',
        ]);
    
        try {
            // Fetch room ID associated with the patient
            $bed = Bed::where('patient_id', $patientId)->first();
    
            if (!$bed) {
                return response()->json([
                    'success' => false,
                    'message' => 'No bed assigned for this patient, hence no room found.'
                ], 422);
            }
    
            $roomId = $bed->room_id;
            \Log::info("Retrieved room_id: $roomId for patient_id: $patientId");
    
            // Create the task
            $task = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'priority' => $validatedData['priority'],
                'due_date' => $validatedData['due_date'],
                'room_id' => $roomId,
                'patient_id' => $patientId,
                'status' => 'pending'
            ]);
    
            \Log::info("Task created: ", $task->toArray());

            // Call the updateRoomId method to ensure room IDs are updated
            $this->updateRoomId();
    
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'task' => $task
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving task:', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'validated_data' => $validatedData
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to create task. Please try again.'
            ], 500);
        }
    }      

    public function destroy(Request $request, $patientId, $taskId)
    {
        $this->updatePassedTasks();
        
        try {
            // Ensure the task belongs to the specified patient
            $task = Task::where('id', $taskId)
                        ->where('patient_id', $patientId) // Use the patientId from the route
                        ->firstOrFail();

            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Task Delete Error:', [
                'task_id' => $taskId,
                'patient_id' => $patientId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete task'
            ], 422);
        }
    }

    public function action(Request $request)
    {
        if($request->ajax()) {
            try {
                if($request->type == 'add') {
                    // Validate the request
                    $validated = $request->validate([
                        'nurse_id' => 'required|exists:users,id',
                        'room_id' => 'required|exists:rooms,id',
                        'shift' => 'required|in:morning,evening,night',
                        'date' => 'required|date|after_or_equal:today',
                        'notes' => 'nullable|string'
                    ]);

                    // Check for existing schedule
                    $existingSchedule = NurseSchedule::where([
                        'date' => $request->date,
                        'nurse_id' => $request->nurse_id,
                        'shift' => $request->shift,
                    ])->first();

                    if ($existingSchedule) {
                        return response()->json([
                            'success' => false,
                            'message' => 'A schedule already exists for this nurse on the selected date and shift'
                        ], 422);
                    }

                    $schedule = NurseSchedule::create([
                        'nurse_id' => $request->nurse_id,
                        'room_id' => $request->room_id,
                        'date' => $request->date,
                        'shift' => $request->shift,
                        'status' => 'scheduled',
                        'notes' => $request->notes
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Schedule created successfully',
                        'data' => $schedule
                    ]);
                }

                if($request->type == 'update') {
                    $schedule = NurseSchedule::find($request->id);
                    if($schedule) {
                        $schedule->update([
                            'date' => $request->date,
                            'nurse_id' => $request->nurse_id,
                            'room_id' => $request->room_id,
                            'shift' => $request->shift,
                            'notes' => $request->notes
                        ]);
                    }
                    return response()->json($schedule);
                }

                if($request->type == 'delete') {
                    $schedule = NurseSchedule::find($request->id);
                    if($schedule) {
                        $schedule->delete();
                    }
                    return response()->json(['success' => true]);
                }
            } catch (\Exception $e) {
                \Log::error('Schedule Action Error:', [
                    'type' => $request->type,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process schedule: ' . $e->getMessage()
                ], 422);
            }
        }
    }

    private function getShiftColor($shift)
    {
        return match($shift) {
            'morning' => [
                'bg' => '#E3F2FD',    // Light blue background
                'border' => '#2196F3', // Blue border
                'text' => '#1565C0'    // Dark blue text
            ],
            'evening' => [
                'bg' => '#FFF3E0',    // Light orange background
                'border' => '#FF9800', // Orange border
                'text' => '#E65100'    // Dark orange text
            ],
            'night' => [
                'bg' => '#E8EAF6',    // Light indigo background
                'border' => '#3F51B5', // Indigo border
                'text' => '#283593'    // Dark indigo text
            ],
            default => [
                'bg' => '#ECEFF1',    // Light grey background
                'border' => '#607D8B', // Blue grey border
                'text' => '#37474F'    // Dark grey text
            ]
        };
    }

    public function assignWeek(Request $request)
    {
        try {
            $request->validate([
                'nurse_id' => 'required|exists:users,id',
                'room' => 'required|exists:rooms,id',
                'shift' => 'required|in:morning,evening,night',
                'start_date' => 'required|date|after_or_equal:today',
                'rest_days' => 'required|array|min:1',
                'rest_days.*' => 'integer|between:0,6',
            ]);

            $startDate = Carbon::parse($request->start_date);
            $endDate = $startDate->copy()->addDays(6);
            $restDays = $request->rest_days;

            $schedules = [];
            $skippedDates = [];
            
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                // Skip if it's a rest day
                if (in_array($date->dayOfWeek, $restDays)) {
                    continue;
                }

                // Check if schedule already exists
                $existingSchedule = NurseSchedule::where([
                    'date' => $date->format('Y-m-d'),
                    'nurse_id' => $request->nurse_id,
                    'shift' => $request->shift,
                ])->first();

                if ($existingSchedule) {
                    $skippedDates[] = $date->format('Y-m-d');
                    continue;
                }

                $schedule = NurseSchedule::create([
                    'nurse_id' => $request->nurse_id,
                    'room' => $request->room,
                    'shift' => $request->shift,
                    'date' => $date->format('Y-m-d'),
                    'status' => 'scheduled'
                ]);

                $schedules[] = $schedule;
            }

            $message = 'Weekly schedule assigned successfully';
            if (!empty($skippedDates)) {
                $message .= '. Skipped dates with existing assignments: ' . implode(', ', $skippedDates);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'schedules' => $schedules,
                'skipped_dates' => $skippedDates
            ]);

        } catch (\Exception $e) {
            \Log::error('Weekly Schedule Assignment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign weekly schedule: ' . $e->getMessage()
            ], 422);
        }
    }

    public function assignMonth(Request $request)
    {
        try {
            $request->validate([
                'nurse_id' => 'required|exists:users,id',
                'room' => 'required|exists:rooms,id',
                'shift' => 'required|in:morning,evening,night',
                'month' => 'required|date_format:Y-m',
                'rest_days' => 'required|array|min:1',
                'rest_days.*' => 'integer|between:0,6',
            ]);

            $startDate = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $restDays = $request->rest_days;

            $schedules = [];
            $skippedDates = [];

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                // Skip if it's a rest day
                if (in_array($date->dayOfWeek, $restDays)) {
                    continue;
                }

                // Check if schedule already exists
                $existingSchedule = NurseSchedule::where([
                    'date' => $date->format('Y-m-d'),
                    'nurse_id' => $request->nurse_id,
                    'shift' => $request->shift,
                ])->first();

                if ($existingSchedule) {
                    $skippedDates[] = $date->format('Y-m-d');
                    continue;
                }

                $schedule = NurseSchedule::create([
                    'nurse_id' => $request->nurse_id,
                    'room' => $request->room,
                    'shift' => $request->shift,
                    'date' => $date->format('Y-m-d'),
                    'status' => 'scheduled'
                ]);

                $schedules[] = $schedule;
            }

            $message = 'Monthly schedule assigned successfully';
            if (!empty($skippedDates)) {
                $message .= '. Skipped dates with existing assignments: ' . implode(', ', $skippedDates);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'schedules' => $schedules,
                'skipped_dates' => $skippedDates
            ]);

        } catch (\Exception $e) {
            \Log::error('Monthly Schedule Assignment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign monthly schedule: ' . $e->getMessage()
            ], 422);
        }
    }

    public function updateRoomId()
    {
        DB::table('tasks as t')
            ->join('beds as b', 't.patient_id', '=', 'b.patient_id')
            //->whereNotNull('b.room_id')
            ->update(['t.room_id' => DB::raw('b.room_id')]);

        return response()->json([
            'success' => true,
            'message' => 'Room IDs updated successfully'
        ]);
    }

    public function edit(Task $task)
    {
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'required|date',
        ]);

        try {
            $task->update($validatedData);
            return response()->json(['success' => true, 'message' => 'Task updated successfully']);
        } catch (\Exception $e) {
            \Log::error('Error updating task:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update task'], 500);
        }
    }
}
