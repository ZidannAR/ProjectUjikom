@extends('layouts.admin')
@section('title', 'Manajemen Lokasi Kantor')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .mini-map {
        width: 200px;
        height: 150px;
        border-radius: 6px;
        border: 1px solid #d1d3e2;
        z-index: 1;
    }
    .address-cell {
        font-size: 0.85rem;
        color: #5a5c69;
        max-width: 280px;
    }
    .address-loading {
        color: #b7b9cc;
        font-style: italic;
    }
    .address-error {
        color: #e74a3b;
        font-style: italic;
    }
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Lokasi Kantor</h1>
    <a href="{{ route('admin.office-locations.create') }}" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Lokasi
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th width="4%">#</th>
                        <th>Nama Lokasi</th>
                        <th width="220px">Mini Map</th>
                        <th>Alamat Lengkap</th>
                        <th width="100px">Radius</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $i => $loc)
                    <tr>
                        <td>{{ $locations->firstItem() + $i }}</td>
                        <td class="font-weight-bold">{{ $loc->name }}</td>
                        <td>
                            <div id="map-{{ $loc->id }}" class="mini-map"
                                 data-lat="{{ $loc->latitude }}"
                                 data-lng="{{ $loc->longitude }}"
                                 data-radius="{{ $loc->radius_meter }}">
                            </div>
                        </td>
                        <td class="address-cell">
                            <span id="address-{{ $loc->id }}" class="address-loading">
                                <i class="fas fa-spinner fa-spin fa-sm"></i> Memuat alamat...
                            </span>
                        </td>
                        <td><span class="badge badge-info">{{ $loc->radius_meter }} m</span></td>
                        <td>
                            <a href="{{ route('admin.office-locations.edit', $loc) }}" class="btn btn-warning btn-sm mb-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.office-locations.destroy', $loc) }}" method="POST" class="d-inline" id="delete-form-{{ $loc->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm mb-1" title="Hapus" onclick="confirmDelete('delete-form-{{ $loc->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">Tidak ada data lokasi kantor</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $locations->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi semua mini maps
    const mapElements = document.querySelectorAll('.mini-map');

    mapElements.forEach(function (el) {
        const lat = parseFloat(el.dataset.lat);
        const lng = parseFloat(el.dataset.lng);
        const radius = parseInt(el.dataset.radius) || 100;

        if (isNaN(lat) || isNaN(lng)) return;

        const miniMap = L.map(el.id, {
            scrollWheelZoom: false,
            dragging: false,
            zoomControl: false,
            doubleClickZoom: false,
            boxZoom: false,
            keyboard: false,
            touchZoom: false,
            attributionControl: false,
        }).setView([lat, lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(miniMap);

        L.marker([lat, lng]).addTo(miniMap);

        L.circle([lat, lng], {
            radius: radius,
            color: '#4e73df',
            fillColor: '#4e73df',
            fillOpacity: 0.15,
            weight: 2,
        }).addTo(miniMap);

        // Fit bounds untuk radius besar
        if (radius > 200) {
            const circleBounds = L.circle([lat, lng], { radius: radius }).getBounds();
            miniMap.fitBounds(circleBounds.pad(0.3));
        }
    });

    // Reverse geocode alamat dengan delay 500ms antar request
    const locArray = @json($locations->items());

    locArray.forEach(function (loc, index) {
        setTimeout(function () {
            const addressEl = document.getElementById('address-' + loc.id);
            if (!addressEl) return;

            fetch('https://nominatim.openstreetmap.org/reverse?lat=' + loc.latitude + '&lon=' + loc.longitude + '&format=json&accept-language=id', {
                headers: { 'User-Agent': 'AbsensiApp/1.0' }
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data && data.display_name) {
                    addressEl.innerHTML = '<i class="fas fa-map-pin text-primary fa-sm mr-1"></i>' + data.display_name;
                    addressEl.classList.remove('address-loading');
                } else {
                    addressEl.textContent = 'Alamat tidak ditemukan';
                    addressEl.classList.remove('address-loading');
                    addressEl.classList.add('address-error');
                }
            })
            .catch(function () {
                addressEl.textContent = 'Alamat tidak ditemukan';
                addressEl.classList.remove('address-loading');
                addressEl.classList.add('address-error');
            });
        }, index * 500); // 500ms delay antar request
    });
});
</script>
@endpush
