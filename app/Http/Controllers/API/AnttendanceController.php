<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Attendance, AttandanceLog, Employee};

class AnttendanceController extends Controller
{
    public function scan(Request $request)
    {
        // Load data karyawan beserta lokasi kantornya
        $employee = Employee::with('department.officeLocation')->findOrFail($request->employee_id);
        $office = $employee->department->officeLocation;

        // 1. Validasi QR 5 Detik [cite: 2026-01-30]
        $timestamp = floor(time() / 5);
        $validToken = hash_hmac('sha256', $timestamp, config('app.qr_secret_key'));
        
        if ($request->qr_token !== $validToken) {
            return response()->json(['message' => 'QR Expired, silakan scan ulang.'], 403);
        }

        // 2. Anti-Replay (1 QR hanya bisa discan oleh 1 orang dalam jendela 5 detik)
        if (AttandanceLog::where('qr_token_hash', $request->qr_token)->exists()) {
            return response()->json(['message' => 'QR sudah digunakan orang lain.'], 403);
        }

        // 3. Device Binding (Mengunci akun ke perangkat tertentu) [cite: 2026-01-30]
        if ($employee->registered_device_id && $employee->registered_device_id !== $request->device_id) {
            return response()->json(['message' => 'Gunakan HP yang terdaftar!'], 403);
        }

        // Jika perangkat belum terdaftar, otomatis daftarkan saat scan pertama berhasil [cite: 2026-01-30]
        if (!$employee->registered_device_id) {
            $employee->update(['registered_device_id' => $request->device_id]);
        }

        // 4. Geofencing (Validasi Jarak GPS)
        $distance = $this->calculateDistance($request->lat, $request->lng, $office->latitude, $office->longitude);
        if ($distance > $office->radius_meter) {
            return response()->json(['message' => 'Anda di luar radius kantor (' . round($distance) . 'm)'], 403);
        }

        // 5. Logika Work Date (Penanganan Shift Malam)
        // Jika scan antara jam 00:00 - 05:00 pagi, dianggap masuk ke hari sebelumnya
        $workDate = now()->hour < 5 ? now()->subDay()->toDateString() : now()->toDateString();

        return $this->processAttendance($employee, $workDate, $request);
    }

    private function processAttendance($emp, $date, $req)
    {
        // Cari data absen untuk hari kerja tersebut atau buat instance baru
        $attendance = Attendance::firstOrNew(['employee_id' => $emp->id, 'work_date' => $date]);

        if (!$attendance->exists) {
            // LOGIKA CLOCK IN (Masuk Kerja)
            $attendance->fill([
                'clock_in' => now(),
                'status_in' => 'Present',
                'gps_latitude' => $req->lat,
                'gps_longitude' => $req->lng,
                'device_id_used' => $req->device_id
            ])->save();
            
            // Catat log agar QR token ini tidak bisa dipakai oleh siapapun lagi
            AttandanceLog::create([
                'employee_id' => $emp->id, 
                'qr_token_hash' => $req->qr_token
            ]);
            
            return response()->json(['message' => 'Berhasil Masuk!']);
        }

        // LOGIKA CLOCK OUT (Pulang Kerja)
        if (!$attendance->clock_out) {
            $attendance->update([
                'clock_out' => now(), 
                'status_out' => 'Finish'
            ]);
            return response()->json(['message' => 'Berhasil Pulang!']);
        }

        return response()->json(['message' => 'Anda sudah melakukan absen masuk & pulang hari ini.'], 400);
    }
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        // Hasil dikonversi ke satuan Meter
        return acos($dist) * 60 * 1.1515 * 1609.344;
    }
}
