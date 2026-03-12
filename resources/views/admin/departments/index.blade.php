@extends('layouts.admin')
@section('title', 'Manajemen Department')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Department</h1>
    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Department
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover dataTable" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama Department</th>
                        <th>Lokasi Kantor</th>
                        <th>Jumlah Karyawan</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $i => $dept)
                    <tr>
                        <td>{{ $departments->firstItem() + $i }}</td>
                        <td>{{ $dept->name }}</td>
                        <td>{{ $dept->officeLocation->name ?? '-' }}</td>
                        <td><span class="badge badge-primary">{{ $dept->employees_count ?? $dept->employees->count() }}</span></td>
                        <td>
                            <a href="{{ route('admin.departments.edit', $dept) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.departments.destroy', $dept) }}" method="POST" class="d-inline" id="delete-form-{{ $dept->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('delete-form-{{ $dept->id }}')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $departments->links() }}</div>
    </div>
</div>
@endsection
