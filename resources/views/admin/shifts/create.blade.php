@extends('layouts.admin')
@section('title', 'Tambah Shift')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Shift</h1>
    <a href="{{ route('admin.shifts.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.shifts.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nama Shift <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Pagi, Siang, Malam" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="start_time">Jam Masuk <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="end_time">Jam Keluar <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="is_cross_day">Cross Day <span class="text-danger">*</span></label>
                        <select name="is_cross_day" id="is_cross_day" class="form-control @error('is_cross_day') is-invalid @enderror" required>
                            <option value="0" {{ old('is_cross_day', '0') == '0' ? 'selected' : '' }}>Tidak</option>
                            <option value="1" {{ old('is_cross_day') == '1' ? 'selected' : '' }}>Ya (Shift Malam)</option>
                        </select>
                        @error('is_cross_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">Pilih "Ya" jika shift melewati tengah malam</small>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        </form>
    </div>
</div>
@endsection
