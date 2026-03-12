@extends('layouts.admin')
@section('title', 'Manajemen Karyawan')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Karyawan</h1>
    <div>
        <a href="{{ route('admin.employees.export-excel') }}" class="btn btn-success btn-sm shadow-sm mr-2">
            <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
        </a>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Karyawan
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.employees.index') }}">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <select name="department_id" class="form-control form-control-sm">
                        <option value="">-- Semua Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select name="shift_id" class="form-control form-control-sm">
                        <option value="">-- Semua Shift --</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select name="is_active" class="form-control form-control-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-sync"></i> Reset</a>
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
                        <th width="5%">#</th>
                        <th>Kode</th>
                        <th>Nama Lengkap</th>
                        <th>Department</th>
                        <th>Shift</th>
                        <th>Device</th>
                        <th>Status</th>
                        <th>Profil</th>
                        <th width="18%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $i => $emp)
                    <tr>
                        <td>{{ $employees->firstItem() + $i }}</td>
                        <td><code>{{ $emp->employee_code }}</code></td>
                        <td>{{ $emp->full_name }}</td>
                        <td>{{ $emp->department->name ?? '-' }}</td>
                        <td>{{ $emp->shift->name ?? '-' }}</td>
                        <td>
                            @if($emp->registered_device_id)
                                <span class="badge badge-info" title="{{ $emp->registered_device_id }}">Terdaftar</span>
                            @else
                                <span class="badge badge-secondary">Belum</span>
                            @endif
                        </td>
                        <td>
                            @if($emp->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            @if($emp->isProfileComplete())
                                <span class="badge badge-success">Lengkap</span>
                            @else
                                <span class="badge badge-warning">Belum</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.employees.show', $emp) }}" class="btn btn-info btn-sm" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.employees.edit', $emp) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($emp->registered_device_id)
                            <form action="{{ route('admin.employees.reset-device', $emp) }}" method="POST" class="d-inline" id="reset-form-{{ $emp->id }}">
                                @csrf
                                <button type="button" class="btn btn-info btn-sm" title="Reset Device" onclick="confirmReset({{ $emp->id }}, '{{ $emp->full_name }}')">
                                    <i class="fas fa-mobile-alt"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.employees.destroy', $emp) }}" method="POST" class="d-inline" id="delete-form-{{ $emp->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm" title="Hapus" onclick="confirmDelete('delete-form-{{ $emp->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Tidak ada data karyawan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $employees->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmReset(id, name) {
    Swal.fire({
        title: 'Reset Device?',
        text: 'Device untuk ' + name + ' akan direset. Karyawan harus scan ulang dengan HP baru.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#36b9cc',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('reset-form-' + id).submit();
        }
    });
}
</script>
@endpush
