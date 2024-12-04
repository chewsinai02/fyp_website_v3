<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\NurseDashboardController;
use App\Http\Controllers\UserDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminAdminController;
use App\Http\Controllers\AdminDoctorController;
use App\Http\Controllers\AdminNurseAdminController;
use App\Http\Controllers\AdminNurseController;
use App\Http\Controllers\AdminPatientController;
use App\Http\Controllers\DoctorReportController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NurseAdminDashboardController;
use App\Http\Controllers\RoomManagementController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\NurseCallController;
use App\Http\Controllers\PatientTaskController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\NurseCalendarController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route for the admin dashboard
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'adminindex'])->name('adminDashboard');
    Route::put('/details/{id}', [UserController::class, 'adminupdate'])->name('details.update');
    Route::get('/details/{id}', [UserDataController::class, 'adminshow'])->name('details.show');
    Route::get('/details/{id}/edit', [UserController::class, 'adminedit'])->name('details.edit');
    Route::put('/admin/users/{id}', [UserController::class, 'adminupdate'])->name('details.update');
    Route::delete('/admin/users/{id}', [UserController::class, 'admindestroy'])->name('users.destroy'); // Delete route
    Route::get('/details/{id}/detailshow', [AdminDashboardController::class, 'admindetailshow'])->name('admindetailshow');
    // Route to show the form for entering additional user details
    Route::middleware(['auth', 'can:isAdmin'])->group(function () {
        // Admin user data routes
        Route::get('/admin/userdata/admin/{id}', [AdminAdminController::class, 'adminshowadmin'])->name('admin.adminUserdata.show');
        Route::post('/admin/userdata/admin/{id}', [AdminAdminController::class, 'adminstoreadmin'])->name('admin.adminUserdata.store');

        // Doctor user data routes
        Route::get('/admin/userdata/doctor/{id}', [AdminDoctorController::class, 'adminshowdoctor'])->name('admin.doctorUserdata.show');
        Route::post('/admin/userdata/doctor/{id}', [AdminDoctorController::class, 'adminstoredoctor'])->name('admin.doctorUserdata.store');
    
        // Nurse Admin user data routes
        Route::get('/admin/userdata/nurseAdmin/{id}', [AdminNurseAdminController::class, 'adminshownurseadmin'])->name('admin.nurseadminUserdata.show');
        Route::post('/admin/userdata/nurseAdmin/{id}', [AdminNurseAdminController::class, 'adminstorenurseadmin'])->name('admin.nurseadminUserdata.store');
    
        // Nurse user data routes
        Route::get('/admin/userdata/nurse/{id}', [AdminNurseController::class, 'adminshownurse'])->name('admin.nurseUserdata.show');
        Route::post('/admin/userdata/nurse/{id}', [AdminNurseController::class, 'adminstorenurse'])->name('admin.nurseUserdata.store');
    
        // Patient user data routes
        Route::get('/admin/userdata/patient/{id}', [AdminPatientController::class, 'adminshowpatient'])->name('admin.patientUserdata.show');
        Route::post('/admin/userdata/patient/{id}', [AdminPatientController::class, 'adminstorepatient'])->name('admin.patientUserdata.store');    
    });

    //manage profile
    Route::get('/admin/adminManageProfile', [AdminDashboardController::class, 'adminManageProfile'])->name('admin.manageProfile');
    Route::get('/admin/adminChangePassword', [AdminDashboardController::class, 'adminChangePassword'])->name('adminChangePassword');
    Route::post('/admin/adminChangePassword', [AdminDashboardController::class, 'adminCheckCurrentPassword'])->name('adminCheckCurrentPassword');
    Route::get('/admin/adminEditProfile', [AdminDashboardController::class, 'adminEditProfile'])->name('adminEditProfile');
    Route::post('/admin/adminUpdateProfile', [AdminDashboardController::class, 'adminUpdateProfilePicture'])->name('adminUpdateProfilePicture');
    
    //search in my users list
    Route::get('/admin/dashboard/search', [AdminDashboardController::class, 'searchUser'])->name('searchUser');

    //admin
    Route::get('/admin/adminList', [AdminAdminController::class, 'adminList'])->name('adminList');
    Route::get('/admin/adminList', [AdminAdminController::class, 'adminListIndex'])->name('adminList');
    //search in admin list
    Route::get('/admin/adminList/searchAdmin', [AdminAdminController::class, 'searchAdmin'])->name('searchAdmin');
    // Add new admin
    Route::get('/admin/addAdmin', [AdminAdminController::class, 'showAddAdminForm'])->name('addAdminForm');
    Route::post('/admin/addAdmin', [AdminAdminController::class, 'addNewAdmin'])->name('addNewAdmin');

    //doctor
    Route::get('/admin/doctorList', [AdminDoctorController::class, 'doctorList'])->name('doctorList');
    Route::get('/admin/doctorList', [AdminDoctorController::class, 'doctorListIndex'])->name('doctorList');
    //search in admin list
    Route::get('/admin/doctorList/searchDoctor', [AdminDoctorController::class, 'searchDoctor'])->name('searchDoctor');
    // Add new doctor
    Route::get('/admin/addDoctor', [AdminDoctorController::class, 'showAddDoctorForm'])->name('addDoctorForm');
    Route::post('/admin/addDoctor', [AdminDoctorController::class, 'addNewDoctor'])->name('addNewDoctor');

    //nurse admin
    Route::get('/admin/nurseadminList', [AdminNurseAdminController::class, 'nurseAdminList'])->name('nurseAdminList');
    Route::get('/admin/nurseadminList', [AdminNurseAdminController::class, 'nurseAdminListIndex'])->name('nurseAdminList');
    //search in nurse admin list
    Route::get('/admin/nurseadminList/searchNurseAdmin', [AdminNurseAdminController::class, 'searchNurseAdmin'])->name('searchNurseAdmin');
    // Add new nurse admin
    Route::get('/admin/addNurseAdmin', [AdminNurseAdminController::class, 'showAddNurseAdminForm'])->name('addNurseAdminForm');
    Route::post('/admin/addNurseAdmin', [AdminNurseAdminController::class, 'addNewNurseAdmin'])->name('addNewNurseAdmin');

    //nurse
    Route::get('/admin/nurseList', [AdminNurseController::class, 'nurseList'])->name('nurseList');
    Route::get('/admin/nurseList', [AdminNurseController::class, 'nurseListIndex'])->name('nurseList');
    //search in nurse  list
    Route::get('/admin/nurseList/searchNurse', [AdminNurseController::class, 'searchNurse'])->name('searchNurse');
    // Add new nurse
    Route::get('/admin/addNurse', [AdminNurseController::class, 'showAddNurseForm'])->name('addNurseForm');
    Route::post('/admin/addNurse', [AdminNurseController::class, 'addNewNurse'])->name('addNewNurse');

    // Patient list
    Route::get('/admin/patientList', [AdminPatientController::class, 'patientListIndex'])->name('patientList');
    // Search in patient list
    Route::get('/admin/patientList/searchPatient', [AdminPatientController::class, 'searchPatient'])->name('searchPatient');
    // Add new patient
    Route::get('/admin/addPatient', [AdminPatientController::class, 'showAddPatientForm'])->name('addPatientForm');
    Route::post('/admin/addPatient', [AdminPatientController::class, 'addNewPatient'])->name('addNewPatient');
});


