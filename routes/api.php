<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomManagementController;

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

Route::get('/patients/{id}', [RoomManagementController::class, 'getPatientDetails']);
Route::get('/patients/unassigned', [RoomManagementController::class, 'getUnassignedPatients']);
Route::get('/rooms/available', [RoomManagementController::class, 'getAvailableRooms']);
Route::get('/rooms/{roomId}/available-beds', [RoomManagementController::class, 'getAvailableBeds']);
Route::post('/beds/manage', [RoomManagementController::class, 'manageBed']);
Route::post('/beds/{bedId}/discharge', [RoomManagementController::class, 'dischargeBed']);
Route::get('/patients/search-unassigned', [RoomManagementController::class, 'searchUnassignedPatients']);
Route::get('/patients/all', [RoomManagementController::class, 'getAllPatients']);