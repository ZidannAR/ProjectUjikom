<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    public function index()
    {
        $logs = AttendanceLog::with('employee')
            ->orderByDesc('scanned_at')
            ->paginate(20);

        return view('admin.attendance-logs.index', compact('logs'));
    }
}
