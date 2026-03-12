<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with('employee.department');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderByDesc('created_at')->paginate(15);

        return view('admin.leave-requests.index', compact('leaveRequests'));
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load('employee.department', 'employee.shift');

        return view('admin.leave-requests.show', compact('leaveRequest'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update(['status' => 'Approved']);
        return redirect()->route('admin.leave-requests.index')->with('success', 'Cuti berhasil disetujui.');
    }

    public function reject(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update(['status' => 'Rejected']);
        return redirect()->route('admin.leave-requests.index')->with('success', 'Cuti berhasil ditolak.');
    }
}
