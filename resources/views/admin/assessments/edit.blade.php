@extends('layouts.admin')
@section('title', 'Edit Penilaian')

@push('styles')
<style>
.star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 4px; }
.star-rating input { display: none; }
.star-rating label { font-size: 2rem; color: #ddd; cursor: pointer; transition: color .15s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #f6c23e; }
.score-display { font-weight: bold; color: #f6c23e; margin-left: 10px; }
.category-block { border: 1px solid #e3e6f0; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Penilaian</h1>
    <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<form action="{{ route('admin.assessments.update', $assessment) }}" method="POST" id="edit-form">
@csrf @method('PUT')
<div class="row">
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Info Penilaian</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Karyawan</label>
                    <input type="text" class="form-control" value="{{ $assessment->evaluatee->full_name ?? '-' }}" readonly>
                </div>
                <div class="form-group">
                    <label>Periode</label>
                    <input type="text" class="form-control" value="{{ $assessment->period_type }} – {{ $assessment->period_label }}" readonly>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="show-emp" name="show_to_employee" value="1"
                               {{ $assessment->show_to_employee ? 'checked' : '' }}>
                        <label class="custom-control-label" for="show-emp">Tampilkan ke Karyawan</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Catatan Umum</label>
                    <textarea name="general_notes" class="form-control" rows="4">{{ old('general_notes', $assessment->general_notes) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-star mr-1"></i> Ubah Nilai per Kategori</h6>
            </div>
            <div class="card-body">
                @foreach($categories as $cat)
                @php $existing = $existingScores->get($cat->id); $currentScore = old("scores.{$cat->id}", $existing?->score ?? 0); @endphp
                <div class="category-block">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>{{ $cat->name }}</strong>
                            @if($cat->description)<br><small class="text-muted">{{ $cat->description }}</small>@endif
                        </div>
                        <span class="score-display" id="score-val-{{ $cat->id }}">{{ $currentScore > 0 ? $currentScore . ' / 5' : '— / 5' }}</span>
                    </div>
                    <div class="star-rating" id="stars-{{ $cat->id }}">
                        @for($s = 5; $s >= 1; $s--)
                        <input type="radio" id="star-{{ $cat->id }}-{{ $s }}" name="scores[{{ $cat->id }}]" value="{{ $s }}"
                               {{ $currentScore == $s ? 'checked' : '' }}>
                        <label for="star-{{ $cat->id }}-{{ $s }}" title="{{ $s }} bintang"><i class="fas fa-star"></i></label>
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg mb-4">
            <i class="fas fa-save mr-1"></i> Simpan Perubahan
        </button>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
$(document).on('change', 'input[name^="scores["]', function () {
    var id = $(this).attr('name').match(/\d+/)[0];
    $('#score-val-' + id).text($(this).val() + ' / 5');
});
</script>
@endpush
