<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::orderByDesc('created_at')->paginate(15);
        return view('admin.shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'is_cross_day' => 'required|boolean',
        ]);

        Shift::create($request->only(['name', 'start_time', 'end_time', 'is_cross_day']));

        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil ditambahkan.');
    }

    public function edit(Shift $shift)
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'is_cross_day' => 'required|boolean',
        ]);

        $shift->update($request->only(['name', 'start_time', 'end_time', 'is_cross_day']));

        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil dihapus.');
    }
}
