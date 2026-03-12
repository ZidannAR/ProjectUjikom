<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;

class AssessmentApiController extends Controller
{
    /**
     * List penilaian milik karyawan tertentu (hanya show_to_employee = true)
     */
    public function index(Request $request, $employeeId)
    {
        // Proteksi: hanya boleh lihat milik sendiri
        $user = auth()->user();
        if (!$user || $user->employee_id != $employeeId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assessments = Assessment::where('evaluatee_id', $employeeId)
            ->where('show_to_employee', true)
            ->with(['details.category'])
            ->orderByDesc('assessment_date')
            ->get()
            ->map(function ($a) {
                return [
                    'id'              => $a->id,
                    'period_type'     => $a->period_type,
                    'period_label'    => $a->period_label,
                    'assessment_date' => $a->assessment_date?->format('Y-m-d'),
                    'average_score'   => $a->average_score,
                    'general_notes'   => $a->general_notes,
                    'categories'      => $a->details->map(fn($d) => [
                        'category_id'   => $d->category_id,
                        'category_name' => $d->category?->name,
                        'score'         => $d->score,
                    ]),
                ];
            });

        return response()->json($assessments);
    }

    /**
     * Detail penilaian tertentu (hanya jika milik sendiri & show_to_employee=true)
     */
    public function show(Request $request, Assessment $assessment)
    {
        $user = auth()->user();

        // Proteksi: hanya milik sendiri
        if (!$user || $user->employee_id != $assessment->evaluatee_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hanya tampilkan jika diizinkan
        if (!$assessment->show_to_employee) {
            return response()->json(['message' => 'Penilaian ini belum dapat dilihat.'], 403);
        }

        $assessment->load(['details.category', 'evaluator', 'evaluatee.department']);

        return response()->json([
            'id'              => $assessment->id,
            'period_type'     => $assessment->period_type,
            'period_label'    => $assessment->period_label,
            'assessment_date' => $assessment->assessment_date?->format('Y-m-d'),
            'average_score'   => $assessment->average_score,
            'general_notes'   => $assessment->general_notes,
            'show_to_employee'=> $assessment->show_to_employee,
            'evaluator'       => $assessment->evaluator?->name,
            'evaluatee'       => [
                'id'         => $assessment->evaluatee?->id,
                'full_name'  => $assessment->evaluatee?->full_name,
                'department' => $assessment->evaluatee?->department?->name,
            ],
            'categories' => $assessment->details->map(fn($d) => [
                'category_id'   => $d->category_id,
                'category_name' => $d->category?->name,
                'description'   => $d->category?->description,
                'score'         => $d->score,
            ]),
        ]);
    }
}
