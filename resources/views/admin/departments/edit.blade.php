@extends('layouts.admin')
@section('title', 'Edit Department')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Department: {{ $department->name }}</h1>
    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.departments.update', $department) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label for="name">Nama Department <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $department->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="office_location_id">Lokasi Kantor <span class="text-danger">*</span></label>
                <select name="office_location_id" id="office_location_id" class="form-control @error('office_location_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($officeLocations as $loc)
                        <option value="{{ $loc->id }}" {{ old('office_location_id', $department->office_location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
                @error('office_location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
        </form>
    </div>
</div>
@endsection
