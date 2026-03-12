<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderByDesc('holiday_date')->paginate(15);
        return view('admin.holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('admin.holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'holiday_date' => 'required|date',
            'name' => 'required|string|max:255',
        ]);

        Holiday::create($request->only(['holiday_date', 'name']));

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function edit(Holiday $holiday)
    {
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $request->validate([
            'holiday_date' => 'required|date',
            'name' => 'required|string|max:255',
        ]);

        $holiday->update($request->only(['holiday_date', 'name']));

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil dihapus.');
    }
}
