<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDetail;
use App\Models\User;
use App\Models\Department;
use App\Models\Shift;
use App\Exports\EmployeesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'shift', 'employeeDetail']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $employees = $query->orderByDesc('created_at')->paginate(15);
        $departments = Department::all();
        $shifts = Shift::all();

        return view('admin.employees.index', compact('employees', 'departments', 'shifts'));
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'shift', 'employeeDetail']);
        return view('admin.employees.show', compact('employee'));
    }

    public function create()
    {
        $departments = Department::all();
        $shifts = Shift::all();
        return view('admin.employees.create', compact('departments', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
            'full_name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'shift_id' => 'required|exists:shifts,id',
            'is_active' => 'required|boolean',
            // Detail fields
            'nik' => 'nullable|digits:16|unique:employee_details,nik',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'phone' => 'nullable|string|min:10|max:15',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'last_education' => 'nullable|in:SD,SMP,SMA/SMK,D1,D2,D3,S1,S2,S3',
            'join_date' => 'nullable|date',
            // Account fields
            'create_account' => 'nullable|boolean',
            'account_email' => 'required_if:create_account,1|nullable|email|unique:users,email',
        ]);

        $employee = Employee::create([
            'employee_code' => $request->employee_code,
            'full_name' => $request->full_name,
            'department_id' => $request->department_id,
            'shift_id' => $request->shift_id,
            'is_active' => $request->is_active,
            'qr_secret_key' => bin2hex(random_bytes(16)),
        ]);

        // Create detail if any field is filled
        $detailFields = ['nik', 'birth_place', 'birth_date', 'gender', 'phone', 'address', 'last_education', 'join_date'];
        $detailData = $request->only($detailFields);
        $hasDetail = collect($detailData)->filter()->isNotEmpty() || $request->hasFile('photo');

        if ($hasDetail) {
            $detailData['employee_id'] = $employee->id;
            if ($request->hasFile('photo')) {
                $detailData['photo'] = $request->file('photo')->store('employee-photos', 'public');
            }
            EmployeeDetail::create(array_filter($detailData, fn($v) => $v !== null && $v !== ''));
        }

        // Create user account if requested
        if ($request->create_account) {
            User::create([
                'name' => $employee->full_name,
                'email' => $request->account_email,
                'password' => 'ganti123',
                'employee_id' => $employee->id,
                'must_change_password' => true,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $employee->load('employeeDetail');
        $departments = Department::all();
        $shifts = Shift::all();
        return view('admin.employees.edit', compact('employee', 'departments', 'shifts'));
    }

    public function update(Request $request, Employee $employee)
    {
        $employee->load('employeeDetail');

        $request->validate([
            'employee_code' => 'required|string|max:50|unique:employees,employee_code,' . $employee->id,
            'full_name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'shift_id' => 'required|exists:shifts,id',
            'is_active' => 'required|boolean',
            // Detail fields
            'nik' => 'nullable|digits:16|unique:employee_details,nik,' . ($employee->employeeDetail?->id),
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'phone' => 'nullable|string|min:10|max:15',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'last_education' => 'nullable|in:SD,SMP,SMA/SMK,D1,D2,D3,S1,S2,S3',
            'join_date' => 'nullable|date',
        ]);

        $employee->update($request->only(['employee_code', 'full_name', 'department_id', 'shift_id', 'is_active']));

        // Upsert employee detail
        $detailData = $request->only(['nik', 'birth_place', 'birth_date', 'gender', 'phone', 'address', 'last_education', 'join_date']);

        if ($request->hasFile('photo')) {
            if ($employee->employeeDetail?->photo) {
                Storage::disk('public')->delete($employee->employeeDetail->photo);
            }
            $detailData['photo'] = $request->file('photo')->store('employee-photos', 'public');
        }

        $employee->employeeDetail()->updateOrCreate(
            ['employee_id' => $employee->id],
            $detailData
        );

        return redirect()->route('admin.employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }

    public function resetDevice(Employee $employee)
    {
        $employee->update(['registered_device_id' => null]);
        return redirect()->route('admin.employees.index')->with('success', "Device untuk {$employee->full_name} berhasil direset.");
    }

    public function exportExcel()
    {
        return Excel::download(new EmployeesExport, 'data-karyawan-' . date('Y-m-d') . '.xlsx');
    }
}
