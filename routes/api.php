<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AttendanceController; // Gunakan 'Api' bukan 'API'

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Jalur untuk menerima hasil scan dari React
Route::get('/get-new-token', [AttendanceController::class, 'generateToken']);
Route::post('/attendance/scan', [AttendanceController::class, 'scan']);