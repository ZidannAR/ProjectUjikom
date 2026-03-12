<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['employee.department', 'shift']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('work_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('work_date', '<=', $request->end_date);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter by status_in
        if ($request->filled('status_in')) {
            $query->where('status_in', $request->status_in);
        }

        $attendances = $query->orderByDesc('work_date')->orderByDesc('clock_in')->paginate(20);
        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.attendance-report.index', compact('attendances', 'employees', 'departments'));
    }

    public function exportExcel(Request $request)
    {
        $query = Attendance::with(['employee.department', 'shift']);

        if ($request->filled('start_date')) {
            $query->whereDate('work_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('work_date', '<=', $request->end_date);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('status_in')) {
            $query->where('status_in', $request->status_in);
        }

        $attendances = $query->orderByDesc('work_date')->get();

        // CSV Export
        $filename = 'laporan_absensi_' . Carbon::now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['Nama Karyawan', 'Department', 'Shift', 'Tanggal Kerja', 'Clock In', 'Clock Out', 'Status In', 'Status Out']);

            foreach ($attendances as $att) {
                fputcsv($file, [
                    $att->employee->full_name ?? '-',
                    $att->employee->department->name ?? '-',
                    $att->shift->name ?? '-',
                    $att->work_date?->format('Y-m-d'),
                    $att->clock_in?->format('H:i:s'),
                    $att->clock_out?->format('H:i:s'),
                    $att->status_in ?? '-',
                    $att->status_out ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Attendance::with(['employee.department', 'shift']);

        if ($request->filled('start_date')) {
            $query->whereDate('work_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('work_date', '<=', $request->end_date);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('status_in')) {
            $query->where('status_in', $request->status_in);
        }

        $attendances = $query->orderByDesc('work_date')->get();

        return view('admin.attendance-report.pdf', compact('attendances'));
    }
}
