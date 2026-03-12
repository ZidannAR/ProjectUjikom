@extends('layouts.admin')
@section('title', 'Laporan Absensi')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Absensi</h1>
    <div>
        <a href="{{ route('admin.attendance-report.export-excel', request()->query()) }}" class="btn btn-success btn-sm shadow-sm">
            <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
        </a>
        <a href="{{ route('admin.attendance-report.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm shadow-sm" target="_blank">
            <i class="fas fa-file-pdf fa-sm text-white-50"></i> Export PDF
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.attendance-report.index') }}">
            <div class="row">
                <div class="col-md-2 mb-2">
                    <label class="small">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small">Karyawan</label>
                    <select name="employee_id" class="form-control form-control-sm">
                        <option value="">-- Semua Karyawan --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small">Department</label>
                    <select name="department_id" class="form-control form-control-sm">
                        <option value="">-- Semua --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small">Status In</label>
                    <select name="status_in" class="form-control form-control-sm">
                        <option value="">-- Semua --</option>
                        @foreach(['Ontime','Present','Late','Alpha','Sick','Leave'] as $st)
                            <option value="{{ $st }}" {{ request('status_in') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fas fa-filter"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Karyawan</th>
                        <th>Department</th>
                        <th>Shift</th>
                        <th>Tanggal Kerja</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status In</th>
                        <th>Status Out</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $att)
                    <tr>
                        <td>{{ $attendances->firstItem() + $i }}</td>
                        <td>{{ $att->employee->full_name ?? '-' }}</td>
                        <td>{{ $att->employee->department->name ?? '-' }}</td>
                        <td>{{ $att->shift->name ?? '-' }}</td>
                        <td>{{ $att->work_date?->format('Y-m-d') }}</td>
                        <td>{{ $att->clock_in?->format('H:i:s') ?? '-' }}</td>
                        <td>{{ $att->clock_out?->format('H:i:s') ?? '-' }}</td>
                        <td>
                            @php
                                $colors = ['Ontime'=>'success','Present'=>'success','Late'=>'warning','Alpha'=>'danger','Sick'=>'info','Leave'=>'secondary'];
                                $c = $colors[$att->status_in] ?? 'primary';
                            @endphp
                            <span class="badge badge-{{ $c }}">{{ $att->status_in ?? '-' }}</span>
                        </td>
                        <td>
                            @if($att->status_out)
                                <span class="badge badge-success">{{ $att->status_out }}</span>
                            @else
                                <span class="badge badge-light">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted">Tidak ada data absensi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $attendances->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
