<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function generateOfficeQR()
    {
        $secret = config('app.qr_secret_key');

        $window = 30; // HARUS sama dengan validator
        $timestamp = floor(time() / $window);

        $qrToken = hash_hmac('sha256', $timestamp, $secret);

        return view('attendance.monitor', compact('qrToken'));
    }
}