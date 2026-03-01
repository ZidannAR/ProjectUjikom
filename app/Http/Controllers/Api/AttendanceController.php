<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Attendance, AttendanceLog, Employee};
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function generateToken()
    {
        $secret = config('app.qr_secret_key');

        $window = 100;
        $timestamp = floor(time() / $window);

        $token = hash_hmac('sha256', $timestamp, $secret);

        return response()->json([
            'token' => $token,
            'expires_in' => $window - (time() % $window),
            'server_time' => now()->toDateTimeString()
        ]);
    }
    public function scan(Request $request)
    {
        // ===== 1. Ambil data karyawan =====
        $employee = Employee::with(['shift', 'department.officeLocation'])
            ->findOrFail($request->employee_id);

        if (!$employee->shift) {
            return response()->json([
                'message' => "Karyawan {$employee->full_name} tidak memiliki shift."
            ], 400);
        }
        // ===== VALIDASI JAM SHIFT =====
        // ===== VALIDASI SHIFT & TANGGAL =====
        $now = now();
        $currentTime = $now->format('H:i:s');

        $shiftStart = $employee->shift->start_time;
        $shiftEnd   = $employee->shift->end_time;

        // ===== CEK APAKAH SHIFT MALAM (LEWAT TANGGAL) =====
        $isOvernightShift = $shiftStart > $shiftEnd;

        if ($isOvernightShift) {

            // ===== SHIFT MALAM =====
            if ($currentTime >= $shiftStart || $currentTime <= $shiftEnd) {

                // Jika setelah tengah malam → tanggal kemarin
                if ($currentTime <= $shiftEnd) {
                    $workDate = $now->copy()->subDay()->toDateString();
                } else {
                    $workDate = $now->toDateString();
                }
            } else {
                return response()->json([
                    'message' => 'Tidak bisa absen karena bukan shift anda.'
                ], 403);
            }
        } else {

            // ===== SHIFT PAGI & SIANG =====
            if ($currentTime >= $shiftStart && $currentTime <= $shiftEnd) {

                $workDate = $now->toDateString();
            } else {
                return response()->json([
                    'message' => 'Tidak bisa absen karena bukan shift anda.'
                ], 403);
            }
        }
        // ===== 2. VALIDASI QR DINAMIS =====
        $window = 100; // QR ganti tiap 30 detikx
        $timestamp = floor(time() / $window);

        $secret = config('app.qr_secret_key');

        $validNow   = hash_hmac('sha256', $timestamp,     $secret);
        $validPrev1 = hash_hmac('sha256', $timestamp - 1, $secret);
        $validPrev2 = hash_hmac('sha256', $timestamp - 2, $secret);

        if (
            $request->qr_token !== $validNow &&
            $request->qr_token !== $validPrev1 &&
            $request->qr_token !== $validPrev2
        ) {
            return response()->json([
                'message' => 'QR Expired, silakan scan ulang.',
                'server_time' => now()->toDateTimeString()
            ], 403);
        }

        // ===== 3. ANTI REPLAY =====
        if (AttendanceLog::where('qr_token_hash', $request->qr_token)->exists()) {
            return response()->json([
                'message' => 'QR sudah digunakan.'
            ], 403);
        }

        // ===== 4. DEVICE BINDING =====
        if (
            $employee->registered_device_id &&
            $employee->registered_device_id !== $request->device_id
        ) {
            return response()->json([
                'message' => 'Gunakan HP yang terdaftar!'
            ], 403);
        }

        // ===== 5. GEOFENCING =====
        $office = $employee->department->officeLocation;

        $distance = $this->calculateDistance(
            $request->lat,
            $request->lng,
            $office->latitude,
            $office->longitude
        );

        if ($distance > $office->radius_meter) {
            return response()->json([
                'message' => 'Anda di luar radius kantor (' . round($distance) . 'm)'
            ], 403);
        }

        // ===== 6. HANDLE SHIFT MALAM =====
        $workDate = now()->hour < 5
            ? now()->subDay()->toDateString()
            : now()->toDateString();

        return $this->processAttendance($employee, $workDate, $request);
    }

    private function processAttendance($emp, $date, $req)
    {
        $attendance = Attendance::firstOrNew([
            'employee_id' => $emp->id,
            'work_date'   => $date
        ]);

        // ===== CLOCK IN =====
        if (!$attendance->exists) {

            $attendance->fill([
                'clock_in' => now(),
                'status_in' => 'Present',
                'gps_latitude' => $req->lat,
                'gps_longitude' => $req->lng,
                'device_id_used' => $req->device_id
            ])->save();

            AttendanceLog::create([
                'employee_id' => $emp->id,
                'qr_token_hash' => $req->qr_token
            ]);

            return response()->json([
                'message' => 'Berhasil Masuk!'
            ]);
        }

        // ===== CLOCK OUT =====
        if (!$attendance->clock_out) {
            $attendance->update([
                'clock_out' => now(),
                'status_out' => 'Finish'
            ]);

            return response()->json([
                'message' => 'Berhasil Pulang!'
            ]);
        }

        return response()->json([
            'message' => 'Anda sudah absen hari ini.'
        ], 400);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;

        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            cos(deg2rad($theta));

        return acos($dist) * 60 * 1.1515 * 1609.344;
    }
}
