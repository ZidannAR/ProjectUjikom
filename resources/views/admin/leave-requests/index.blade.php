@extends('layouts.admin')
@section('title', 'Manajemen Cuti')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Cuti (Leave Requests)</h1>
</div>

<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.leave-requests.index') }}">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <select name="status" class="form-control form-control-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-sync"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover dataTable" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Karyawan</th>
                        <th>Department</th>
                        <th>Tipe</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $i => $leave)
                    <tr>
                        <td>{{ $leaveRequests->firstItem() + $i }}</td>
                        <td>{{ $leave->employee->full_name ?? '-' }}</td>
                        <td>{{ $leave->employee->department->name ?? '-' }}</td>
                        <td>{{ $leave->type }}</td>
                        <td>{{ $leave->start_date?->format('Y-m-d') }}</td>
                        <td>{{ $leave->end_date?->format('Y-m-d') }}</td>
                        <td>
                            @if($leave->attachment)
                                <span class="badge badge-info"><i class="fas fa-paperclip"></i> Ada Bukti</span>
                            @else
                                @if(\App\Models\LeaveRequest::requiresAttachment($leave->type))
                                    <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Tanpa Bukti</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @php
                                $statusColors = ['Pending' => 'warning', 'Approved' => 'success', 'Rejected' => 'danger'];
                                $sc = $statusColors[$leave->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $sc }}">{{ $leave->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.leave-requests.show', $leave) }}" class="btn btn-info btn-sm" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($leave->status === 'Pending')
                                <form action="{{ route('admin.leave-requests.approve', $leave) }}" method="POST" class="d-inline" id="approve-form-{{ $leave->id }}">
                                    @csrf @method('PATCH')
                                    <button type="button" class="btn btn-success btn-sm" onclick="confirmAction('approve-form-{{ $leave->id }}', 'approve')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.leave-requests.reject', $leave) }}" method="POST" class="d-inline" id="reject-form-{{ $leave->id }}">
                                    @csrf @method('PATCH')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('reject-form-{{ $leave->id }}', 'reject')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted">Tidak ada data cuti</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $leaveRequests->withQueryString()->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmAction(formId, action) {
    const isApprove = action === 'approve';
    Swal.fire({
        title: isApprove ? 'Setujui Cuti?' : 'Tolak Cuti?',
        text: isApprove ? 'Pengajuan cuti ini akan disetujui.' : 'Pengajuan cuti ini akan ditolak.',
        icon: isApprove ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: isApprove ? '#1cc88a' : '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: isApprove ? 'Ya, Setujui!' : 'Ya, Tolak!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}
</script>
@endpush
