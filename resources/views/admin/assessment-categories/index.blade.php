@extends('layouts.admin')
@section('title', 'Indikator Penilaian')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Indikator Penilaian</h1>
    <a href="{{ route('admin.assessment-categories.create') }}" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm mr-1"></i> Tambah Indikator Baru
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tbl-kategori" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th width="10%">Type</th>
                        <th width="10%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $cat)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $cat->name }}</strong></td>
                        <td class="text-muted small">{{ Str::limit($cat->description, 70) ?: '—' }}</td>
                        <td><span class="badge badge-secondary">{{ $cat->type }}</span></td>
                        <td class="text-center">
                            @if($cat->is_active)
                                <span class="badge badge-success px-3">Aktif</span>
                            @else
                                <span class="badge badge-secondary px-3">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- Edit --}}
                            <a href="{{ route('admin.assessment-categories.edit', $cat) }}"
                               class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Toggle Aktif/Nonaktif --}}
                            <form action="{{ route('admin.assessment-categories.toggle-active', $cat) }}"
                                  method="POST" class="d-inline" id="toggle-{{ $cat->id }}">
                                @csrf @method('PATCH')
                                <button type="button"
                                    class="btn btn-{{ $cat->is_active ? 'secondary' : 'success' }} btn-sm"
                                    title="{{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                    onclick="confirmToggle('toggle-{{ $cat->id }}', '{{ $cat->name }}', {{ $cat->is_active ? 'true' : 'false' }})">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>

                            {{-- Hapus (hard delete hanya jika belum punya assessment) --}}
                            @if($cat->details->isEmpty())
                            <form action="{{ route('admin.assessment-categories.destroy', $cat) }}"
                                  method="POST" class="d-inline" id="del-{{ $cat->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm" title="Hapus"
                                    onclick="confirmDelete('del-{{ $cat->id }}', '{{ $cat->name }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            <button class="btn btn-danger btn-sm" disabled title="Dipakai di penilaian – tidak bisa dihapus">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Belum ada kategori penilaian.
                            <a href="{{ route('admin.assessment-categories.create') }}">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#tbl-kategori').DataTable({
        destroy: true,
        order: [[1, 'asc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
    });
});

function confirmToggle(formId, name, isActive) {
    Swal.fire({
        title: isActive ? 'Nonaktifkan kategori?' : 'Aktifkan kategori?',
        text: '"' + name + '"',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
    }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
}

function confirmDelete(formId, name) {
    Swal.fire({
        title: 'Hapus kategori?',
        text: '"' + name + '" akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
}
</script>
@endpush
