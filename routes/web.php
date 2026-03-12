<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\OfficeLocationController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\AttendanceLogController;
use App\Http\Controllers\Admin\AccountController;
// Modul Penilaian (Assessment)
use App\Http\Controllers\Admin\AssessmentCategoryController;
use App\Http\Controllers\Admin\AssessmentController;

// ============================
// EXISTING ROUTES
// ============================
Route::get('/', [AttendanceController::class, 'generateToken']);

// ============================
// ADMIN ROUTES
// ============================
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Karyawan
    Route::get('/employees/export-excel', [EmployeeController::class, 'exportExcel'])->name('employees.export-excel');
    Route::post('/employees/{employee}/reset-device', [EmployeeController::class, 'resetDevice'])->name('employees.reset-device');
    Route::resource('employees', EmployeeController::class);

    // Department
    Route::resource('departments', DepartmentController::class);

    // Lokasi Kantor
    Route::resource('office-locations', OfficeLocationController::class);

    // Shift
    Route::resource('shifts', ShiftController::class);

    // Laporan Absensi
    Route::get('/attendance-report', [AttendanceReportController::class, 'index'])->name('attendance-report.index');
    Route::get('/attendance-report/export-excel', [AttendanceReportController::class, 'exportExcel'])->name('attendance-report.export-excel');
    Route::get('/attendance-report/export-pdf', [AttendanceReportController::class, 'exportPdf'])->name('attendance-report.export-pdf');

    // Manajemen Cuti
    Route::patch('/leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::patch('/leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::resource('leave-requests', LeaveRequestController::class)->only(['index', 'show']);

    // Hari Libur
    Route::resource('holidays', HolidayController::class);

    // Log Absensi
    Route::get('/attendance-logs', [AttendanceLogController::class, 'index'])->name('attendance-logs.index');

    // Manajemen Akun Karyawan
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::post('/', [AccountController::class, 'store'])->name('store');
        Route::post('/{user}/reset-password', [AccountController::class, 'resetPassword'])->name('reset-password');
        Route::patch('/{user}/toggle-active', [AccountController::class, 'toggleActive'])->name('toggle-active');
    });

    // Modul Penilaian (Assessment) – simplified schema
    Route::resource('assessment-categories', AssessmentCategoryController::class)->except(['show']);
    Route::patch('assessment-categories/{assessmentCategory}/toggle-active', [AssessmentCategoryController::class, 'toggleActive'])->name('assessment-categories.toggle-active');
    Route::get('assessments/report', [AssessmentController::class, 'report'])->name('assessments.report');
    Route::resource('assessments', AssessmentController::class);
});

// ============================
// KARYAWAN ROUTES (placeholder – add controllers as needed)
// ============================