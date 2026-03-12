<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Stats
        $totalActiveEmployees = Employee::where('is_active', true)->count();
        $totalAttendanceToday = Attendance::whereDate('work_date', $today)->count();
        $totalPendingLeaves = LeaveRequest::where('status', 'Pending')->count();
        $totalEmployees = Employee::count();

        // Chart: Absensi 7 hari terakhir
        $chartData = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $chartData[] = Attendance::whereDate('work_date', $date)->count();
        }

        // Tabel absensi terbaru hari ini
        $recentAttendance = Attendance::with(['employee.department', 'shift'])
            ->whereDate('work_date', $today)
            ->orderByDesc('clock_in')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalActiveEmployees',
            'totalAttendanceToday',
            'totalPendingLeaves',
            'totalEmployees',
            'chartLabels',
            'chartData',
            'recentAttendance'
        ));
    }
}
