<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NurseSchedule;
use App\Models\User;
use App\Models\Room;
use Carbon\Carbon;

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
        
        $initialSchedules = NurseSchedule::with(['nurse', 'room'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $nurses = User::where('role', 'nurse')->get();
        $rooms = Room::all();

        return view('nurseAdmin.calendar', compact(
            'nurses', 
            'rooms', 
            'initialSchedules', 
            'today',
            'date'
        ));
    }

    public function action(Request $request)
    {
        if($request->ajax()) {
            if($request->type == 'add') {
                $schedule = NurseSchedule::create([
                    'nurse_id' => $request->nurse_id,
                    'room_id' => $request->room_id,
                    'date' => $request->date,
                    'shift' => $request->shift,
                    'status' => 'scheduled',
                    'notes' => $request->notes
                ]);

                return response()->json($schedule);
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
}