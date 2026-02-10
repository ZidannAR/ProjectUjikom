<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Attendance, AttandanceLog, Employee};
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function scan(Request $request)
    {
        $employee = Employee::with('department.officeLocation')->findOrFail($request->employee_id);
        $office = $employee->department->officeLocation;

        // 1. Validasi QR 5 Detik
        $timestamp = floor(time() / 5);
        $validToken = hash_hmac('sha256', $timestamp, config('app.qr_secret_key'));
        if ($request->qr_token !== $validToken) {
            return response()->json(['message' => 'QR Expired, silakan scan ulang.'], 403);
        }

        // 2. Anti-Replay (1 QR untuk 1 Orang)
        if (AttandanceLog::where('qr_token_hash', $request->qr_token)->exists()) {
            return response()->json(['message' => 'QR sudah digunakan orang lain.'], 403);
        }

        // 3. Device Binding
        if ($employee->registered_device_id && $employee->registered_device_id !== $request->device_id) {
            return response()->json(['message' => 'Gunakan HP yang terdaftar!'], 403);
        }

        // 4. Geofencing (GPS)
        $distance = $this->calculateDistance($request->lat, $request->lng, $office->latitude, $office->longitude);
        if ($distance > $office->radius_meter) {
            return response()->json(['message' => 'Anda di luar radius kantor (' . round($distance) . 'm)'], 403);
        }

        // 5. Logika Work Date (Handle Shift Malam)
        // Jika scan antara jam 00:00 - 05:00 pagi, anggap masih work_date hari kemarin
        $workDate = now()->hour < 5 ? now()->subDay()->toDateString() : now()->toDateString();

        return $this->processAttendance($employee, $workDate, $request);
    }

    private function processAttendance($emp, $date, $req)
    {
        $attendance = Attendance::firstOrNew(['employee_id' => $emp->id, 'work_date' => $date]);

        if (!$attendance->exists) {
            // CLOCK IN
            $attendance->fill([
                'clock_in' => now(),
                'status_in' => 'Present', // Tambahkan logic cek terlambat di sini
                'gps_latitude' => $req->lat,
                'gps_longitude' => $req->lng,
                'device_id_used' => $req->device_id
            ])->save();     
            
            // Catat log agar QR tidak bisa dipakai lagi
            AttandanceLog::create(['employee_id' => $emp->id, 'qr_token_hash' => $req->qr_token]);
            
            return response()->json(['message' => 'Berhasil Masuk!']);
        }

        // CLOCK OUT
        if (!$attendance->clock_out) {
            $attendance->update(['clock_out' => now(), 'status_out' => 'Finish']);
            return response()->json(['message' => 'Berhasil Pulang!']);
        }

        return response()->json(['message' => 'Anda sudah absen hari ini.'], 400);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        return acos($dist) * 60 * 1.1515 * 1609.344; // Meter
    }
}