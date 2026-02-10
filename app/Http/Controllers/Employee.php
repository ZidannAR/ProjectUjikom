<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;
use App\Models\OfficeLocation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Lokasi Kantor (WAJIB: Sesuaikan Koordinat dengan Lokasi Anda Sekarang)
        $office = OfficeLocation::create([
            'name' => 'Kantor Uji Coba',
            'latitude' => -6.175392, // <-- GANTI: Pakai koordinat tempat Anda sekarang
            'longitude' => 106.827153, // <-- GANTI: Agar tidak kena error Geofencing
            'radius_meter' => 100, // Radius agak lebar (100m) untuk testing
        ]);

        // 2. Buat Departemen
        $dept = Department::create([
            'name' => 'IT Research',
            'office_location_id' => $office->id
        ]);

        // 3. Buat Data Karyawan Testing
        $employee = Employee::create([
            'employee_code' => 'DEV-001',
            'full_name' => 'Budi Tester',
            'department_id' => $dept->id,
            'qr_secret_key' => config('app.qr_secret_key'), // Mengambil key dari .env [cite: 2026-01-30]
            'registered_device_id' => null, // Set null dulu agar login/scan pertama bisa mendaftarkan HP [cite: 2026-01-30]
            'is_active' => true,
        ]);

        // 4. Buat User Login (Opsional jika ingin test login)
        User::create([
            'employee_id' => $employee->id,
            'email' => 'budi@test.com',
            'password' => Hash::make('password123'),
            'role' => 'Staff'
        ]);

        $this->command->info('Data Tester Berhasil Dibuat!');
        $this->command->warn('Ingat: Sesuaikan koordinat OfficeLocation dengan lokasi Anda sekarang agar scan tidak ditolak!');
    }
}