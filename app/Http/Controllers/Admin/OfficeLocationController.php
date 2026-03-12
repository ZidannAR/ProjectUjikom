<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;

class OfficeLocationController extends Controller
{
    public function index()
    {
        $locations = OfficeLocation::orderByDesc('created_at')->paginate(15);
        return view('admin.office-locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.office-locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:1',
        ]);

        OfficeLocation::create($request->only(['name', 'latitude', 'longitude', 'radius_meter']));

        return redirect()->route('admin.office-locations.index')->with('success', 'Lokasi kantor berhasil ditambahkan.');
    }

    public function edit(OfficeLocation $office_location)
    {
        return view('admin.office-locations.edit', compact('office_location'));
    }

    public function update(Request $request, OfficeLocation $office_location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:1',
        ]);

        $office_location->update($request->only(['name', 'latitude', 'longitude', 'radius_meter']));

        return redirect()->route('admin.office-locations.index')->with('success', 'Lokasi kantor berhasil diperbarui.');
    }

    public function destroy(OfficeLocation $office_location)
    {
        $office_location->delete();
        return redirect()->route('admin.office-locations.index')->with('success', 'Lokasi kantor berhasil dihapus.');
    }
}
