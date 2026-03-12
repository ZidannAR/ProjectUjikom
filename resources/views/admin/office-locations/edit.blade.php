@extends('layouts.admin')
@section('title', 'Edit Lokasi Kantor')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
    #map { height: 450px; width: 100%; border-radius: 6px; border: 2px solid #d1d3e2; z-index: 1; }
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Lokasi: {{ $office_location->name }}</h1>
    <a href="{{ route('admin.office-locations.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.office-locations.update', $office_location) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label for="name">Nama Lokasi <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $office_location->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="radius_meter">Radius (meter) <span class="text-danger">*</span></label>
                <input type="number" name="radius_meter" id="radius_meter" class="form-control @error('radius_meter') is-invalid @enderror" value="{{ old('radius_meter', $office_location->radius_meter) }}" min="1" required>
                @error('radius_meter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="form-text text-muted">Radius geofencing dalam meter. Lingkaran biru di peta akan menunjukkan area radius.</small>
            </div>

            <!-- Peta Leaflet -->
            <div class="form-group">
                <label><i class="fas fa-map-marked-alt text-primary"></i> Lokasi di Peta</label>
                <small class="form-text text-muted mb-2 d-block">Klik pada peta, drag marker, atau gunakan kotak pencarian untuk mengubah koordinat.</small>
                <div id="map"></div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="latitude">Latitude <span class="text-danger">*</span></label>
                        <input type="text" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $office_location->latitude) }}" required>
                        @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="longitude">Longitude <span class="text-danger">*</span></label>
                        <input type="text" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $office_location->longitude) }}" required>
                        @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Koordinat tersimpan
    const savedLat = {{ old('latitude', $office_location->latitude) }};
    const savedLng = {{ old('longitude', $office_location->longitude) }};
    const savedRadius = {{ old('radius_meter', $office_location->radius_meter) }};

    const map = L.map('map').setView([savedLat, savedLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    let marker = null;
    let circle = null;

    function getRadius() {
        return parseInt(document.getElementById('radius_meter').value) || 100;
    }

    function updateMarker(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                updateMarker(pos.lat, pos.lng);
            });
        }

        if (circle) {
            circle.setLatLng([lat, lng]);
            circle.setRadius(getRadius());
        } else {
            circle = L.circle([lat, lng], {
                radius: getRadius(),
                color: '#4e73df',
                fillColor: '#4e73df',
                fillOpacity: 0.15,
                weight: 2,
            }).addTo(map);
        }
    }

    // Langsung tampilkan marker & circle dari data tersimpan
    updateMarker(savedLat, savedLng);

    // Klik peta → pindahkan marker
    map.on('click', function (e) {
        updateMarker(e.latlng.lat, e.latlng.lng);
        map.setView(e.latlng, Math.max(map.getZoom(), 15));
    });

    // Search geocoder
    L.Control.geocoder({
        defaultMarkType: 'L.marker',
        placeholder: 'Cari alamat atau kota...',
        errorMessage: 'Lokasi tidak ditemukan.',
        collapsed: false,
    }).on('markgeocode', function (e) {
        const latlng = e.geocode.center;
        updateMarker(latlng.lat, latlng.lng);
        map.setView(latlng, 16);
    }).addTo(map);

    // Radius berubah → update circle
    document.getElementById('radius_meter').addEventListener('input', function () {
        if (circle) {
            circle.setRadius(getRadius());
        }
    });

    // Input manual lat/lng → update marker
    function onManualInput() {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            updateMarker(lat, lng);
            map.setView([lat, lng], Math.max(map.getZoom(), 15));
        }
    }
    document.getElementById('latitude').addEventListener('change', onManualInput);
    document.getElementById('longitude').addEventListener('change', onManualInput);
});
</script>
@endpush
