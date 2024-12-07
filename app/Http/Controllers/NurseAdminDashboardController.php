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
use Barryvdh\DomPDF\Facade\Pdf;

class NurseAdminDashboardController extends Controller
{
    public function __construct()
    {
        DB::enableQueryLog();
    }

    public function nurseAdminindex()
    {
        $this->updateScheduleStatuses();

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
        $this->updateScheduleStatuses();

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
        try {
            DB::beginTransaction();

            // Validate request
            $validatedData = $request->validate([
                'nurse_id' => 'required|exists:users,id',
                'room_id' => 'required|exists:rooms,id',
                'date' => 'required|date|after_or_equal:today',
                'shift' => 'required|in:morning,afternoon,night',
                'notes' => 'nullable|string|max:255'
            ]);

            // Get nurse and room details for the response
            $nurse = User::find($validatedData['nurse_id']);
            $room = Room::find($validatedData['room_id']);

            // Check if nurse already has ANY shift for this date
            $nurseConflict = NurseSchedule::where('nurse_id', $validatedData['nurse_id'])
                ->whereDate('date', $validatedData['date'])
                ->exists();

            if ($nurseConflict) {
                DB::rollback();
                \Log::info('Nurse conflict detected', [
                    'nurse_id' => $validatedData['nurse_id'], 
                    'date' => $validatedData['date']
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'title' => 'Schedule Conflict',
                    'message' => "Nurse {$nurse->name} is already assigned to a shift on " . 
                                Carbon::parse($validatedData['date'])->format('M d, Y') . ".",
                    'icon' => 'error'
                ], 422);
            }

            // Create schedule
            $schedule = NurseSchedule::create($validatedData);
            
            DB::commit();

            // Format the date for display
            $formattedDate = Carbon::parse($validatedData['date'])->format('M d, Y');
            $formattedShift = ucfirst($validatedData['shift']);

            // Return success response with formatted data for SweetAlert2
            return response()->json([
                'status' => 'success',
                'title' => 'Schedule Created!',
                'message' => 'Schedule has been created successfully',
                'icon' => 'success',
                'details' => [
                    'nurse' => $nurse->name,
                    'date' => $formattedDate,
                    'shift' => $formattedShift,
                    'room' => "Room {$room->room_number}"
                ],
                'data' => [
                    'schedule' => $schedule->load('nurse', 'room'),
                    'redirect' => route('nurseadmin.scheduleList')
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Schedule creation failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'title' => 'Error!',
                'message' => 'Failed to create schedule. Please try again.',
                'icon' => 'error',
                'technical_error' => $e->getMessage()  // For debugging
            ], 500);
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
            ->orderBy('shift', 'asc')
            ->join('users', 'nurse_schedules.nurse_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('nurse_schedules.*')
            ->get();

        return view('nurseAdmin.scheduleReport', compact('schedules'));
    }

    public function exportScheduleReport(Request $request)
    {
        // Get schedules with consistent ordering
        $schedules = NurseSchedule::with(['nurse', 'room'])
            ->whereDate('date', '>=', Carbon::now()->startOfMonth())
            ->orderBy('date')
            ->orderBy('shift', 'asc')
            ->join('users', 'nurse_schedules.nurse_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('nurse_schedules.*')
            ->get();

        // Debug the data
        \Log::info('Total schedules:', ['count' => $schedules->count()]);

        // Group schedules by date
        $groupedSchedules = $schedules->groupBy(function ($schedule) {
            return $schedule->date->format('Y-m-d');
        });

        // Debug the grouped data
        \Log::info('Grouped schedules:', [
            'dates' => $groupedSchedules->keys()->toArray(),
            'counts' => $groupedSchedules->map->count()->toArray()
        ]);

        // Generate PDF
        $pdf = PDF::loadView('nurseAdmin.schedule-report', [
            'groupedSchedules' => $groupedSchedules,
            'generatedDate' => Carbon::now()->format('M d, Y h:i A')
        ]);

        $filename = 'schedule_report_' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
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

    private function updateScheduleStatuses()
    {
        $now = Carbon::now();
        
        try {
            // Update all past schedules
            NurseSchedule::where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    // Past dates
                    $q->whereDate('date', '<', $now->toDateString());
                })->orWhere(function ($q) use ($now) {
                    // Current date but past shifts
                    $q->whereDate('date', $now->toDateString())
                        ->where(function ($sq) use ($now) {
                            $sq->where(function ($morning) use ($now) {
                                $morning->where('shift', 'morning')
                                    ->where('date', '<=', $now->copy()->setTime(15, 0, 0));
                            })->orWhere(function ($afternoon) use ($now) {
                                $afternoon->where('shift', 'afternoon')
                                        ->where('date', '<=', $now->copy()->setTime(23, 0, 0));
                            })->orWhere(function ($night) use ($now) {
                                $night->where('shift', 'night')
                                    ->where('date', '<=', $now->copy()->subDay()->setTime(7, 0, 0));
                            });
                        });
                });
            })
            ->where('status', 'scheduled')
            ->update(['status' => 'completed']);

            \Log::info('Schedule statuses updated successfully', [
                'current_time' => $now->toDateTimeString(),
                'updated_count' => DB::connection()->getQueryLog()[0]['rows'] ?? 0
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update schedule statuses: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function nurseAdminManageProfile()
    {
        $user = Auth::user();
        return view('nurseadmin.manageProfile', compact('user'));
    }

    public function nurseAdminChangePassword()
    {
        $user = Auth::user();
        return view('nurseadmin.changePassword', compact('user'));
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
            return redirect()->route('nurseadmin.changePassword')->with('success', 'Password changed successfully! Please log in again.');
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

    protected function formatJsonResponse($data, $view = true)
    {
        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return view('nurseAdmin.json-response', [
            'success' => $data['success'] ?? false,
            'message' => $data['message'] ?? '',
            'data' => $data,
            'title' => 'API Response'
        ]);
    }

    public function assignmentReport()
    {
        $schedules = NurseSchedule::with(['nurse', 'room'])
            ->whereDate('date', '>=', Carbon::now()->startOfMonth())
            ->orderBy('date')
            ->orderBy('shift', 'asc')
            ->join('users', 'nurse_schedules.nurse_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('nurse_schedules.*')
            ->get();

        // Group schedules by nurse
        $groupedSchedules = $schedules->groupBy('nurse_id');

        return view('nurseAdmin.assignmentReport', [
            'groupedSchedules' => $groupedSchedules,
            'currentDate' => Carbon::now()->format('M d, Y')
        ]);
    }

    public function exportAssignmentReport()
    {
        $schedules = NurseSchedule::with(['nurse', 'room'])
            ->whereDate('date', '>=', Carbon::now()->startOfMonth())
            ->orderBy('date')
            ->orderBy('shift', 'asc')
            ->join('users', 'nurse_schedules.nurse_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('nurse_schedules.*')
            ->get();

        // Group schedules by nurse
        $groupedSchedules = $schedules->groupBy('nurse_id');

        // Generate PDF
        $pdf = PDF::loadView('nurseAdmin.assignment-report', [
            'groupedSchedules' => $groupedSchedules,
            'generatedDate' => Carbon::now()->format('M d, Y h:i A')
        ]);

        $filename = 'assignment_report_' . Carbon::now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function calendar()
    {
        $schedules = NurseSchedule::with(['nurse', 'room'])
            ->whereDate('date', '>=', now()->startOfMonth())
            ->whereDate('date', '<=', now()->endOfMonth())
            ->get();

        $events = $schedules->map(function($schedule) {
            $colors = $this->getShiftColor($schedule->shift);
            return [
                'title' => $schedule->nurse?->name . ' - ' . ucfirst($schedule->shift),
                'start' => $schedule->date,
                'backgroundColor' => $colors['bg'],
                'borderColor' => $colors['border'],
                'textColor' => $colors['text'],
                'extendedProps' => [
                    'nurse' => $schedule->nurse?->name ?? 'Unassigned',
                    'room' => $schedule->room ? "Room {$schedule->room->room_number}" : 'Unassigned',
                    'shift' => $schedule->shift,
                    'status' => $schedule->status
                ]
            ];
        });

        // Get nurses and rooms for the modals
        $nurses = User::where('role', 'nurse')->orderBy('name')->get();
        $rooms = Room::orderBy('room_number')->get();
        $date = now();

        return view('nurseAdmin.calendar', compact('events', 'nurses', 'rooms', 'date'));
    }
    
    private function getShiftColor($shift)
    {
        return match($shift) {
            'morning' => [
                'bg' => '#E3F2FD',    // Light blue background
                'border' => '#2196F3', // Blue border
                'text' => '#1565C0'    // Dark blue text
            ],
            'afternoon' => [
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
}