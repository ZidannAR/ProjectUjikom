@extends('layouts.admin')
@section('title', 'Laporan Penilaian')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Penilaian Karyawan</h1>
    <a href="{{ route('admin.assessments.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-list mr-1"></i> Semua Penilaian
    </a>
</div>

{{-- Filter --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Karyawan</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline flex-wrap" style="gap:10px;">
            <select name="employee_id" class="form-control">
                <option value="">-- Pilih Karyawan --</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>
                    {{ $emp->full_name }} ({{ $emp->department->name ?? '-' }})
                </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Lihat Laporan</button>
        </form>
    </div>
</div>

@if($selectedEmployee)
<div class="row">
    {{-- Radar Chart --}}
    <div class="col-md-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user mr-1"></i> {{ $selectedEmployee->full_name }}
                    <small class="text-muted">({{ $selectedEmployee->department->name ?? '-' }})</small>
                </h6>
            </div>
            <div class="card-body text-center">
                @if($radarData)
                <canvas id="radarChart" style="max-height: 300px;"></canvas>
                @else
                <div class="text-muted py-4"><i class="fas fa-chart-area fa-3x mb-2"></i><br>Belum ada data penilaian</div>
                @endif
            </div>
        </div>
    </div>

    {{-- History --}}
    <div class="col-md-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-1"></i> Riwayat Penilaian</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Periode</th>
                                <th>Tanggal</th>
                                <th class="text-center">Rata-rata</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historyData as $a)
                            <tr>
                                <td>
                                    <span class="badge badge-info">{{ $a->period_type }}</span>
                                    <small class="d-block text-muted">{{ $a->period_label }}</small>
                                </td>
                                <td>{{ $a->assessment_date->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <strong class="text-primary">{{ number_format($a->average_score, 2) }}</strong>
                                    <span class="text-muted small">/ 5</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.assessments.show', $a) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($historyData instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="p-3">{{ $historyData->appends(request()->all())->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<div class="card shadow">
    <div class="card-body text-center text-muted py-5">
        <i class="fas fa-user-chart fa-3x mb-3"></i>
        <p>Pilih karyawan di atas untuk melihat laporan penilaian</p>
    </div>
</div>
@endif
@endsection

@push('scripts')
@if($radarData)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('radarChart').getContext('2d'), {
    type: 'radar',
    data: @json($radarData),
    options: {
        scales: { r: { min: 0, max: 5, ticks: { stepSize: 1 } } },
        plugins: { legend: { position: 'bottom' } },
        responsive: true,
    }
});
</script>
@endif
@endpush
