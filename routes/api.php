<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AnttendaceController;
use App\Http\Controllers\Api\AttendanceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Jalur untuk menerima hasil scan dari React
Route::post('/attendance/scan', [AttendanceController::class, 'scan']);