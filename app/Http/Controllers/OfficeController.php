<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function generateOfficeQR()
{
    // Rahasia kantor (bisa ditaruh di .env)
    $secret = config('app.qr_secret_key'); 
    
    // Membuat jendela waktu 5 detik
    $timestamp = floor(time() / 5); 
    
    // Hash token unik untuk jendela waktu ini
    $qrToken = hash_hmac('sha256', $timestamp, $secret);

    return view('attendance.monitor', compact('qrToken'));
}
}
