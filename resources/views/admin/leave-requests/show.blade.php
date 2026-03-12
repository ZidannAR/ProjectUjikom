@extends('layouts.admin')
@section('title', 'Detail Cuti - ' . ($leaveRequest->employee->full_name ?? ''))

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Pengajuan Cuti</h1>
    <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <!-- Info Cuti -->
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Cuti</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="35%" class="font-weight-bold text-gray-800">Karyawan</td>
                        <td>{{ $leaveRequest->employee->full_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Department</td>
                        <td>{{ $leaveRequest->employee->department->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Jenis Cuti</td>
                        <td>{{ $leaveRequest->type }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Tanggal Mulai</td>
                        <td>{{ $leaveRequest->start_date?->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Tanggal Selesai</td>
                        <td>{{ $leaveRequest->end_date?->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Durasi</td>
                        <td>{{ $leaveRequest->start_date?->diffInDays($leaveRequest->end_date) + 1 }} hari</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Status</td>
                        <td>
                            @php
                                $sc = ['Pending' => 'warning', 'Approved' => 'success', 'Rejected' => 'danger'][$leaveRequest->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $sc }} px-3 py-2">{{ $leaveRequest->status }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Diajukan</td>
                        <td>{{ $leaveRequest->created_at?->format('d F Y H:i') }}</td>
                    </tr>
                </table>

                @if($leaveRequest->status === 'Pending')
                <hr>
                <div class="d-flex gap-2">
                    <form action="{{ route('admin.leave-requests.approve', $leaveRequest) }}" method="POST" class="d-inline" id="approve-form">
                        @csrf @method('PATCH')
                        <button type="button" class="btn btn-success mr-2" onclick="confirmAction('approve-form', 'approve')">
                            <i class="fas fa-check"></i> Setujui
                        </button>
                    </form>
                    <form action="{{ route('admin.leave-requests.reject', $leaveRequest) }}" method="POST" class="d-inline" id="reject-form">
                        @csrf @method('PATCH')
                        <button type="button" class="btn btn-danger" onclick="confirmAction('reject-form', 'reject')">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bukti Attachment -->
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Bukti Lampiran</h6>
            </div>
            <div class="card-body">
                @if($leaveRequest->attachment)
                    @if($leaveRequest->attachment_note)
                        <p class="text-muted small mb-2">
                            <i class="fas fa-tag"></i> {{ $leaveRequest->attachment_note }}
                        </p>
                    @endif

                    @php
                        $ext = pathinfo($leaveRequest->attachment, PATHINFO_EXTENSION);
                        $url = asset('storage/' . $leaveRequest->attachment);
                    @endphp

                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                        <div class="text-center mb-3">
                            <img src="{{ $url }}" alt="Bukti" class="img-fluid rounded" style="max-height: 400px; cursor: pointer;" onclick="window.open('{{ $url }}', '_blank')">
                        </div>
                    @elseif(strtolower($ext) === 'pdf')
                        <div class="text-center p-4 bg-light rounded mb-3">
                            <i class="fas fa-file-pdf text-danger" style="font-size: 48px;"></i>
                            <p class="mt-2 mb-0 font-weight-bold">Dokumen PDF</p>
                        </div>
                    @endif

                    <a href="{{ $url }}" target="_blank" class="btn btn-primary btn-block">
                        <i class="fas fa-external-link-alt"></i> Buka File
                    </a>
                @else
                    <div class="text-center py-4">
                        @if(\App\Models\LeaveRequest::requiresAttachment($leaveRequest->type))
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 36px;"></i>
                            <p class="mt-2 mb-0 text-warning font-weight-bold">Bukti tidak dilampirkan</p>
                            <p class="small text-muted">Jenis cuti ini seharusnya melampirkan: {{ \App\Models\LeaveRequest::getAttachmentLabel($leaveRequest->type) }}</p>
                        @else
                            <i class="fas fa-minus-circle text-muted" style="font-size: 36px;"></i>
                            <p class="mt-2 mb-0 text-muted">Tidak memerlukan bukti</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
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
