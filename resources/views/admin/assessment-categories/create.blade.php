@extends('layouts.admin')
@section('title', 'Tambah Indikator Penilaian')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Indikator Penilaian</h1>
    <a href="{{ route('admin.assessment-categories.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-plus-circle mr-2"></i>Form Tambah Kategori
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.assessment-categories.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="name">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="cth: Disiplin, Kerja Sama Tim, Inisiatif..."
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Penjelasan singkat tentang kategori ini...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">Type</label>
                        <input type="text" name="type" id="type"
                               class="form-control @error('type') is-invalid @enderror"
                               value="{{ old('type', 'Employee') }}"
                               placeholder="Employee">
                        <small class="text-muted">Biarkan "Employee" untuk kategori karyawan pada umumnya.</small>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active"
                                   name="is_active" value="1"
                                   {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">
                                Aktif <small class="text-muted">(kategori nonaktif tidak muncul di form penilaian)</small>
                            </label>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end" style="gap: 10px;">
                        <a href="{{ route('admin.assessment-categories.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