// Route for the doctor dashboard
Route::middleware(['auth', 'doctor'])->group(function () {
    Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'doctorindex'])->name('doctorDashboard');
    Route::get('/doctor/dashboard/searchActiveAppointments', [DoctorDashboardController::class, 'searchActiveAppointments'])->name('searchActiveAppointments');

    //appoinment
    Route::get('/doctor/appointments', [DoctorDashboardController::class, 'doctorAppointmentIndex'])->name('doctorAppointment.index');
    Route::get('/doctor/appointments/{appointment}', [DoctorDashboardController::class, 'doctorAppointmentShow'])->name('doctorAppointment.show');
    Route::get('/doctor/appointments/{appointment}/edit', [DoctorDashboardController::class, 'doctorAppointmentEdit'])->name('doctorAppointment.edit');
    Route::put('/doctor/appointments/{appointment}', [DoctorDashboardController::class, 'doctorAppointmentUpdate'])->name('doctorAppointment.update');
    Route::delete('/doctor/appointments/{appointment}', [DoctorDashboardController::class, 'doctorAppointmentDestroy'])->name('doctorAppointment.destroy');     
    Route::get('/doctor/searchAppointments', [DoctorDashboardController::class, 'searchAppointments'])->name('searchAppointments');

    //manage profile
    Route::get('/doctor/doctorManageProfile', [DoctorDashboardController::class, 'doctorManageProfile'])->name('doctor.manageProfile');
    Route::get('/doctor/doctorChangePassword', [DoctorDashboardController::class, 'doctorChangePassword'])->name('doctorChangePassword');
    Route::post('/doctor/doctorChangePassword', [DoctorDashboardController::class, 'doctorCheckCurrentPassword'])->name('doctorCheckCurrentPassword');
    Route::get('/doctor/doctorEditProfile', [DoctorDashboardController::class, 'doctorEditProfile'])->name('doctorEditProfile');
    Route::post('/doctor/doctorUpdateProfile', [DoctorDashboardController::class, 'doctorUpdateProfilePicture'])->name('doctorUpdateProfilePicture');

    Route::get('/doctor/patient/{patientId}/add-report', [DoctorReportController::class, 'create'])->name('doctor.addReport');
    Route::post('/doctor/reports', [DoctorReportController::class, 'store'])->name('doctor.storeReport');
    Route::get('/doctor/patient/{patientId}/reports', [DoctorReportController::class, 'index'])->name('doctor.reportList');
    Route::get('/doctor/reports/{report}', [DoctorReportController::class, 'show'])->name('doctor.viewReport');
    Route::get('/doctor/reports/{report}/edit', [DoctorReportController::class, 'editReport'])->name('doctor.editReport');
    Route::put('/doctor/reports/{report}', [DoctorReportController::class, 'updateReport'])->name('doctor.updateReport');
    Route::delete('/doctor/reports/{report}', [DoctorReportController::class, 'destroy'])->name('doctor.deleteReport');

    Route::get('/doctor/patient/{id}/edit', [DoctorDashboardController::class, 'editPatientDetails'])->name('doctor.editPatientDetails');
    Route::put('/doctor/patient/{id}', [DoctorDashboardController::class, 'doctorUpdatePatientDetails'])->name('doctor.updatePatientDetails');

    Route::get('/doctor/unread-messages', [MessageController::class, 'getUnreadMessages']);
    Route::post('/doctor/mark-messages-read', [MessageController::class, 'markAsRead']);

    Route::get('/doctor/report/{id}/print', [DoctorReportController::class, 'printReport'])
        ->name('doctor.printReport');

    Route::get('/doctor/unread-messages-count', [DoctorDashboardController::class, 'getUnreadCount']);
});

