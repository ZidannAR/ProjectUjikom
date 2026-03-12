<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDetailController extends Controller
{
    /**
     * GET /api/employees/{id}/detail
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->employee_id !== (int) $id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $employee = Employee::findOrFail($id);
        $detail = $employee->employeeDetail;

        return response()->json([
            'has_detail' => !!$detail,
            'detail' => $detail ? [
                'nik' => $detail->nik,
                'birth_place' => $detail->birth_place,
                'birth_date' => $detail->birth_date?->format('Y-m-d'),
                'gender' => $detail->gender,
                'phone' => $detail->phone,
                'address' => $detail->address,
                'photo_url' => $detail->photo_url,
                'last_education' => $detail->last_education,
                'join_date' => $detail->join_date?->format('Y-m-d'),
            ] : null,
            'is_complete' => $employee->isProfileComplete(),
        ]);
    }

    /**
     * POST /api/employees/{id}/detail (create)
     */
    public function store(Request $request, $id)
    {
        if ($request->user()->employee_id !== (int) $id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $employee = Employee::findOrFail($id);

        if ($employee->employeeDetail) {
            return response()->json(['message' => 'Detail sudah ada, gunakan PUT untuk update'], 409);
        }

        $validated = $request->validate($this->rules());

        $data = collect($validated)->except('photo')->toArray();
        $data['employee_id'] = $employee->id;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employee-photos', 'public');
        }

        $detail = EmployeeDetail::create($data);

        return response()->json([
            'message' => 'Detail profil berhasil disimpan',
            'data' => $detail,
        ], 201);
    }

    /**
     * PUT /api/employees/{id}/detail (update)
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->employee_id !== (int) $id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $employee = Employee::findOrFail($id);
        $detail = $employee->employeeDetail;

        if (!$detail) {
            return response()->json(['message' => 'Detail belum ada, gunakan POST untuk buat baru'], 404);
        }

        $validated = $request->validate($this->rules($detail->id));

        $data = collect($validated)->except('photo')->toArray();

        if ($request->hasFile('photo')) {
            // Hapus foto lama
            if ($detail->photo) {
                Storage::disk('public')->delete($detail->photo);
            }
            $data['photo'] = $request->file('photo')->store('employee-photos', 'public');
        }

        $detail->update($data);

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => $detail->fresh(),
        ]);
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'nik' => 'required|string|size:16|unique:employee_details,nik,' . $ignoreId,
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'phone' => 'required|string|min:10|max:15',
            'address' => 'required|string|max:500',
            'last_education' => 'nullable|in:SD,SMP,SMA/SMK,D1,D2,D3,S1,S2,S3',
            'join_date' => 'nullable|date',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
