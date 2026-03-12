@extends('layouts.admin')
@section('title', 'Beri Penilaian Karyawan')

@push('styles')
<style>
.star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 4px; }
.star-rating input { display: none; }
.star-rating label { font-size: 2rem; color: #ddd; cursor: pointer; transition: color .15s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color: #f6c23e; }
.score-display { font-weight: bold; color: #4e73df; margin-left: 10px; }
.category-block { border: 1px solid #e3e6f0; border-radius: 8px; padding: 16px; margin-bottom: 16px; background: #fff; }
.category-block:hover { border-color: #4e73df; box-shadow: 0 2px 8px rgba(78,115,223,.15); }
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Beri Penilaian Karyawan</h1>
    <a href="{{ route('admin.assessments.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
    </a>
</div>

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form action="{{ route('admin.assessments.store') }}" method="POST" id="assessment-form">
@csrf
<div class="row">
    {{-- Section 1 – Info & Pilih Karyawan --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user mr-2"></i>Info Penilaian</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Karyawan <span class="text-danger">*</span></label>
                    <select name="evaluatee_id" id="sel-employee" class="form-control" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" data-dept="{{ $emp->department->name ?? '-' }}">
                            {{ $emp->full_name }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted" id="dept-info"></small>
                </div>

                <div class="form-group">
                    <label>Tipe Periode <span class="text-danger">*</span></label>
                    <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                        @foreach(['Bulanan','Mingguan','Harian'] as $pt)
                        <label class="btn btn-outline-primary btn-sm flex-fill {{ $pt === 'Bulanan' ? 'active' : '' }}">
                            <input type="radio" name="period_type" value="{{ $pt }}" {{ $pt === 'Bulanan' ? 'checked' : '' }}>
                            {{ $pt }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label>Tanggal Penilaian <span class="text-danger">*</span></label>
                    <input type="date" name="assessment_date" id="assessment-date" class="form-control"
                           value="{{ now()->format('Y-m-d') }}" required>
                    <div class="mt-2 p-2 bg-light rounded small text-primary" id="period-label-preview">
                        <i class="fas fa-calendar mr-1"></i> <span id="period-label-text">{{ now()->isoFormat('MMMM Y') }}</span>
                    </div>
                    <input type="hidden" name="period_label" id="period-label-input" value="">
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="show-to-employee" name="show_to_employee" value="1" checked>
                        <label class="custom-control-label" for="show-to-employee">Tampilkan hasil ke karyawan</label>
                    </div>
                </div>

                {{-- Duplikat warning --}}
                <div id="duplicate-warning" class="alert alert-warning d-none">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Karyawan ini sudah dinilai untuk periode <strong id="dup-label"></strong>.
                    <a href="#" id="dup-edit-link" class="alert-link">Edit penilaian yang ada?</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2 – Star Rating per Kategori --}}
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-star mr-2"></i>Penilaian per Kategori</h6>
            </div>
            <div class="card-body">
                <div id="star-validation-error" class="alert alert-danger d-none">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Semua kategori wajib diberi nilai (minimal 1 bintang)!
                </div>

                @foreach($categories as $cat)
                <div class="category-block" data-category="{{ $cat->id }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>{{ $cat->name }}</strong>
                            @if($cat->description)
                            <br><small class="text-muted">{{ $cat->description }}</small>
                            @endif
                        </div>
                        <span class="score-display" id="score-val-{{ $cat->id }}">— / 5</span>
                    </div>
                    <div class="star-rating" id="stars-{{ $cat->id }}">
                        @for($s = 5; $s >= 1; $s--)
                        <input type="radio" id="star-{{ $cat->id }}-{{ $s }}" name="scores[{{ $cat->id }}]" value="{{ $s }}">
                        <label for="star-{{ $cat->id }}-{{ $s }}" title="{{ $s }} bintang"><i class="fas fa-star"></i></label>
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Section 3 – Catatan Umum --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-comment mr-2"></i>Catatan / Feedback Umum</h6>
            </div>
            <div class="card-body">
                <textarea name="general_notes" class="form-control" rows="4"
                          placeholder="Tuliskan catatan evaluasi umum...">{{ old('general_notes') }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2" style="gap: 10px;">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save mr-1"></i> Simpan Penilaian
            </button>
            <button type="submit" name="save_next" value="1" class="btn btn-success btn-lg">
                <i class="fas fa-forward mr-1"></i> Simpan & Nilai Berikutnya
            </button>
            <a href="{{ route('admin.assessments.index') }}" class="btn btn-secondary btn-lg">Batal</a>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    var CSRF = $('meta[name=csrf-token]').attr('content');

    // Show dept info
    $('#sel-employee').change(function () {
        var opt = $(this).find(':selected');
        $('#dept-info').text(opt.val() ? '🏢 ' + opt.data('dept') : '');
        checkDuplicate();
    });

    // Update period label preview
    function updatePeriodLabel() {
        var date = new Date($('#assessment-date').val());
        if (isNaN(date)) return;
        var pt   = $('input[name=period_type]:checked').val();
        var label = '';
        var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

        if (pt === 'Harian') {
            label = days[date.getDay()] + ', ' + date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
        } else if (pt === 'Mingguan') {
            var weekNum = Math.ceil(date.getDate() / 7);
            label = 'Minggu ' + weekNum + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
        } else {
            label = months[date.getMonth()] + ' ' + date.getFullYear();
        }

        $('#period-label-text').text(label);
        $('#period-label-input').val(label);
        checkDuplicate();
    }

    $('input[name=period_type], #assessment-date').on('change', updatePeriodLabel);
    updatePeriodLabel();

    // Star rating – update display
    $(document).on('change', 'input[name^="scores["]', function () {
        var id  = $(this).attr('name').match(/\d+/)[0];
        var val = $(this).val();
        $('#score-val-' + id).text(val + ' / 5').css('color', '#f6c23e');
    });

    // Check duplikat via AJAX
    var dupTimer;
    function checkDuplicate() {
        clearTimeout(dupTimer);
        var empId = $('#sel-employee').val();
        var pt    = $('input[name=period_type]:checked').val();
        var label = $('#period-label-input').val();
        if (!empId || !pt || !label) { $('#duplicate-warning').addClass('d-none'); return; }

        dupTimer = setTimeout(function () {
            $.ajax({
                url: '{{ route('admin.assessments.store') }}',
                method: 'POST',
                data: { _token: CSRF, evaluatee_id: empId, period_type: pt, period_label: label,
                        assessment_date: $('#assessment-date').val(), check_only: 1, scores: {} },
                success: function (r) {
                    if (r.duplicate) {
                        $('#dup-label').text(r.period_label);
                        $('#dup-edit-link').attr('href', '/admin/assessments/' + r.assessment_id + '/edit');
                        $('#duplicate-warning').removeClass('d-none');
                    } else {
                        $('#duplicate-warning').addClass('d-none');
                    }
                },
                error: function () { $('#duplicate-warning').addClass('d-none'); }
            });
        }, 600);
    }

    // Form validation
    $('#assessment-form').submit(function (e) {
        var allFilled = true;
        @foreach($categories as $cat)
        if (!$('input[name="scores[{{ $cat->id }}]"]:checked').val()) allFilled = false;
        @endforeach

        if (!allFilled) {
            e.preventDefault();
            $('#star-validation-error').removeClass('d-none');
            $('html, body').animate({ scrollTop: 0 }, 400);
            Swal.fire({ icon: 'error', title: 'Penilaian Belum Lengkap', text: 'Semua kategori wajib diberi nilai (minimal 1 bintang)!' });
        }
    });
});
</script>
@endpush
