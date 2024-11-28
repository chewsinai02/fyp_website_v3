<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class PatientTaskController extends Controller
{
    public function index(User $patient)
    {
        return view('nurse.patientTasks', compact('patient'));
    }

    public function getEvents(User $patient)
    {
        $tasks = Task::where('patient_id', $patient->id)
            ->get()
            ->map(function($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->due_date,
                    'backgroundColor' => $this->getPriorityColor($task->priority),
                    'borderColor' => $this->getPriorityColor($task->priority),
                    'textColor' => '#fff'
                ];
            });

        return response()->json($tasks);
    }

    public function getTasks(User $patient, $date)
    {
        $tasks = Task::where('patient_id', $patient->id)
            ->whereDate('due_date', $date)
            ->orderBy('due_date')
            ->get();

        return response()->json($tasks);
    }

    public function store(Request $request, User $patient)
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

    public function update(Request $request, Task $task)
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

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed'
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => true]);
    }

    private function getPriorityColor($priority)
    {
        return [
            'low' => '#0dcaf0',
            'medium' => '#ffc107',
            'high' => '#dc3545',
            'urgent' => '#212529'
        ][$priority] ?? '#6c757d';
    }
} 