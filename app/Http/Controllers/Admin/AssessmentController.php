<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentDetail;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Helper: generate period_label berdasarkan period_type + tanggal
     */
    private function generatePeriodLabel(string $type, Carbon $date): string
    {
        return match ($type) {
            'Harian'   => $date->isoFormat('dddd, D MMMM Y'),
            'Mingguan' => 'Minggu ' . $date->weekOfMonth . ' ' . $date->isoFormat('MMMM Y'),
            'Bulanan'  => $date->isoFormat('MMMM Y'),
            default    => $date->isoFormat('MMMM Y'),
        };
    }

    public function index(Request $request)
    {
        $query = Assessment::with(['evaluatee.department', 'details']);

        if ($request->filled('employee_id')) {
            $query->where('evaluatee_id', $request->employee_id);
        }
        if ($request->filled('department_id')) {
            $query->whereHas('evaluatee', fn($q) => $q->where('department_id', $request->department_id));
        }
        if ($request->filled('period_type')) {
            $query->where('period_type', $request->period_type);
        }
        if ($request->filled('month') && $request->filled('year')) {
            $label = Carbon::createFromDate($request->year, $request->month, 1)->isoFormat('MMMM Y');
            $query->where('period_label', 'like', "%{$label}%");
        }

        $assessments  = $query->orderByDesc('assessment_date')->paginate(20)->withQueryString();
        $employees    = Employee::where('is_active', true)->with('department')->orderBy('full_name')->get();
        $departments  = Department::orderBy('name')->get();

        // Progress bulan ini
        $thisMonthLabel = Carbon::now()->isoFormat('MMMM Y');
        $totalKaryawan  = Employee::where('is_active', true)->count();
        $sudahDinilai   = Assessment::where('period_type', 'Bulanan')
            ->where('period_label', $thisMonthLabel)->count();

        return view('admin.assessments.index', compact(
            'assessments', 'employees', 'departments',
            'totalKaryawan', 'sudahDinilai', 'thisMonthLabel'
        ));
    }

    public function create()
    {
        $employees  = Employee::where('is_active', true)->with('department')->orderBy('full_name')->get();
        $categories = AssessmentCategory::active()->get();
        return view('admin.assessments.create', compact('employees', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'evaluatee_id'    => 'required|exists:employees,id',
            'assessment_date' => 'required|date',
            'period_type'     => 'required|in:Harian,Mingguan,Bulanan',
            'general_notes'   => 'nullable|string|max:3000',
            'show_to_employee'=> 'nullable|boolean',
            'scores'          => 'required|array|min:1',
            'scores.*'        => 'required|integer|min:1|max:5',
        ]);

        $date        = Carbon::parse($request->assessment_date);
        $periodLabel = $this->generatePeriodLabel($request->period_type, $date);

        // Cek duplikat
        $existing = Assessment::where('evaluatee_id', $request->evaluatee_id)
            ->where('period_type', $request->period_type)
            ->where('period_label', $periodLabel)
            ->first();

        if ($existing) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['duplicate' => true, 'assessment_id' => $existing->id, 'period_label' => $periodLabel]);
            }
            return back()->withInput()->with('error', "Karyawan ini sudah dinilai untuk periode \"{$periodLabel}\". Silakan edit penilaian yang ada.");
        }

        $assessment = Assessment::create([
            'evaluator_id'    => auth()->id() ?? 1,
            'evaluatee_id'    => $request->evaluatee_id,
            'assessment_date' => $request->assessment_date,
            'period_type'     => $request->period_type,
            'period_label'    => $periodLabel,
            'general_notes'   => $request->general_notes,
            'show_to_employee'=> $request->boolean('show_to_employee', true),
        ]);

        foreach ($request->scores as $categoryId => $score) {
            AssessmentDetail::create([
                'assessment_id' => $assessment->id,
                'category_id'   => $categoryId,
                'score'         => $score,
            ]);
        }

        if ($request->filled('save_next')) {
            return $this->redirectToNextEmployee($request->evaluatee_id, $request->period_type, $periodLabel);
        }

        return redirect()->route('admin.assessments.show', $assessment)
            ->with('success', "Penilaian karyawan berhasil disimpan untuk periode {$periodLabel}.");
    }

    private function redirectToNextEmployee(int $currentId, string $periodType, string $periodLabel)
    {
        $assessedIds = Assessment::where('period_type', $periodType)
            ->where('period_label', $periodLabel)->pluck('evaluatee_id');

        $next = Employee::where('is_active', true)
            ->whereNotIn('id', $assessedIds)
            ->orderBy('full_name')->first();

        if (!$next) {
            return redirect()->route('admin.assessments.index')
                ->with('success', '🎉 Semua karyawan sudah dinilai untuk periode ini!');
        }

        return redirect()->route('admin.assessments.create')
            ->with('info', "Lanjut menilai: {$next->full_name}");
    }

    public function show(Assessment $assessment)
    {
        $assessment->load(['evaluatee.department', 'evaluatee.employeeDetail', 'evaluator', 'details.category']);
        return view('admin.assessments.show', compact('assessment'));
    }

    public function edit(Assessment $assessment)
    {
        $assessment->load(['details.category']);
        $categories     = AssessmentCategory::active()->get();
        $existingScores = $assessment->details->keyBy('category_id');
        return view('admin.assessments.edit', compact('assessment', 'categories', 'existingScores'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $request->validate([
            'general_notes'   => 'nullable|string|max:3000',
            'show_to_employee'=> 'nullable|boolean',
            'scores'          => 'required|array|min:1',
            'scores.*'        => 'required|integer|min:1|max:5',
        ]);

        $assessment->update([
            'general_notes'   => $request->general_notes,
            'show_to_employee'=> $request->boolean('show_to_employee'),
        ]);

        // Hapus & create ulang details
        $assessment->details()->delete();
        foreach ($request->scores as $categoryId => $score) {
            AssessmentDetail::create([
                'assessment_id' => $assessment->id,
                'category_id'   => $categoryId,
                'score'         => $score,
            ]);
        }

        return redirect()->route('admin.assessments.show', $assessment)
            ->with('success', 'Penilaian berhasil diperbarui.');
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();
        return redirect()->route('admin.assessments.index')
            ->with('success', 'Penilaian berhasil dihapus.');
    }

    public function report(Request $request)
    {
        $employees   = Employee::where('is_active', true)->with('department')->orderBy('full_name')->get();
        $departments = Department::orderBy('name')->get();

        $selectedEmployee = null;
        $radarData        = null;
        $historyData      = collect();

        if ($request->filled('employee_id')) {
            $selectedEmployee = Employee::with(['department', 'employeeDetail'])->find($request->employee_id);
            $categories       = AssessmentCategory::active()->get();

            $assessments = Assessment::where('evaluatee_id', $request->employee_id)
                ->with(['details.category'])
                ->orderByDesc('assessment_date')
                ->take(6)->get();

            $historyData = Assessment::where('evaluatee_id', $request->employee_id)
                ->with('details')->orderByDesc('assessment_date')->paginate(10);

            // Build radar data (latest assessment)
            if ($assessments->isNotEmpty()) {
                $latest = $assessments->first();
                $radarData = [
                    'labels'   => $categories->pluck('name')->toArray(),
                    'datasets' => [[
                        'label'                => $latest->period_label,
                        'data'                 => $categories->map(fn($c) => optional($latest->details->firstWhere('category_id', $c->id))->score ?? 0)->toArray(),
                        'backgroundColor'      => 'rgba(78, 115, 223, 0.2)',
                        'borderColor'          => 'rgba(78, 115, 223, 1)',
                        'pointBackgroundColor' => 'rgba(78, 115, 223, 1)',
                    ]],
                ];
            }
        }

        return view('admin.assessments.report', compact(
            'employees', 'departments', 'selectedEmployee', 'radarData', 'historyData'
        ));
    }
}
