<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OfficeLocation;
use App\Models\Department;
use App\Models\Shift;
use App\Models\Employee;

class AttendanceSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     */
    public function run(): void
    {
        // 1. Buat Lokasi Kantor (Geofencing)
        // Data ini digunakan untuk memvalidasi jarak GPS saat karyawan scan [cite: 2026-01-30].
        $office = OfficeLocation::create([
            'name' => 'Kantor Pusat',
            'latitude' => -6.200000, // Ubah ke koordinat kantormu
            'longitude' => 106.816666,
            'radius_meter' => 100, // Karyawan hanya bisa absen dalam radius 100 meter
        ]);

        // 2. Buat Departemen
        $dept = Department::create([
            'name' => 'IT Department',
            'office_location_id' => $office->id
        ]);

        // 3. Buat Master 3 Shift Kerja
        // Jam kerja ini akan menentukan apakah karyawan terlambat atau tepat waktu [cite: 2026-01-30].
        $shiftPagi = Shift::create([
            'name' => 'Pagi',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00'
        ]);

        $shiftSiang = Shift::create([
            'name' => 'Siang',
            'start_time' => '14:00:00',
            'end_time' => '22:00:00'
        ]);

        $shiftMalam = Shift::create([
            'name' => 'Malam',
            'start_time' => '22:00:00',
            'end_time' => '06:00:00' // Skenario lewat tengah malam yang ditangani logika work_date [cite: 2026-01-30].
        ]);

        // 4. Buat Akun Karyawan Contoh
        Employee::create([
            'employee_code' => 'EMP001',
            'full_name' => 'Budi Tester',
            'department_id' => $dept->id,
            'shift_id' => $shiftPagi->id, 
            'qr_secret_key' => 'rahasia_budi_123', // Kunci untuk algoritma QR 5 detik [cite: 2026-01-30].
            'registered_device_id' => null, // Set null agar otomatis mengunci ke HP pertama yang scan [cite: 2026-01-30].
            'is_active' => true
        ]);

        // Pastikan shift sudah dibuat sebelumnya
$shiftPagi = Shift::where('name', 'Pagi')->first();
$shiftSiang = Shift::where('name', 'Siang')->first();
$shiftMalam = Shift::where('name', 'Malam')->first();

// Masukkan karyawan ke shift spesifik
Employee::create([
    'employee_code' => 'EMP002',
    'full_name' => 'Siti Siang',
    'department_id' => 1,
    'shift_id' => $shiftSiang->id, // Dia masuk shift siang
    'qr_secret_key' => 'secret_siti',
    'is_active' => true
]);

Employee::create([
    'employee_code' => 'EMP003',
    'full_name' => 'Maulana Malam',
    'department_id' => 1,
    'shift_id' => $shiftMalam->id, // Dia masuk shift malam
    'qr_secret_key' => 'secret_maul',
    'is_active' => true
]);
        
        $this->command->info('Data awal absensi (Kantor, Shift, & Karyawan) berhasil dibuat!');
    }
}