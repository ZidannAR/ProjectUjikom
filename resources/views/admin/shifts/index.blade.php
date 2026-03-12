@extends('layouts.admin')
@section('title', 'Manajemen Shift')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Shift</h1>
    <a href="{{ route('admin.shifts.create') }}" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Shift
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover dataTable" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama Shift</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Cross Day</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $i => $shift)
                    <tr>
                        <td>{{ $shifts->firstItem() + $i }}</td>
                        <td>{{ $shift->name }}</td>
                        <td>{{ $shift->start_time }}</td>
                        <td>{{ $shift->end_time }}</td>
                        <td>
                            @if($shift->is_cross_day)
                                <span class="badge badge-warning"><i class="fas fa-moon"></i> Cross Day</span>
                            @else
                                <span class="badge badge-light">Normal</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.shifts.edit', $shift) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.shifts.destroy', $shift) }}" method="POST" class="d-inline" id="delete-form-{{ $shift->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('delete-form-{{ $shift->id }}')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $shifts->links() }}</div>
    </div>
</div>
@endsection
