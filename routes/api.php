<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomManagementController;
use App\Http\Controllers\BedController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'role:nurseAdmin,nurse,nurse_admin'])->group(function () {
    Route::get('/patients/unassigned', [RoomManagementController::class, 'getUnassignedPatients']);
    Route::get('/patients/{id}', [RoomManagementController::class, 'getPatientDetails']);
    Route::get('/rooms/available', [RoomManagementController::class, 'getAvailableRooms']);
    Route::get('/rooms/{roomId}/available-beds', [RoomManagementController::class, 'getAvailableBeds']);
    Route::post('/beds/{bedId}/discharge', [RoomManagementController::class, 'dischargeBed']);
    Route::get('/patients/search-unassigned', [RoomManagementController::class, 'searchUnassignedPatients']);
    Route::get('/patients/all', [RoomManagementController::class, 'getAllPatients']);
    Route::get('/api/check-patient-assignment/{patientId}', [RoomManagementController::class, 'checkPatientAssignment']);
    Route::match(['get', 'post'], '/beds/manage', [RoomManagementController::class, 'manageBed'])->name('manageBed');

    Route::get('/nurse-admin/edit-beds', [RoomManagementController::class, 'editBedsPage'])->name('editBeds');
    Route::get('/nurseadmin/beds', [RoomManagementController::class, 'index'])->name('nurseadmin.beds');

    // Route to fetch beds for a specific room
    Route::get('/rooms/{roomId}/beds', [RoomManagementController::class, 'getBedsForRoom'])
        ->name('api.rooms.beds');

    // Route to change the status of a bed
    Route::post('/beds/change-status', [RoomManagementController::class, 'changeStatus'])->name('changeStatus');
    Route::post('/patients/transfer', [RoomManagementController::class, 'transferPatient']);

    Route::get('/get-room-nurse/{room_number}', function ($roomNumber) {
        $nurseSchedule = \App\Models\NurseSchedule::where('room_id', $roomNumber)
            ->whereDate('date', today())
            ->first();
        
        return response()->json([
            'nurse_id' => $nurseSchedule ? $nurseSchedule->nurse_id : null
        ]);
    });
});

Route::get('/check-role', function () {
    return response()->json([
        'user_id' => auth()->id(),
        'role' => auth()->user()->role,
        'is_authenticated' => auth()->check()
    ]);
});

Route::get('/patients/{id}', function ($id) {
    $patient = DB::table('users')
        ->select('name')
        ->where('id', $id)
        ->first();
    
    return response()->json($patient);
});

Route::get('/beds/patient/{patientId}', function ($patientId) {
    $bed = DB::table('beds')
        ->select('bed_number')
        ->where('patient_id', $patientId)
        ->first();
    
    return response()->json($bed);
});