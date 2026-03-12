<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class AttendanceDummySeeder extends Seeder
{
    public function run(): void
    {
        // Jika sudah ada data, truncate dulu lalu regenerate
        if (Attendance::count() > 0) {
            $this->command->warn('Tabel attendance sudah ada data. Menghapus dan membuat ulang...');
            Attendance::truncate();
        }

        // Ambil semua karyawan aktif beserta shift-nya
        $employees = Employee::where('is_active', true)
            ->with('shift')
            ->get();

        if ($employees->isEmpty()) {
            $this->command->warn('Tidak ada karyawan aktif ditemukan. Seeder dibatalkan.');
            return;
        }

        // Ambil semua tanggal holiday
        $holidays = Holiday::pluck('holiday_date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        $start = Carbon::parse('2026-03-01');
        $end   = Carbon::parse('2027-02-28');
        $period = CarbonPeriod::create($start, $end);

        $batch = [];
        $batchSize = 500;
        $totalInserted = 0;

        foreach ($period as $date) {
            // Skip Sabtu (6) & Minggu (0)
            if (in_array($date->dayOfWeek, [0, 6])) continue;

            // Skip hari libur
            if (in_array($date->format('Y-m-d'), $holidays)) continue;

            $this->command->info('Generating attendance for: ' . $date->format('Y-m-d'));

            foreach ($employees as $employee) {
                if (!$employee->shift) continue;

                $shift = $employee->shift;

                // Clock in: start_time + random 0-10 menit
                $clockIn = Carbon::parse(
                    $date->format('Y-m-d') . ' ' . $shift->start_time
                )->addMinutes(rand(0, 10));

                // Clock out: end_time + random 0-30 menit
                // Handle cross day shift
                $clockOutDate = $shift->is_cross_day
                    ? $date->copy()->addDay()
                    : $date->copy();

                $clockOut = Carbon::parse(
                    $clockOutDate->format('Y-m-d') . ' ' . $shift->end_time
                )->addMinutes(rand(0, 30));

                $batch[] = [
                    'employee_id'    => $employee->id,
                    'shift_id'       => $shift->id,
                    'work_date'      => $date->format('Y-m-d'),
                    'clock_in'       => $clockIn->format('Y-m-d H:i:s'),
                    'clock_out'      => $clockOut->format('Y-m-d H:i:s'),
                    'status_in'      => 'Ontime',
                    'status_out'     => 'Ontime',
                    'gps_latitude'   => null,
                    'gps_longitude'  => null,
                    'device_id_used' => null,
                    'created_at'     => $date->format('Y-m-d H:i:s'),
                    'updated_at'     => $date->format('Y-m-d H:i:s'),
                ];

                // Batch insert setiap $batchSize record
                if (count($batch) >= $batchSize) {
                    Attendance::insert($batch);
                    $totalInserted += count($batch);
                    $batch = [];
                }
            }
        }

        // Insert sisa batch yang belum di-flush
        if (!empty($batch)) {
            Attendance::insert($batch);
            $totalInserted += count($batch);
        }

        $this->command->info("✅ Attendance dummy data selesai dibuat! Total: {$totalInserted} record.");
    }
}