Route::middleware(['auth', 'role:doctor,patient'])->group(function () {
    Route::get('/doctor/message', [DoctorDashboardController::class, 'doctorMessage'])->name('doctorMessage');
    Route::get('/doctor/messages/{patientId}', [DoctorDashboardController::class, 'doctorshow'])->name('doctor.chat');
    Route::delete('/doctor/messages/{id}', [DoctorDashboardController::class, 'doctorMessageDestroy'])->name('doctor.messages.destroy');

    Route::post('/doctor/messages/{receiverId}', [DoctorDashboardController::class, 'doctorstore'])->name('chat.store');
    Route::delete('/doctor/messages/{id}', [DoctorDashboardController::class, 'destroy'])->name('doctor.messages.destroy');
});

// Nurse Admin Routes
Route::middleware(['auth', 'role:nurse_admin'])->group(function () {
    // Dashboard
    Route::get('/nurseadmin/dashboard', [NurseAdminDashboardController::class, 'nurseadminindex'])
        ->name('nurseadminDashboard');


    Route::get('/nurseadmin/manageProfile', [NurseAdminDashboardController::class, 'nurseAdminManageProfile'])
        ->name('nurseadmin.manageProfile');
    Route::get('/nurseadmin/changePassword', [NurseAdminDashboardController::class, 'nurseAdminChangePassword'])
        ->name('nurseadmin.changePassword');
    Route::post('/nurseadmin/changePassword', [NurseAdminDashboardController::class, 'nurseAdminCheckCurrentPassword'])
        ->name('nurseadmin.checkCurrentPassword');
    Route::get('/nurseadmin/editProfile', [NurseAdminDashboardController::class, 'nurseAdminEditProfile'])
        ->name('nurseadmin.editProfile');
    Route::post('/nurseadmin/updateProfile', [NurseAdminDashboardController::class, 'nurseAdminUpdateProfilePicture'])
        ->name('nurseadmin.updateProfilePicture');
    
    // Nurse Management
    Route::get('/nurseadmin/nurses', [NurseAdminDashboardController::class, 'nurseList'])
        ->name('nurseadmin.nurseList');
    Route::get('/nurseadmin/nurses/{id}', [NurseAdminDashboardController::class, 'showNurse'])
        ->name('nurseadmin.showNurse');

    // Room Management
    Route::get('/nurseadmin/rooms', [RoomManagementController::class, 'index'])
        ->name('nurseadmin.roomList');
    Route::post('/nurseadmin/rooms', [RoomManagementController::class, 'store'])
        ->name('nurseadmin.rooms.store');
    Route::put('/nurseadmin/rooms/{room}', [RoomManagementController::class, 'update'])
        ->name('nurseadmin.rooms.update');
    Route::delete('/nurseadmin/rooms/{room}', [RoomManagementController::class, 'destroy'])
        ->name('nurseadmin.rooms.destroy');
    Route::get('/nurseadmin/rooms/{room}/edit', [RoomManagementController::class, 'edit'])
        ->name('rooms.edit');
    Route::post('/nurseadmin/rooms/{room}', [RoomManagementController::class, 'update'])
        ->name('rooms.update');
    
    // Room Assignment
    Route::post('/nurseadmin/assign-room', [NurseAdminDashboardController::class, 'assignRoom'])
        ->name('nurseadmin.assignRoom');
    Route::get('/nurseadmin/available-rooms', [NurseAdminDashboardController::class, 'getAvailableRooms'])
        ->name('nurseadmin.availableRooms');

    // Bed Management
    Route::post('/nurseadmin/rooms/{room}/beds', [RoomManagementController::class, 'addBed'])
        ->name('nurseadmin.rooms.addBed');
    Route::put('/nurseadmin/beds/{bed}', [RoomManagementController::class, 'updateBed'])
        ->name('nurseadmin.beds.update');
    Route::delete('/nurseadmin/beds/{bed}', [RoomManagementController::class, 'removeBed'])
        ->name('nurseadmin.beds.destroy');

    // Schedule Management
    Route::get('/nurseadmin/schedules', [NurseAdminDashboardController::class, 'scheduleList'])
        ->name('nurseadmin.scheduleList');
    Route::get('/nurseadmin/schedules/filter', [NurseAdminDashboardController::class, 'filterSchedules'])
        ->name('nurseadmin.filterSchedules');
    Route::post('/nurseadmin/schedules', [NurseAdminDashboardController::class, 'storeSchedule'])
        ->name('nurseadmin.addSchedule');
    Route::delete('/nurseadmin/schedules/{schedule}', [NurseAdminDashboardController::class, 'deleteSchedule'])
        ->name('nurseadmin.deleteSchedule');
    Route::delete('/nurseadmin/schedules/{id}', [NurseAdminDashboardController::class, 'destroySchedule'])
        ->name('nurseadmin.destroySchedule');
    Route::get('/schedules/{schedule}/edit', [NurseAdminDashboardController::class, 'editSchedule'])
        ->name('nurseadmin.editSchedule');
    Route::put('/schedules/{schedule}', [NurseAdminDashboardController::class, 'updateSchedule'])
        ->name('nurseadmin.updateSchedule');
    Route::get('/nurseadmin/schedules/{id}', [NurseAdminDashboardController::class, 'getSchedule'])
        ->name('nurseadmin.getSchedule');
    Route::get('/nurseadmin/schedules/filter', [NurseAdminDashboardController::class, 'filterSchedules'])
        ->name('nurseadmin.filterSchedules');
    Route::post('/schedules/statuses', [NurseAdminDashboardController::class, 'updateScheduleStatuses'])->name('schedules.statuses');

    // Reports
    Route::get('/nurseadmin/reports', [NurseAdminDashboardController::class, 'reports'])
        ->name('nurseadmin.reports');
    Route::get('/nurseadmin/reports/schedule', [NurseAdminDashboardController::class, 'scheduleReport'])
        ->name('nurseadmin.scheduleReport');
    Route::get('/export-report', [NurseAdminDashboardController::class, 'exportScheduleReport'])
        ->name('nurseadmin.exportScheduleReport');
    Route::get('/nurseadmin/reports/assignment', [NurseAdminDashboardController::class, 'assignmentReport'])
        ->name('nurseadmin.assignmentReport');
    Route::get('/nurseadmin/reports/export-assignment', [NurseAdminDashboardController::class, 'exportAssignmentReport'])
        ->name('nurseadmin.exportAssignmentReport');

    // Schedule Management with Room Assignment
    Route::get('/nurseadmin/nurses/{nurse}/current-assignment', [NurseAdminDashboardController::class, 'getCurrentAssignment'])
        ->name('nurseadmin.getCurrentAssignment');
    Route::get('/nurseadmin/available-rooms', [NurseAdminDashboardController::class, 'getAvailableRooms'])
        ->name('nurseadmin.availableRooms');

    Route::get('/nurseadmin/rooms/{room}/beds', [RoomManagementController::class, 'getBeds'])
        ->name('rooms.beds');

    Route::get('/nurseadmin/search-patients', [RoomManagementController::class, 'searchPatients'])
        ->name('nurseadmin.searchPatients');

    //calander
    Route::get('full-calendar', [CalendarController::class, 'index'])->name('full-calendar');
    Route::post('full-calendar/action', [CalendarController::class, 'action'])->name('full-calendar.action');
    Route::get('/calendar/schedule/{id}', [CalendarController::class, 'getSchedule'])->name('calendar.getSchedule');
    Route::post('nurseadmin/schedules/assign-week', [CalendarController::class, 'assignWeek'])->name('nurseadmin.schedules.assign-week');
    Route::post('/nurseadmin/schedules/assign-month', [CalendarController::class, 'assignMonth'])->name('nurseadmin.schedules.assign-month');
    Route::delete('/schedules/{id}', [CalendarController::class, 'destroy'])->name('schedules.destroy');
    Route::post('/schedules/action', [CalendarController::class, 'action'])->name('schedules.action');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('nurseadmin')->name('nurseadmin.')->group(function () {
        // Route for editing a room
        Route::get('/rooms/{id}/edit', [RoomManagementController::class, 'edit'])->name('rooms.edit');
        
        // Route for updating a room
        Route::post('/rooms/{id}', [RoomManagementController::class, 'update'])->name('rooms.update');
    });
});

