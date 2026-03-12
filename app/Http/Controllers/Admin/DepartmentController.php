<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('officeLocation')->orderByDesc('created_at')->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $officeLocations = OfficeLocation::all();
        return view('admin.departments.create', compact('officeLocations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'office_location_id' => 'required|exists:office_locations,id',
        ]);

        Department::create($request->only(['name', 'office_location_id']));

        return redirect()->route('admin.departments.index')->with('success', 'Department berhasil ditambahkan.');
    }

    public function edit(Department $department)
    {
        $officeLocations = OfficeLocation::all();
        return view('admin.departments.edit', compact('department', 'officeLocations'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'office_location_id' => 'required|exists:office_locations,id',
        ]);

        $department->update($request->only(['name', 'office_location_id']));

        return redirect()->route('admin.departments.index')->with('success', 'Department berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.departments.index')->with('success', 'Department berhasil dihapus.');
    }
}
