@extends('layouts.admin')
@section('title', 'Edit Indikator Penilaian')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Indikator Penilaian</h1>
    <a href="{{ route('admin.assessment-categories.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-edit mr-2"></i>Edit: {{ $assessmentCategory->name }}
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.assessment-categories.update', $assessmentCategory) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $assessmentCategory->name) }}"
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $assessmentCategory->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">Type</label>
                        <input type="text" name="type" id="type"
                               class="form-control @error('type') is-invalid @enderror"
                               value="{{ old('type', $assessmentCategory->type) }}">
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active"
                                   name="is_active" value="1"
                                   {{ old('is_active', $assessmentCategory->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">
                                Aktif <small class="text-muted">(kategori nonaktif tidak muncul di form penilaian)</small>
                            </label>
                        </div>
                    </div>

                    @if(!$assessmentCategory->details->isEmpty())
                    <div class="alert alert-info small mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Kategori ini sudah digunakan di <strong>{{ $assessmentCategory->details->count() }}</strong> penilaian.
                        Perubahan nama/deskripsi tidak akan mengubah data penilaian lama.
                    </div>
                    @endif

                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        {{-- Hapus (hanya jika belum dipakai) --}}
                        @if($assessmentCategory->details->isEmpty())
                        <form action="{{ route('admin.assessment-categories.destroy', $assessmentCategory) }}"
                              method="POST" id="del-form">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm"
                                onclick="Swal.fire({ title: 'Hapus kategori ini?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#e74a3b', confirmButtonText: 'Ya, Hapus' }).then(r => { if (r.isConfirmed) document.getElementById('del-form').submit(); })">
                                <i class="fas fa-trash mr-1"></i> Hapus Permanen
                            </button>
                        </form>
                        @else
                        <span class="text-muted small"><i class="fas fa-lock mr-1"></i>Tidak dapat dihapus (sudah dipakai)</span>
                        @endif

                        <div class="d-flex" style="gap: 10px;">
                            <a href="{{ route('admin.assessment-categories.index') }}" class="btn btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