//route access, create middleware, add kernel, web.php
Route::middleware(['auth', 'role:nurse'])->group(function () {
    // NurseDashboard routes
    Route::get('/nurse/dashboard', [NurseDashboardController::class, 'index'])->name('nurseDashboard');
    Route::get('/nurse/patient/{user}', [NurseDashboardController::class, 'show'])->name('nurse.patient.view');
    Route::post('/nurse/patient/{user}/vitals', [NurseDashboardController::class, 'storeVitals'])
        ->name('nurse.patient.vitals.store');
    Route::post('/nurse/patient/{user}/notes', [NurseDashboardController::class, 'storeNote'])
        ->name('nurse.patient.notes.store');

    // Task management
    Route::prefix('nurse/patient/{patient}/tasks')->group(function () {
        Route::get('/', [NurseDashboardController::class, 'patientTasks'])->name('nurse.patient.tasks');
        Route::get('/events', [NurseDashboardController::class, 'getTaskEvents']);
        Route::get('/{date}', [NurseDashboardController::class, 'getPatientTasks']);
        Route::post('/', [NurseDashboardController::class, 'storeTask']);
    });
    
    Route::prefix('nurse/tasks')->group(function () {
        Route::patch('/{task}', [NurseDashboardController::class, 'updateTask']);
        Route::patch('/{task}/status', [NurseDashboardController::class, 'updateTaskStatus']);
        Route::delete('/{task}', [NurseDashboardController::class, 'destroyTask']);
    });

    // Schedule and Patient List routes
    Route::get('/nurse/schedule', [NurseDashboardController::class, 'schedule'])
        ->name('nurse.schedule');
    Route::get('/nurse/patients', [NurseDashboardController::class, 'patients'])
        ->name('nurse.patients');

    Route::get('/nurse/tasks', [NurseDashboardController::class, 'tasks'])->name('nurse.tasks');
});

