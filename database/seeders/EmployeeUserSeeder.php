<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeUserSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@absen.com',
                'password' => Hash::make('password123'),
                'employee_id' => 2,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@absen.com',
                'password' => Hash::make('password123'),
                'employee_id' => 3,
            ],
            [
                'name' => 'Maulana Ibrahim',
                'email' => 'maulana@absen.com',
                'password' => Hash::make('password123'),
                'employee_id' => 4,
            ],
        ];

        foreach ($employees as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        $this->command->info('✅ 3 akun karyawan berhasil dibuat!');
    }
}
