<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/doctors/{doctor}/services', [DoctorController::class, 'getServices']);

Route::get('/patients/search', [PatientController::class, 'search']);
Route::get('/api/patients/{patId}', [PatientController::class, 'getPatientInfo']);

Route::get('/appointment/calendar', [AppointmentController::class, 'calendar']);

Route::get('/get-data', [DoctorController::class, 'getDoctorServices']);

Route::get('/inventory/{startDate}/{endDate}', [InventoryController::class, 'getInventoryByDateRange']);
