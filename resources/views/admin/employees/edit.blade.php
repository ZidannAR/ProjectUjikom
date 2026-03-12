@extends('layouts.admin')
@section('title', 'Edit Karyawan - ' . $employee->full_name)

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Karyawan</h1>
    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

@php $detail = $employee->employeeDetail; @endphp

<form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    {{-- Data Utama --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Utama</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kode Karyawan <span class="text-danger">*</span></label>
                        <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code', $employee->employee_code) }}" required>
                        @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $employee->full_name) }}" required>
                        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Department <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Shift <span class="text-danger">*</span></label>
                        <select name="shift_id" class="form-control @error('shift_id') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                            @endforeach
                        </select>
                        @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="is_active" class="form-control" required>
                            <option value="1" {{ old('is_active', $employee->is_active) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $employee->is_active) == 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Lengkap --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Lengkap Karyawan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Foto</label>
                        @if($detail && $detail->photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $detail->photo) }}" alt="Foto saat ini" class="img-thumbnail" style="max-height: 120px;">
                                <small class="d-block text-muted">Foto saat ini</small>
                            </div>
                        @endif
                        <div class="custom-file">
                            <input type="file" name="photo" class="custom-file-input @error('photo') is-invalid @enderror" id="photoInput" accept=".jpg,.jpeg,.png" onchange="previewPhoto(this)">
                            <label class="custom-file-label" for="photoInput">{{ $detail && $detail->photo ? 'Ganti foto...' : 'Pilih foto...' }}</label>
                            @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">JPG/PNG, maks 2MB</small>
                        <div id="photoPreview" class="mt-2" style="display:none;">
                            <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>NIK (16 digit)</label>
                        <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $detail->nik ?? '') }}" maxlength="16" pattern="\d{16}">
                        @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" {{ old('gender', $detail->gender ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender', $detail->gender ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="birth_place" class="form-control @error('birth_place') is-invalid @enderror" value="{{ old('birth_place', $detail->birth_place ?? '') }}">
                        @error('birth_place')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $detail->birth_date?->format('Y-m-d') ?? '') }}">
                        @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $detail->phone ?? '') }}" placeholder="08xxxxxxxxxx">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $detail->address ?? '') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Pendidikan Terakhir</label>
                        <select name="last_education" class="form-control @error('last_education') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach(['SD','SMP','SMA/SMK','D1','D2','D3','S1','S2','S3'] as $edu)
                                <option value="{{ $edu }}" {{ old('last_education', $detail->last_education ?? '') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                            @endforeach
                        </select>
                        @error('last_education')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Mulai Kerja</label>
                        <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror" value="{{ old('join_date', $detail->join_date?->format('Y-m-d') ?? '') }}">
                        @error('join_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Akun Aplikasi --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Akun Aplikasi</h6>
        </div>
        <div class="card-body">
            @php $account = $employee->user; @endphp
            @if($account)
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="font-weight-bold text-gray-600" width="130">Email</td>
                                <td>{{ $account->email }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Status Akun</td>
                                <td>
                                    <span class="badge badge-{{ $account->is_active ? 'success' : 'danger' }} px-2 py-1">
                                        {{ $account->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Password</td>
                                <td>
                                    @if($account->must_change_password)
                                        <span class="badge badge-warning px-2 py-1">Perlu Ganti Password</span>
                                    @else
                                        <span class="badge badge-success px-2 py-1">Normal</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4 text-right">
                        <form action="{{ route('admin.accounts.reset-password', $account) }}" method="POST" class="d-inline" id="reset-pw-{{ $account->id }}">
                            @csrf
                            <button type="button" class="btn btn-warning btn-sm mb-1" onclick="confirmResetPw({{ $account->id }}, '{{ $account->name }}')">
                                <i class="fas fa-key"></i> Reset Password
                            </button>
                        </form>
                        <br>
                        <form action="{{ route('admin.accounts.toggle-active', $account) }}" method="POST" class="d-inline" id="toggle-{{ $account->id }}">
                            @csrf @method('PATCH')
                            @if($account->is_active)
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmToggleAcc({{ $account->id }}, '{{ $account->name }}', 'nonaktifkan')">
                                    <i class="fas fa-ban"></i> Nonaktifkan
                                </button>
                            @else
                                <button type="button" class="btn btn-success btn-sm" onclick="confirmToggleAcc({{ $account->id }}, '{{ $account->name }}', 'aktifkan')">
                                    <i class="fas fa-check"></i> Aktifkan
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @else
                <div class="text-center py-3">
                    <p class="text-muted mb-2"><i class="fas fa-user-slash"></i> Karyawan ini belum mempunyai akun</p>
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus"></i> Buat Akun di Manajemen Akun
                    </a>
                </div>
            @endif
        </div>
    </div>

    <button type="submit" class="btn btn-primary mb-4">
        <i class="fas fa-save"></i> Simpan Perubahan
    </button>
</form>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('photoPreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
        input.nextElementSibling.textContent = input.files[0].name;
    }
}

function confirmResetPw(id, name) {
    Swal.fire({
        title: 'Reset Password?',
        html: `Password <strong>${name}</strong> akan direset ke <strong>ganti123</strong>.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f6c23e',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal'
    }).then((r) => { if (r.isConfirmed) document.getElementById('reset-pw-' + id).submit(); });
}

function confirmToggleAcc(id, name, action) {
    Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} Akun?`,
        html: `Akun <strong>${name}</strong> akan di-<strong>${action}</strong>.`,
        icon: action === 'nonaktifkan' ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: action === 'nonaktifkan' ? '#e74a3b' : '#1cc88a',
        cancelButtonColor: '#858796',
        confirmButtonText: `Ya!`,
        cancelButtonText: 'Batal'
    }).then((r) => { if (r.isConfirmed) document.getElementById('toggle-' + id).submit(); });
}
</script>
@endpush
