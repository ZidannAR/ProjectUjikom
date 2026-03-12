@extends('layouts.admin')
@section('title', 'Detail Penilaian')

@push('styles')
<style>
.star-display { color: #f6c23e; font-size: 1.3rem; }
.star-display .empty { color: #e0e0e0; }
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Penilaian</h1>
    <div>
        <a href="{{ route('admin.assessments.edit', $assessment) }}" class="btn btn-warning btn-sm mr-2">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <a href="{{ route('admin.assessments.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    {{-- Profil + Ringkasan --}}
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                @php $emp = $assessment->evaluatee; @endphp
                @if($emp->employeeDetail?->photo)
                    <img src="{{ asset('storage/' . $emp->employeeDetail->photo) }}" class="rounded-circle mb-3" width="90" height="90" style="object-fit:cover;">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->full_name ?? 'K') }}&background=4e73df&color=fff&size=90" class="rounded-circle mb-3">
                @endif
                <h5 class="font-weight-bold mb-0">{{ $emp->full_name ?? '-' }}</h5>
                <p class="text-muted mb-1">{{ $emp->department->name ?? '-' }}</p>
                <p class="text-muted small">{{ $emp->employee_code ?? '-' }}</p>
                <hr>
                <p class="mb-1 small"><i class="fas fa-calendar mr-1"></i> {{ $assessment->period_type }}: <strong>{{ $assessment->period_label }}</strong></p>
                <p class="mb-1 small"><i class="fas fa-user-tie mr-1"></i> Penilai: {{ $assessment->evaluator->name ?? 'Admin' }}</p>
                <p class="mb-1 small"><i class="fas fa-clock mr-1"></i> {{ $assessment->assessment_date->format('d/m/Y') }}</p>
                <p class="mb-0">
                    <span class="badge badge-{{ $assessment->show_to_employee ? 'success' : 'secondary' }}">
                        {{ $assessment->show_to_employee ? '👁 Tampil ke Karyawan' : '🔒 Disembunyikan' }}
                    </span>
                </p>
            </div>
        </div>

        <div class="card shadow mb-4 border-left-primary">
            <div class="card-body text-center">
                <div class="text-xs text-primary font-weight-bold text-uppercase mb-2">Rata-rata Nilai</div>
                <div class="star-display h3">
                    @for($s = 1; $s <= 5; $s++)
                        <i class="fas fa-star {{ $s <= round($assessment->average_score) ? '' : 'empty' }}"></i>
                    @endfor
                </div>
                <h2 class="font-weight-bold text-primary">{{ number_format($assessment->average_score, 2) }}</h2>
                <p class="text-muted small">dari 5</p>
            </div>
        </div>
    </div>

    {{-- Radar Chart + Detail --}}
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-spider mr-1"></i> Grafik Radar Performa</h6>
            </div>
            <div class="card-body">
                <canvas id="radarChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-1"></i> Nilai per Kategori</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr><th>Kategori</th><th class="text-center">Bintang</th><th class="text-center">Score</th></tr>
                        </thead>
                        <tbody>
                            @foreach($assessment->details as $detail)
                            <tr>
                                <td>
                                    <strong>{{ $detail->category->name ?? '-' }}</strong>
                                    @if($detail->category?->description)
                                    <br><small class="text-muted">{{ $detail->category->description }}</small>
                                    @endif
                                </td>
                                <td class="text-center star-display">
                                    @for($s = 1; $s <= 5; $s++)
                                        <i class="fas fa-star {{ $s <= $detail->score ? '' : 'empty' }}" style="font-size:.95rem;"></i>
                                    @endfor
                                </td>
                                <td class="text-center font-weight-bold">{{ $detail->score }} / 5</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <th colspan="2" class="text-right">Rata-rata:</th>
                                <th class="text-center text-primary">{{ number_format($assessment->average_score, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @if($assessment->general_notes)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-quote-left mr-1"></i> Catatan Umum</h6>
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <p>{{ $assessment->general_notes }}</p>
                    <footer class="blockquote-footer">{{ $assessment->evaluator->name ?? 'Admin' }}</footer>
                </blockquote>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
var ctx = document.getElementById('radarChart').getContext('2d');
new Chart(ctx, {
    type: 'radar',
    data: {
        labels: @json($assessment->details->map(fn($d) => $d->category?->name ?? '-')),
        datasets: [{
            label: '{{ $assessment->period_label }}',
            data: @json($assessment->details->pluck('score')),
            backgroundColor: 'rgba(78, 115, 223, 0.15)',
            borderColor: 'rgba(78, 115, 223, 1)',
            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
            pointBorderColor: '#fff',
            pointHoverRadius: 6,
        }]
    },
    options: {
        scales: { r: { min: 0, max: 5, ticks: { stepSize: 1 } } },
        plugins: { legend: { position: 'bottom' } },
        responsive: true,
    }
});
</script>
@endpush
