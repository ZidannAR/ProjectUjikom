@extends('layouts.admin')
@section('title', 'Penilaian Karyawan')

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<style>
    .star-display { color: #f6c23e; font-size: 1.1rem; }
    .star-display .empty { color: #ddd; }
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Penilaian Karyawan</h1>
    <a href="{{ route('admin.assessments.create') }}" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-star fa-sm mr-1"></i> Beri Penilaian Baru
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

{{-- Progress Bar Bulan Ini --}}
<div class="card shadow mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="font-weight-bold">Penilaian Bulanan – {{ $thisMonthLabel }}</span>
            <span class="text-muted small">{{ $sudahDinilai }} dari {{ $totalKaryawan }} karyawan</span>
        </div>
        @php $pct = $totalKaryawan > 0 ? round(($sudahDinilai / $totalKaryawan) * 100) : 0; @endphp
        <div class="progress" style="height: 16px; border-radius: 8px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pct }}%">{{ $pct }}%</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" class="form-inline flex-wrap" style="gap: 10px;">
            <select name="employee_id" class="form-control form-control-sm">
                <option value="">Semua Karyawan</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
            <select name="department_id" class="form-control form-control-sm">
                <option value="">Semua Departemen</option>
                @foreach($departments as $dep)
                <option value="{{ $dep->id }}" @selected(request('department_id') == $dep->id)>{{ $dep->name }}</option>
                @endforeach
            </select>
            <select name="period_type" class="form-control form-control-sm">
                <option value="">Semua Periode</option>
                <option value="Bulanan" @selected(request('period_type') === 'Bulanan')>Bulanan</option>
                <option value="Mingguan" @selected(request('period_type') === 'Mingguan')>Mingguan</option>
                <option value="Harian" @selected(request('period_type') === 'Harian')>Harian</option>
            </select>
            <select name="month" class="form-control form-control-sm">
                <option value="">Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" @selected(request('month') == $m)>{{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}</option>
                @endfor
            </select>
            <input type="number" name="year" class="form-control form-control-sm" style="width:90px" placeholder="Tahun" value="{{ request('year', now()->year) }}">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.assessments.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tbl-assessment" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Karyawan</th>
                        <th>Departemen</th>
                        <th>Periode</th>
                        <th class="text-center">Rata-rata</th>
                        <th>Tanggal</th>
                        <th class="text-center">Tampil ke Karyawan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assessments as $i => $assessment)
                    @php
                        $avg = $assessment->average_score;
                        $stars = round($avg);
                    @endphp
                    <tr>
                        <td>{{ ($assessments->currentPage() - 1) * $assessments->perPage() + $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($assessment->evaluatee->full_name ?? 'K') }}&background=4e73df&color=fff&size=32"
                                     class="rounded-circle mr-2" width="32" height="32">
                                <strong>{{ $assessment->evaluatee->full_name ?? '-' }}</strong>
                            </div>
                        </td>
                        <td>{{ $assessment->evaluatee->department->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-info">{{ $assessment->period_type }}</span>
                            <small class="d-block text-muted">{{ $assessment->period_label }}</small>
                        </td>
                        <td class="text-center">
                            <div class="star-display">
                                @for($s = 1; $s <= 5; $s++)
                                    <i class="fas fa-star {{ $s <= $stars ? '' : 'empty' }}"></i>
                                @endfor
                            </div>
                            <small class="text-muted">{{ number_format($avg, 2) }} / 5</small>
                        </td>
                        <td>{{ $assessment->assessment_date->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $assessment->show_to_employee ? 'success' : 'secondary' }}">
                                {{ $assessment->show_to_employee ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.assessments.edit', $assessment) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.assessments.destroy', $assessment) }}" method="POST" class="d-inline" id="del-{{ $assessment->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('del-{{ $assessment->id }}')" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data penilaian</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $assessments->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(formId) {
    Swal.fire({ title: 'Hapus penilaian ini?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#e74a3b' })
        .then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
}
</script>
@endpush
