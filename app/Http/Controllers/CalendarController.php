<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NurseSchedule;
use App\Models\User;
use App\Models\Room;
use Carbon\Carbon;
use App\Models\NurseSchedule as Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::now();
        
        // Get month and year from request or use current date
        $month = $request->get('month', $today->month);
        $year = $request->get('year', $today->year);
        
        // Create date object for the requested month
        $date = Carbon::createFromDate($year, $month, 1);
        
        // Get schedules for the selected month
        $initialSchedules = NurseSchedule::with(['nurse', 'room'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $nurses = User::where('role', 'nurse')->get();
        $rooms = Room::all();
        
        // Create an empty schedule object for the view
        $schedule = new NurseSchedule();

        return view('nurseAdmin.calendar', compact(
            'nurses', 
            'rooms', 
            'initialSchedules', 
            'today',
            'date',
            'schedule'
        ));
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

    public function getSchedule($id)
    {
        return NurseSchedule::with(['nurse', 'room'])
            ->findOrFail($id);
    }

    public function assignWeek(Request $request)
    {
        try {
            $request->validate([
                'nurse_id' => 'required|exists:users,id',
                'room_id' => 'required|exists:rooms,id',
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
                    'room_id' => $request->room_id,
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
                'room_id' => 'required|exists:rooms,id',
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
                    'room_id' => $request->room_id,
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

    public function destroy($id)
    {
        try {
            Log::info('Attempting to delete schedule:', ['id' => $id]);
            
            $schedule = NurseSchedule::findOrFail($id);
            $schedule->delete();
            
            Log::info('Schedule deleted successfully:', ['id' => $id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Schedule Delete Error:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete schedule: ' . $e->getMessage()
            ], 422);
        }
    }
}
