<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Cek apakah user terhubung dengan employee
        if (!$user->employee_id) {
            return response()->json([
                'message' => 'Akun ini bukan akun karyawan',
            ], 403);
        }

        // Cek akun aktif
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Akun Anda telah dinonaktifkan. Hubungi admin untuk informasi lebih lanjut.',
            ], 403);
        }

        // Cek wajib ganti password
        if ($user->must_change_password) {
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json([
                'must_change_password' => true,
                'token' => $token,
                'message' => 'Anda harus mengganti password terlebih dahulu.',
            ], 200);
        }

        // Load relasi employee + department + shift
        $user->load(['employee.department:id,name', 'employee.shift:id,name,start_time,end_time']);

        // Buat token Sanctum
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'employee' => [
                'id' => $user->employee->id,
                'employee_code' => $user->employee->employee_code,
                'full_name' => $user->employee->full_name,
                'is_active' => $user->employee->is_active,
                'department' => $user->employee->department ? [
                    'id' => $user->employee->department->id,
                    'name' => $user->employee->department->name,
                ] : null,
                'shift' => $user->employee->shift ? [
                    'id' => $user->employee->shift->id,
                    'name' => $user->employee->shift->name,
                    'start_time' => $user->employee->shift->start_time,
                    'end_time' => $user->employee->shift->end_time,
                ] : null,
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load(['employee.department:id,name', 'employee.shift:id,name,start_time,end_time']);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'employee' => $user->employee ? [
                'id' => $user->employee->id,
                'employee_code' => $user->employee->employee_code,
                'full_name' => $user->employee->full_name,
                'is_active' => $user->employee->is_active,
                'department' => $user->employee->department ? [
                    'id' => $user->employee->department->id,
                    'name' => $user->employee->department->name,
                ] : null,
                'shift' => $user->employee->shift ? [
                    'id' => $user->employee->shift->id,
                    'name' => $user->employee->shift->name,
                    'start_time' => $user->employee->shift->start_time,
                    'end_time' => $user->employee->shift->end_time,
                ] : null,
            ] : null,
        ]);
    }

    /**
     * POST /api/auth/change-password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak cocok.'],
            ]);
        }

        // Password baru tidak boleh sama dengan yang lama
        if (Hash::check($request->new_password, $user->password)) {
            throw ValidationException::withMessages([
                'new_password' => ['Password baru tidak boleh sama dengan password lama.'],
            ]);
        }

        $user->update([
            'password' => $request->new_password,
            'must_change_password' => false,
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah',
        ]);
    }
}
