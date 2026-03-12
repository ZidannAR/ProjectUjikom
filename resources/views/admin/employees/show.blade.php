@extends('layouts.admin')
@section('title', 'Detail Karyawan - ' . $employee->full_name)

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Karyawan</h1>
    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <!-- Data Dasar -->
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Dasar</h6>
                @if($employee->isProfileComplete())
                    <span class="badge badge-success px-2 py-1">Profil Lengkap ✅</span>
                @else
                    <span class="badge badge-warning px-2 py-1">Profil Belum Lengkap ⚠️</span>
                @endif
            </div>
            <div class="card-body text-center">
                @php $detail = $employee->employeeDetail; @endphp

                {{-- Foto --}}
                @if($detail && $detail->photo)
                    <img src="{{ asset('storage/' . $detail->photo) }}" alt="Foto" class="rounded-circle mb-3"
                         style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #4e73df;">
                @else
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width: 120px; height: 120px; background: #4e73df; color: white; font-size: 36px; font-weight: bold;">
                        {{ strtoupper(substr($employee->full_name, 0, 2)) }}
                    </div>
                @endif

                <h5 class="font-weight-bold text-gray-800">{{ $employee->full_name }}</h5>
                <p class="text-muted mb-3"><code>{{ $employee->employee_code }}</code></p>

                <table class="table table-borderless table-sm text-left">
                    <tr>
                        <td class="font-weight-bold text-gray-600">Department</td>
                        <td>{{ $employee->department->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-600">Shift</td>
                        <td>{{ $employee->shift->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-600">Status</td>
                        <td>
                            <span class="badge badge-{{ $employee->is_active ? 'success' : 'danger' }}">
                                {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-600">Device</td>
                        <td>
                            @if($employee->registered_device_id)
                                <span class="badge badge-info">Terdaftar</span>
                            @else
                                <span class="badge badge-secondary">Belum</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Data Lengkap -->
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Lengkap (Diisi Karyawan)</h6>
            </div>
            <div class="card-body">
                @if($detail)
                <table class="table table-borderless">
                    <tr>
                        <td width="35%" class="font-weight-bold text-gray-800">NIK</td>
                        <td>
                            @if($detail->nik)
                                {{ substr($detail->nik, 0, 4) }}••••••••{{ substr($detail->nik, -4) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Tempat, Tgl Lahir</td>
                        <td>
                            {{ $detail->birth_place ?? '-' }},
                            {{ $detail->birth_date?->format('d F Y') ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Jenis Kelamin</td>
                        <td>{{ $detail->gender ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">No. Telepon</td>
                        <td>{{ $detail->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Alamat</td>
                        <td>{{ $detail->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Pendidikan Terakhir</td>
                        <td>{{ $detail->last_education ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-gray-800">Tanggal Mulai Kerja</td>
                        <td>{{ $detail->join_date?->format('d F Y') ?? '-' }}</td>
                    </tr>
                </table>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-user-slash text-muted" style="font-size: 48px;"></i>
                    <p class="mt-3 text-muted font-weight-bold">Karyawan belum mengisi data lengkap</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