Route::get('/firebase/store', [FirebaseController::class, 'store']);
Route::get('/firebase/show', [FirebaseController::class, 'show']);
Route::get('/firebase/update', [FirebaseController::class, 'update']);
Route::get('/firebase/delete', [FirebaseController::class, 'delete']);
Route::get('/firebase/push', [FirebaseController::class, 'pushToList']);
Route::get('/firebase/query', [FirebaseController::class, 'query']);

Route::middleware(['auth'])->group(function () {
    Route::get('/nurse/calls', [NurseCallController::class, 'index'])->name('nurse.calls');
    Route::post('/nurse/calls/{callId}/update', [NurseCallController::class, 'updateCallStatus']);
});

Route::prefix('nurse/patient/{patientId}')->group(function () {
    Route::get('/tasks', [NurseCalendarController::class, 'index'])->name('nurse.patient.tasks');
    Route::post('/tasks', [NurseCalendarController::class, 'store'])->name('nurse.patient.tasks.store');
    Route::post('/tasks/details', [NurseCalendarController::class, 'getTaskDetails'])->name('nurse.patient.tasks.details');
    Route::post('/tasks/{taskId}/status', [NurseCalendarController::class, 'updateTaskStatus'])->name('nurse.patient.tasks.status');
    Route::delete('/tasks/{taskId}', [NurseCalendarController::class, 'destroy'])->name('nurse.patient.tasks.destroy');
});

