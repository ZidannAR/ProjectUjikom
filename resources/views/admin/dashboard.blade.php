@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row - Stats Cards -->
<div class="row">
    <!-- Total Karyawan Aktif -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Karyawan Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalActiveEmployees }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Absensi Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Absensi Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAttendanceToday }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuti Pending -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Cuti Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPendingLeaves }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Karyawan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Karyawan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalEmployees }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Chart -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Grafik Absensi 7 Hari Terakhir</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Hari Ini</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-xs font-weight-bold text-success">Hadir</span>
                        <span class="text-xs font-weight-bold">{{ $totalAttendanceToday }} / {{ $totalActiveEmployees }}</span>
                    </div>
                    @php $pct = $totalActiveEmployees > 0 ? round(($totalAttendanceToday / $totalActiveEmployees) * 100) : 0; @endphp
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%">{{ $pct }}%</div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-xs font-weight-bold text-warning">Cuti Pending</span>
                        <span class="text-xs font-weight-bold">{{ $totalPendingLeaves }}</span>
                    </div>
                </div>
                <hr>
                <p class="text-center text-muted small mb-0">Data per {{ now()->translatedFormat('d F Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Recent Attendance Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Absensi Terbaru Hari Ini</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama</th>
                                <th>Department</th>
                                <th>Shift</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Status In</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendance as $att)
                            <tr>
                                <td>{{ $att->employee->full_name ?? '-' }}</td>
                                <td>{{ $att->employee->department->name ?? '-' }}</td>
                                <td>{{ $att->shift->name ?? '-' }}</td>
                                <td>{{ $att->clock_in ? $att->clock_in->format('H:i:s') : '-' }}</td>
                                <td>{{ $att->clock_out ? $att->clock_out->format('H:i:s') : '-' }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'Ontime' => 'success', 'Present' => 'success',
                                            'Late' => 'warning',
                                            'Alpha' => 'danger',
                                            'Sick' => 'info',
                                            'Leave' => 'secondary',
                                        ];
                                        $color = $statusColors[$att->status_in] ?? 'primary';
                                    @endphp
                                    <span class="badge badge-{{ $color }}">{{ $att->status_in ?? '-' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data absensi hari ini</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Jumlah Absensi',
                data: {!! json_encode($chartData) !!},
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush
