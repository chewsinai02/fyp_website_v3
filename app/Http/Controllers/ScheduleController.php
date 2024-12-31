<?php

namespace App\Http\Controllers;

use App\Models\NurseSchedule;
use Illuminate\Http\Request;

class ScheduleController
{
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'schedule_id' => 'required|exists:nurse_schedules,id',
                'nurse_id' => 'required|exists:users,id',
                'date' => 'required|date',
                'shift' => 'required|in:morning,afternoon,night',
                'room_id' => 'required|exists:rooms,id'
            ]);

            $schedule = NurseSchedule::findOrFail($validated['schedule_id']);
            
            // Check for conflicts
            $conflict = NurseSchedule::where('id', '!=', $schedule->id)
                ->where('nurse_id', $validated['nurse_id'])
                ->where('date', $validated['date'])
                ->where('shift', $validated['shift'])
                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule conflict detected'
                ], 422);
            }

            $schedule->update([
                'nurse_id' => $validated['nurse_id'],
                'date' => $validated['date'],
                'shift' => $validated['shift'],
                'room_id' => $validated['room_id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Schedule update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule: ' . $e->getMessage()
            ], 500);
        }
    }
} 