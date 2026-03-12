<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeApiController extends Controller
{
    /**
     * GET /api/employees → list all employees (for dropdown)
     */
    public function index()
    {
        $employees = Employee::where('is_active', true)
            ->with('shift:id,name,start_time,end_time')
            ->select('id', 'full_name', 'employee_code', 'shift_id')
            ->orderBy('full_name')
            ->get();

        return response()->json($employees);
    }

    /**
     * GET /api/employees/{id} → employee profile with shift, department & detail
     */
    public function show(Request $request, $id)
    {
        // Ownership check
        if ($request->user()->employee_id !== (int) $id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $employee = Employee::with(['shift:id,name,start_time,end_time', 'department:id,name', 'employeeDetail'])
            ->findOrFail($id);

        $detail = $employee->employeeDetail;

        return response()->json([
            'id' => $employee->id,
            'full_name' => $employee->full_name,
            'employee_code' => $employee->employee_code,
            'department' => $employee->department?->name ?? '-',
            'shift' => $employee->shift ? [
                'name' => $employee->shift->name,
                'start_time' => $employee->shift->start_time,
                'end_time' => $employee->shift->end_time,
            ] : null,
            'is_active' => $employee->is_active,
            'registered_device_id' => $employee->registered_device_id,
            'detail' => $detail ? [
                'birth_place' => $detail->birth_place,
                'birth_date' => $detail->birth_date?->format('Y-m-d'),
                'gender' => $detail->gender,
                'phone' => $detail->phone,
                'address' => $detail->address,
                'photo_url' => $detail->photo_url,
                'last_education' => $detail->last_education,
                'join_date' => $detail->join_date?->format('Y-m-d'),
            ] : null,
        ]);
    }

    /**
     * GET /api/employees/{id}/attendance/today → today's attendance
     */
    public function attendanceToday(Request $request, $id)
    {
        if ($request->user()->employee_id !== (int) $id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $employee = Employee::findOrFail($id);
        $today = Carbon::today()->toDateString();

        $attendance = $employee->attendances()
            ->where('work_date', $today)
            ->with('shift:id,name')
            ->first();

        if (!$attendance) {
            return response()->json([
                'present' => false,
                'message' => 'Belum absen hari ini',
            ]);
        }

        return response()->json([
            'present' => true,
            'work_date' => $attendance->work_date->format('Y-m-d'),
            'clock_in' => $attendance->clock_in?->format('H:i:s'),
            'clock_out' => $attendance->clock_out?->format('H:i:s'),
            'status_in' => $attendance->status_in,
            'status_out' => $attendance->status_out,
            'shift_name' => $attendance->shift?->name ?? '-',
        ]);
    }

    /**
     * GET /api/employees/{id}/attendance?month=&year= → attendance history
     */
    public function attendanceHistory(Request $request, $id)
    {
        if ($request->user()->employee_id !== (int) $id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $employee = Employee::findOrFail($id);

        $month = $request->query('month', Carbon::now()->month);
        $year = $request->query('year', Carbon::now()->year);

        $attendances = $employee->attendances()
            ->whereMonth('work_date', $month)
            ->whereYear('work_date', $year)
            ->with('shift:id,name')
            ->orderBy('work_date', 'desc')
            ->get()
            ->map(function ($att) {
                return [
                    'id' => $att->id,
                    'work_date' => $att->work_date->format('Y-m-d'),
                    'clock_in' => $att->clock_in?->format('H:i:s'),
                    'clock_out' => $att->clock_out?->format('H:i:s'),
                    'status_in' => $att->status_in,
                    'status_out' => $att->status_out,
                    'shift_name' => $att->shift?->name ?? '-',
                ];
            });

        return response()->json($attendances);
    }

    /**
     * GET /api/employees/{id}/leave-requests → leave requests for employee
     */
    public function leaveRequests(Request $request, $id)
    {
        if ($request->user()->employee_id !== (int) $id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $employee = Employee::findOrFail($id);

        $leaves = $employee->leaveRequests()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($leave) {
                $start = Carbon::parse($leave->start_date);
                $end = Carbon::parse($leave->end_date);
                return [
                    'id' => $leave->id,
                    'type' => $leave->type,
                    'start_date' => $start->format('Y-m-d'),
                    'end_date' => $end->format('Y-m-d'),
                    'days' => $start->diffInDays($end) + 1,
                    'status' => $leave->status,
                    'attachment_url' => $leave->attachment
                        ? url('storage/' . $leave->attachment)
                        : null,
                    'attachment_note' => $leave->attachment_note,
                ];
            });

        return response()->json($leaves);
    }

    /**
     * POST /api/leave-requests → create new leave request
     */
    public function storeLeaveRequest(Request $request)
    {
        $rules = [
            'type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];

        // Validasi dinamis: wajib upload bukti untuk jenis cuti tertentu
        if (LeaveRequest::requiresAttachment($request->type)) {
            $rules['attachment'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:2048';
        } else {
            $rules['attachment'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }

        $request->validate($rules, [
            'attachment.required' => 'Jenis cuti ini wajib melampirkan bukti: ' . LeaveRequest::getAttachmentLabel($request->type ?? ''),
        ]);

        // Force employee_id dari user yang sedang login
        $employeeId = $request->user()->employee_id;

        $data = [
            'employee_id' => $employeeId,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'Pending',
        ];

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leave-attachments', 'public');
            $data['attachment_note'] = LeaveRequest::getAttachmentLabel($request->type);
        }

        $leave = LeaveRequest::create($data);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim',
            'data' => $leave,
        ], 201);
    }
}
