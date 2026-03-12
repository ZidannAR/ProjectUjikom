<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentCategory;
use Illuminate\Http\Request;

class AssessmentCategoryController extends Controller
{
    public function index()
    {
        $categories = AssessmentCategory::orderBy('name')->get();
        return view('admin.assessment-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.assessment-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:assessment_categories,name',
            'description' => 'nullable|string|max:1000',
            'type'        => 'nullable|string|max:100',
            'is_active'   => 'nullable|boolean',
        ]);

        AssessmentCategory::create([
            'name'        => $request->name,
            'description' => $request->description,
            'type'        => $request->type ?? 'Employee',
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.assessment-categories.index')
            ->with('success', "Kategori \"{$request->name}\" berhasil ditambahkan.");
    }

    public function edit(AssessmentCategory $assessmentCategory)
    {
        return view('admin.assessment-categories.edit', compact('assessmentCategory'));
    }

    public function update(Request $request, AssessmentCategory $assessmentCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:assessment_categories,name,' . $assessmentCategory->id,
            'description' => 'nullable|string|max:1000',
            'type'        => 'nullable|string|max:100',
            'is_active'   => 'nullable|boolean',
        ]);

        $assessmentCategory->update([
            'name'        => $request->name,
            'description' => $request->description,
            'type'        => $request->type ?? 'Employee',
            'is_active'   => $request->boolean('is_active', $assessmentCategory->is_active),
        ]);

        return redirect()->route('admin.assessment-categories.index')
            ->with('success', "Kategori \"{$assessmentCategory->name}\" berhasil diperbarui.");
    }

    public function destroy(AssessmentCategory $assessmentCategory)
    {
        // Soft-delete: nonaktifkan agar histori penilaian tidak rusak
        $assessmentCategory->update(['is_active' => false]);

        return redirect()->route('admin.assessment-categories.index')
            ->with('success', "Kategori \"{$assessmentCategory->name}\" berhasil dinonaktifkan.");
    }

    public function toggleActive(AssessmentCategory $assessmentCategory)
    {
        $assessmentCategory->update(['is_active' => !$assessmentCategory->is_active]);

        return redirect()->route('admin.assessment-categories.index')
            ->with('success', $assessmentCategory->is_active
                ? "Kategori \"{$assessmentCategory->name}\" diaktifkan."
                : "Kategori \"{$assessmentCategory->name}\" dinonaktifkan."
            );
    }
}
