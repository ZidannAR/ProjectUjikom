<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department; // Tambahkan ini
use App\Models\OfficeLocation; // Tambahkan ini
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Lokasi Kantor dulu (syarat departemen)
        $office = OfficeLocation::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Kantor Pusat',
                'latitude' => -6.8269999995731725, 
                'longitude' => 107.13725399966268,
                'radius_meter' => 100
            ]
        );

        // 2. Buat Departemen (syarat karyawan)
        $dept = Department::updateOrCreate(
            ['id' => 1],
            ['name' => 'IT Department', 'office_location_id' => $office->id]
        );

        // 3. Baru masukkan Karyawan
        $employee = Employee::create([
            'employee_code' => 'EMP-TEST-01',
            'full_name' => 'Karyawan Tester',
            'department_id' => $dept->id, // Menggunakan ID yang baru dibuat
            'qr_secret_key' => config('app.qr_secret_key'), // [cite: 2026-01-30]
            'registered_device_id' => null, // [cite: 2026-01-30]
            'is_active' => true,
        ]);

        // User::create([
        //     'employee_id' => $employee->id,
        //     'email' => 'karyawan@test.com',
        //     'password' => Hash::make('password123'),
        // ]);

        $this->command->info('Data berhasil disinkronkan!');
    }
}