@extends('layouts.admin')
@section('title', 'Edit Hari Libur')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Hari Libur: {{ $holiday->name }}</h1>
    <a href="{{ route('admin.holidays.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.holidays.update', $holiday) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label for="holiday_date">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="holiday_date" id="holiday_date" class="form-control @error('holiday_date') is-invalid @enderror" value="{{ old('holiday_date', $holiday->holiday_date?->format('Y-m-d')) }}" required>
                @error('holiday_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="name">Nama Hari Libur <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $holiday->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
        </form>
    </div>
</div>
@endsection
