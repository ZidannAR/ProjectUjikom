<?php

use App\Http\Controllers\AttendanceController;

use Illuminate\Support\Facades\Route;

// routes/web.php
use App\Http\Controllers\OfficeController;

// Jalur untuk buka halaman monitor di browser
Route::get('/', [AttendanceController::class, 'generateToken']);

// Jalur API untuk ambil token baru tiap 5 detik
// Route::get('/api/get-new-token', function() {
//     // Hapus agar tidak error
//     $timestamp = floor(time() / 10000); 
//     $token = hash_hmac('sha256', $timestamp, config('app.qr_secret_key'));
    
//     return response()->json(['token' => $token]);
// });