@extends('layouts.admin')
@section('title', 'Manajemen Akun Karyawan')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Akun Karyawan</h1>
    @if($employeesWithoutAccount->count() > 0)
    <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#createAccountModal">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Akun Baru
    </button>
    @endif
</div>

<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" class="row align-items-end">
            <div class="col-md-3">
                <label class="small font-weight-bold">Status Akun</label>
                <select name="is_active" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small font-weight-bold">Department</label>
                <select name="department_id" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-sync"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Karyawan</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Status Akun</th>
                        <th>Password</th>
                        <th>Dibuat</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                    <tr>
                        <td class="font-weight-bold">{{ $account->employee->full_name ?? '-' }}</td>
                        <td>{{ $account->email }}</td>
                        <td>{{ $account->employee->department->name ?? '-' }}</td>
                        <td>
                            @if($account->is_active)
                                <span class="badge badge-success px-2 py-1">Aktif</span>
                            @else
                                <span class="badge badge-danger px-2 py-1">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            @if($account->must_change_password)
                                <span class="badge badge-warning px-2 py-1">Perlu Ganti</span>
                            @else
                                <span class="badge badge-success px-2 py-1">Normal</span>
                            @endif
                        </td>
                        <td>{{ $account->created_at->format('d/m/Y') }}</td>
                        <td>
                            <form action="{{ route('admin.accounts.reset-password', $account) }}" method="POST" class="d-inline" id="reset-pw-{{ $account->id }}">
                                @csrf
                                <button type="button" class="btn btn-warning btn-sm" onclick="confirmReset({{ $account->id }}, '{{ $account->name }}')">
                                    <i class="fas fa-key"></i> Reset
                                </button>
                            </form>
                            <form action="{{ route('admin.accounts.toggle-active', $account) }}" method="POST" class="d-inline" id="toggle-{{ $account->id }}">
                                @csrf @method('PATCH')
                                @if($account->is_active)
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmToggle({{ $account->id }}, '{{ $account->name }}', 'nonaktifkan')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success btn-sm" onclick="confirmToggle({{ $account->id }}, '{{ $account->name }}', 'aktifkan')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada akun karyawan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $accounts->appends(request()->query())->links() }}
    </div>
</div>

<!-- Modal Create Account -->
<div class="modal fade" id="createAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.accounts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Tambah Akun Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Pilih Karyawan <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-control" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($employeesWithoutAccount as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hanya karyawan yang belum punya akun</small>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="email@contoh.com">
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        Password default: <strong>ganti123</strong><br>
                        <small>Karyawan wajib ganti password saat pertama kali login.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmReset(id, name) {
    Swal.fire({
        title: 'Reset Password?',
        html: `Password <strong>${name}</strong> akan direset ke <strong>ganti123</strong>.<br>Karyawan akan wajib ganti password saat login berikutnya.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f6c23e',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('reset-pw-' + id).submit();
        }
    });
}

function confirmToggle(id, name, action) {
    Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} Akun?`,
        html: `Akun <strong>${name}</strong> akan di-<strong>${action}</strong>.${action === 'nonaktifkan' ? '<br><small class="text-danger">Karyawan akan otomatis logout dan tidak bisa login.</small>' : ''}`,
        icon: action === 'nonaktifkan' ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: action === 'nonaktifkan' ? '#e74a3b' : '#1cc88a',
        cancelButtonColor: '#858796',
        confirmButtonText: `Ya, ${action.charAt(0).toUpperCase() + action.slice(1)}!`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('toggle-' + id).submit();
        }
    });
}

@if($errors->any())
    $('#createAccountModal').modal('show');
@endif
</script>
@endpush
