@extends('layouts.app')

@section('title', 'Rapor Düzenle')

@push('styles')
<style>
.parametre-row { margin-bottom: 1rem; padding: 1rem; background: var(--gray-50); border-radius: var(--border-radius-sm); border: 1px solid var(--gray-200); }
.parametre-row .form-label { margin-bottom: 0.25rem; font-size: 0.875rem; }
.btn-remove-param { color: var(--danger); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="bi bi-pencil"></i> Rapor Düzenle</h1>
        <a href="{{ route('admin.laboratuvar.show', $rapor->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Geri
        </a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <strong><i class="bi bi-exclamation-triangle"></i> Hatalar:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="content-card">
    <div class="content-card-body">
        <form method="POST" action="{{ route('admin.laboratuvar.update', $rapor->id) }}" enctype="multipart/form-data" id="raporForm">
            @csrf
            @method('PUT')
            
            <!-- Rapor Bilgileri -->
            <h5 class="mb-3"><i class="bi bi-info-circle"></i> Rapor Bilgileri</h5>
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Rapor No <span class="text-danger">*</span></label>
                    <input type="text" name="rapor_no" class="form-control" value="{{ old('rapor_no', $rapor->rapor_no) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Rapor Tarihi <span class="text-danger">*</span></label>
                    <input type="date" name="rapor_tarihi" class="form-control" value="{{ old('rapor_tarihi', $rapor->rapor_tarihi) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tesis Adı <span class="text-danger">*</span></label>
                    <input type="text" name="tesis_adi" class="form-control" value="{{ old('tesis_adi', $rapor->tesis_adi) }}" required>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Numune Alma Noktası</label>
                    <input type="text" name="numune_alma_noktasi" class="form-control" value="{{ old('numune_alma_noktasi', $rapor->numune_alma_noktasi) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Numune Alma Tarihi</label>
                    <input type="date" name="numune_alma_tarihi" class="form-control" value="{{ old('numune_alma_tarihi', $rapor->numune_alma_tarihi) }}">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Numune Alma Şekli</label>
                    <input type="text" name="numune_alma_sekli" class="form-control" value="{{ old('numune_alma_sekli', $rapor->numune_alma_sekli) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Numune Geliş Şekli</label>
                    <input type="text" name="numune_gelis_sekli" class="form-control" value="{{ old('numune_gelis_sekli', $rapor->numune_gelis_sekli) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Numune Ambalaj</label>
                    <input type="text" name="numune_ambalaj" class="form-control" value="{{ old('numune_ambalaj', $rapor->numune_ambalaj) }}">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Numune Numarası</label>
                    <input type="text" name="numune_numarasi" class="form-control" value="{{ old('numune_numarasi', $rapor->numune_numarasi) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Lab Geliş Tarihi</label>
                    <input type="datetime-local" name="lab_gelis_tarihi" class="form-control" value="{{ old('lab_gelis_tarihi', $rapor->lab_gelis_tarihi ? \Carbon\Carbon::parse($rapor->lab_gelis_tarihi)->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Şahit Numune</label>
                    <input type="text" name="sahit_numune" class="form-control" value="{{ old('sahit_numune', $rapor->sahit_numune) }}">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Analiz Başlangıç Tarihi</label>
                    <input type="date" name="analiz_baslangic" class="form-control" value="{{ old('analiz_baslangic', $rapor->analiz_baslangic) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Analiz Bitiş Tarihi</label>
                    <input type="date" name="analiz_bitis" class="form-control" value="{{ old('analiz_bitis', $rapor->analiz_bitis) }}">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <label class="form-label">Notlar</label>
                    <textarea name="notlar" class="form-control" rows="3">{{ old('notlar', $rapor->notlar) }}</textarea>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">PDF Dosyası (Varsa)</label>
                    <input type="file" name="pdf_dosya" class="form-control" accept=".pdf">
                    <small class="text-muted">Maksimum 10 MB. Mevcut: {{ $rapor->hasPdf() ? '✓ Var' : '✗ Yok' }}</small>
                </div>
            </div>

            <hr class="my-4">

            <!-- Parametreler -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="bi bi-list-check"></i> Analiz Parametreleri</h5>
                <button type="button" class="btn btn-success btn-sm" onclick="parametreEkle()">
                    <i class="bi bi-plus-circle"></i> Parametre Ekle
                </button>
            </div>

            <div id="parametrelerContainer">
                @foreach($rapor->parametreler as $index => $param)
                <div class="parametre-row" data-index="{{ $index }}">
                    <input type="hidden" name="parametreler[{{ $index }}][id]" value="{{ $param->id }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Parametre #{{ $index + 1 }}</strong>
                        <button type="button" class="btn btn-sm btn-remove-param" onclick="parametreSil(this)">
                            <i class="bi bi-trash"></i> Sil
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Parametre Adı *</label>
                            <input type="text" name="parametreler[{{ $index }}][parametre_adi]" class="form-control form-control-sm" value="{{ $param->parametre_adi }}" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Birim</label>
                            <input type="text" name="parametreler[{{ $index }}][birim]" class="form-control form-control-sm" value="{{ $param->birim }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Sonuç *</label>
                            <input type="number" step="0.0001" name="parametreler[{{ $index }}][analiz_sonucu]" class="form-control form-control-sm" value="{{ $param->analiz_sonucu }}" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Limit Değeri</label>
                            <input type="text" name="parametreler[{{ $index }}][limit_degeri]" class="form-control form-control-sm" value="{{ $param->limit_degeri }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Analiz Metodu</label>
                            <input type="text" name="parametreler[{{ $index }}][analiz_metodu]" class="form-control form-control-sm" value="{{ $param->analiz_metodu }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Tablo No</label>
                            <input type="text" name="parametreler[{{ $index }}][tablo_no]" class="form-control form-control-sm" value="{{ $param->tablo_no }}">
                        </div>
                        <div class="col-md-10 mb-2">
                            <label class="form-label">Notlar</label>
                            <input type="text" name="parametreler[{{ $index }}][notlar]" class="form-control form-control-sm" value="{{ $param->notlar }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Güncelle
                </button>
                <a href="{{ route('admin.laboratuvar.show', $rapor->id) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> İptal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let parametreIndex = {{ $rapor->parametreler->count() }};

function parametreEkle() {
    const container = document.getElementById('parametrelerContainer');
    const html = `
        <div class="parametre-row" data-index="${parametreIndex}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Parametre #${parametreIndex + 1}</strong>
                <button type="button" class="btn btn-sm btn-remove-param" onclick="parametreSil(this)">
                    <i class="bi bi-trash"></i> Sil
                </button>
            </div>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label class="form-label">Parametre Adı *</label>
                    <input type="text" name="parametreler[${parametreIndex}][parametre_adi]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Birim</label>
                    <input type="text" name="parametreler[${parametreIndex}][birim]" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Sonuç *</label>
                    <input type="number" step="0.0001" name="parametreler[${parametreIndex}][analiz_sonucu]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Limit Değeri</label>
                    <input type="text" name="parametreler[${parametreIndex}][limit_degeri]" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Analiz Metodu</label>
                    <input type="text" name="parametreler[${parametreIndex}][analiz_metodu]" class="form-control form-control-sm">
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 mb-2">
                    <label class="form-label">Tablo No</label>
                    <input type="text" name="parametreler[${parametreIndex}][tablo_no]" class="form-control form-control-sm">
                </div>
                <div class="col-md-10 mb-2">
                    <label class="form-label">Notlar</label>
                    <input type="text" name="parametreler[${parametreIndex}][notlar]" class="form-control form-control-sm">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    parametreIndex++;
}

function parametreSil(btn) {
    if (confirm('Bu parametreyi silmek istediğinizden emin misiniz?')) {
        btn.closest('.parametre-row').remove();
    }
}
</script>
@endpush
