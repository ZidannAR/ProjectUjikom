@extends('layouts.admin')
@section('title', 'Hari Libur')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Hari Libur</h1>
    <a href="{{ route('admin.holidays.create') }}" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Hari Libur
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover dataTable" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Tanggal</th>
                        <th>Nama Hari Libur</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($holidays as $i => $holiday)
                    <tr>
                        <td>{{ $holidays->firstItem() + $i }}</td>
                        <td>{{ $holiday->holiday_date?->format('d M Y') }}</td>
                        <td>{{ $holiday->name }}</td>
                        <td>
                            <a href="{{ route('admin.holidays.edit', $holiday) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" class="d-inline" id="delete-form-{{ $holiday->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('delete-form-{{ $holiday->id }}')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $holidays->links() }}</div>
    </div>
</div>
@endsection
