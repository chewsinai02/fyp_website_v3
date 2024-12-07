<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomManagementController;
use App\Http\Controllers\BedController;

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

Route::middleware(['auth', 'role:nurse'])->group(function () {
    Route::get('/patients/{id}', [RoomManagementController::class, 'getPatientDetails']);
    Route::get('/patients/unassigned', [RoomManagementController::class, 'getUnassignedPatients']);
    Route::get('/rooms/available', [RoomManagementController::class, 'getAvailableRooms']);
    Route::get('/rooms/{roomId}/available-beds', [RoomManagementController::class, 'getAvailableBeds']);
    Route::post('/beds/{bedId}/discharge', [RoomManagementController::class, 'dischargeBed']);
    Route::get('/patients/search-unassigned', [RoomManagementController::class, 'searchUnassignedPatients']);
    Route::get('/patients/all', [RoomManagementController::class, 'getAllPatients']);
    Route::get('/api/check-patient-assignment/{patientId}', [RoomManagementController::class, 'checkPatientAssignment']);
    Route::match(['get', 'post'], '/api/beds/manage', [RoomManagementController::class, 'manageBed'])->name('api.manageBed');

    Route::get('/nurse-admin/edit-beds', [RoomManagementController::class, 'editBedsPage'])->name('editBeds');
    Route::get('/nurseadmin/beds', [RoomManagementController::class, 'index'])->name('nurseadmin.beds');

    // Route to fetch beds for a specific room
    Route::get('/nurseadmin/rooms/{roomId}/beds', [RoomManagementController::class, 'getBedsForRoom']);

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