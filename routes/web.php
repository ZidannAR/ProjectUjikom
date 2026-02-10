<?php

use App\Http\Controllers\Api\AttendanceController;

use Illuminate\Support\Facades\Route;

// routes/web.php
use App\Http\Controllers\OfficeController;

// Jalur untuk buka halaman monitor di browser
Route::get('/', [OfficeController::class, 'generateOfficeQR']);

// Jalur API untuk ambil token baru tiap 5 detik
Route::get('/api/get-new-token', function() {
    // Hapus agar tidak error
    $timestamp = floor(time() / 5); 
    $token = hash_hmac('sha256', $timestamp, config('app.qr_secret_key'));
    
    return response()->json(['token' => $token]);
});