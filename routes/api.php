<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Http\Controllers\Api\AuthController;

// ========== AUTH ROUTES (public) ==========
Route::post('/auth/login', [AuthController::class, 'login']);

// ========== AUTH ROUTES (protected) ==========
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    // Employee endpoints (protected — ownership checked in controller)
    Route::get('/employees', [EmployeeApiController::class, 'index']);
    Route::get('/employees/{id}', [EmployeeApiController::class, 'show']);
    Route::get('/employees/{id}/attendance', [EmployeeApiController::class, 'attendanceHistory']);
    Route::get('/employees/{id}/attendance/today', [EmployeeApiController::class, 'attendanceToday']);
    Route::get('/employees/{id}/leave-requests', [EmployeeApiController::class, 'leaveRequests']);
    Route::post('/leave-requests', [EmployeeApiController::class, 'storeLeaveRequest']);

    // Assessment API – karyawan lihat penilaian diri sendiri
    Route::get('/employees/{id}/assessments', [\App\Http\Controllers\Api\AssessmentApiController::class, 'index']);
    Route::get('/assessments/{assessment}', [\App\Http\Controllers\Api\AssessmentApiController::class, 'show']);
});

// ========== PUBLIC ROUTES (QR system) ==========
Route::get('/get-new-token', [AttendanceController::class, 'generateToken']);
Route::post('/attendance/scan', [AttendanceController::class, 'scan']);