@extends('layouts.admin')
@section('title', 'Log Absensi')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Log Absensi (Raw QR Scan)</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover dataTable" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama Karyawan</th>
                        <th>QR Token Hash</th>
                        <th>Waktu Scan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $i => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $i }}</td>
                        <td>{{ $log->employee->full_name ?? '-' }}</td>
                        <td>
                            <code title="{{ $log->qr_token_hash }}">{{ \Illuminate\Support\Str::limit($log->qr_token_hash, 16, '****') }}</code>
                        </td>
                        <td>{{ $log->scanned_at ? \Carbon\Carbon::parse($log->scanned_at)->format('d M Y H:i:s') : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted">Tidak ada data log</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
