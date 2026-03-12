@extends('layouts.admin')
@section('title', 'Tambah Karyawan')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Karyawan</h1>
    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

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
                        <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code') }}" required>
                        @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
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
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
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
                                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                            @endforeach
                        </select>
                        @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="is_active" class="form-control" required>
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
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
                        <div class="custom-file">
                            <input type="file" name="photo" class="custom-file-input @error('photo') is-invalid @enderror" id="photoInput" accept=".jpg,.jpeg,.png" onchange="previewPhoto(this)">
                            <label class="custom-file-label" for="photoInput">Pilih foto...</label>
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
                        <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" maxlength="16" pattern="\d{16}" placeholder="3201234567890123">
                        @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="birth_place" class="form-control @error('birth_place') is-invalid @enderror" value="{{ old('birth_place') }}">
                        @error('birth_place')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}">
                        @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Pendidikan Terakhir</label>
                        <select name="last_education" class="form-control @error('last_education') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach(['SD','SMP','SMA/SMK','D1','D2','D3','S1','S2','S3'] as $edu)
                                <option value="{{ $edu }}" {{ old('last_education') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
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
                        <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror" value="{{ old('join_date') }}">
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
            <div class="custom-control custom-switch mb-3">
                <input type="checkbox" class="custom-control-input" id="createAccount" name="create_account" value="1" {{ old('create_account') ? 'checked' : '' }} onchange="toggleAccountFields()">
                <label class="custom-control-label" for="createAccount">Sekaligus buatkan akun aplikasi</label>
            </div>
            <div id="accountFields" style="{{ old('create_account') ? '' : 'display:none;' }}">
                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="account_email" class="form-control @error('account_email') is-invalid @enderror" value="{{ old('account_email') }}" placeholder="email@contoh.com">
                    @error('account_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-1"></i>
                    Password default: <strong>ganti123</strong>
                    <br><small>Karyawan wajib ganti password saat pertama kali login.</small>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mb-4">
        <i class="fas fa-save"></i> Simpan Karyawan
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

function toggleAccountFields() {
    const checked = document.getElementById('createAccount').checked;
    document.getElementById('accountFields').style.display = checked ? '' : 'none';
}
</script>
@endpush
