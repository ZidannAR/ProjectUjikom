<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereNotNull('employee_id')
            ->with('employee.department');

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $accounts = $query->orderByDesc('created_at')->paginate(15);
        $departments = Department::all();

        // Karyawan yang belum punya akun (untuk modal create)
        $employeesWithoutAccount = Employee::whereDoesntHave('user')
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        return view('admin.accounts.index', compact('accounts', 'departments', 'employeesWithoutAccount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:users,employee_id',
            'email' => 'required|email|unique:users,email',
        ], [
            'employee_id.unique' => 'Karyawan ini sudah memiliki akun.',
            'email.unique' => 'Email sudah digunakan.',
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        User::create([
            'name' => $employee->full_name,
            'email' => $request->email,
            'password' => 'ganti123',
            'employee_id' => $employee->id,
            'must_change_password' => true,
            'is_active' => true,
        ]);

        return redirect()->route('admin.accounts.index')
            ->with('success', "Akun berhasil dibuat untuk {$employee->full_name}. Password default: ganti123");
    }

    public function resetPassword(User $user)
    {
        $user->update([
            'password' => 'ganti123',
            'must_change_password' => true,
        ]);

        // Hapus semua token (force logout)
        $user->tokens()->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', "Password {$user->name} berhasil direset ke default.");
    }

    public function toggleActive(User $user)
    {
        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        // Jika dinonaktifkan, hapus semua token
        if (!$newStatus) {
            $user->tokens()->delete();
        }

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.accounts.index')
            ->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }
}
