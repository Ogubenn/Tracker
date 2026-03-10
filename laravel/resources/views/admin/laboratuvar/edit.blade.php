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
                    <label class="form-label">Rapor No</label>
                    <input type="text" name="rapor_no" class="form-control" value="{{ old('rapor_no', $rapor->rapor_no) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Rapor Tarihi</label>
                    <input type="date" name="rapor_tarihi" class="form-control" value="{{ old('rapor_tarihi', $rapor->rapor_tarihi) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Müşteri Adı</label>
                    <input type="text" name="tesis_adi" class="form-control" value="{{ old('tesis_adi', $rapor->tesis_adi ?: 'BULANCAK BELEDİYESİ SU VE KANALİZASYON İŞLETME MÜDÜRLÜĞÜ') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Teklif Tarihi</label>
                    <input type="date" name="teklif_tarihi" class="form-control" value="{{ old('teklif_tarihi', $rapor->teklif_tarihi ? \Carbon\Carbon::parse($rapor->teklif_tarihi)->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Teklif Numarası</label>
                    <input type="text" name="teklif_no" class="form-control" placeholder="T-79051-2025-03" value="{{ old('teklif_no', $rapor->teklif_no) }}">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Numunenin Cinsi ve Adı</label>
                    <select name="numune_cinsi_adi" class="form-select">
                        <option value="">Seçiniz...</option>
                        <option value="Atıksu Arıtma Tesis Çıkış Numunesi" {{ old('numune_cinsi_adi', $rapor->numune_cinsi_adi) == 'Atıksu Arıtma Tesis Çıkış Numunesi' ? 'selected' : '' }}>Atıksu Arıtma Tesis Çıkış Numunesi</option>
                        <option value="Atıksu Arıtma Tesisi Giriş Numunesi" {{ old('numune_cinsi_adi', $rapor->numune_cinsi_adi) == 'Atıksu Arıtma Tesisi Giriş Numunesi' ? 'selected' : '' }}>Atıksu Arıtma Tesisi Giriş Numunesi</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Numune Alma Noktası ve Sayısı</label>
                    <input type="text" name="numune_alma_noktasi_sayisi" class="form-control" placeholder="Atıksu Arıtma Tesisi Çıkış 1" value="{{ old('numune_alma_noktasi_sayisi', $rapor->numune_alma_noktasi_sayisi ?: 'Atıksu Arıtma Tesisi Çıkış 1') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Numune Alma Başlangıç Tarih ve Saati</label>
                    <input type="datetime-local" name="numune_alma_tarihi" class="form-control" value="{{ old('numune_alma_tarihi', $rapor->numune_alma_tarihi ? \Carbon\Carbon::parse($rapor->numune_alma_tarihi)->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Numune Alma Bitiş Tarih ve Saati</label>
                    <input type="datetime-local" name="numune_alma_tarihi_bitis" class="form-control" value="{{ old('numune_alma_tarihi_bitis', $rapor->numune_alma_tarihi_bitis ? \Carbon\Carbon::parse($rapor->numune_alma_tarihi_bitis)->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Numune Alınış Şekli</label>
                    <select name="numune_alma_sekli" class="form-select">
                        <option value="">Seçiniz...</option>
                        <option value="24 Saatlik Kompozit" {{ old('numune_alma_sekli', $rapor->numune_alma_sekli) == '24 Saatlik Kompozit' ? 'selected' : '' }}>24 Saatlik Kompozit</option>
                        <option value="12 Saatlik Kompozit" {{ old('numune_alma_sekli', $rapor->numune_alma_sekli) == '12 Saatlik Kompozit' ? 'selected' : '' }}>12 Saatlik Kompozit</option>
                        <option value="Anlık Numune" {{ old('numune_alma_sekli', $rapor->numune_alma_sekli) == 'Anlık Numune' ? 'selected' : '' }}>Anlık Numune</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Numune Geliş Şekli</label>
                    <input type="text" name="numune_gelis_sekli" class="form-control" value="{{ old('numune_gelis_sekli', $rapor->numune_gelis_sekli) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Numune Ambalaj</label>
                    <input type="text" name="numune_ambalaj" class="form-control" value="{{ old('numune_ambalaj', $rapor->numune_ambalaj ?: '1 Ad. 1L, 1 Ad. 0,5L Plastik Ambalajlarda') }}">
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
                    <label class="form-label">Şahit Numune Durumu</label>
                    <select name="sahit_numune" class="form-select">
                        <option value="Yok" {{ old('sahit_numune', $rapor->sahit_numune) == 'Yok' ? 'selected' : '' }}>Yok</option>
                        <option value="Var" {{ old('sahit_numune', $rapor->sahit_numune) == 'Var' ? 'selected' : '' }}>Var</option>
                    </select>
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
                            <label class="form-label">Parametre Adı</label>
                            <input type="text" name="parametreler[{{ $index }}][parametre_adi]"
                                   class="form-control form-control-sm parametre-adi-input"
                                   list="parametreListesi"
                                   value="{{ $param->parametre_adi }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Birim</label>
                            <input type="text" name="parametreler[{{ $index }}][birim]"
                                   class="form-control form-control-sm birim-input"
                                   value="{{ $param->birim }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Sonuç</label>
                            <input type="number" step="0.0001" name="parametreler[{{ $index }}][analiz_sonucu]" class="form-control form-control-sm" value="{{ $param->analiz_sonucu }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Limit Değeri</label>
                            <input type="text" name="parametreler[{{ $index }}][limit_degeri]" class="form-control form-control-sm" value="{{ $param->limit_degeri }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Analiz Metodu</label>
                            <input type="text" name="parametreler[{{ $index }}][analiz_metodu]"
                                   class="form-control form-control-sm metod-input"
                                   value="{{ $param->analiz_metodu }}">
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
<datalist id="parametreListesi">
    <option value="Biyokimyasal Oksijen İhtiyacı">
    <option value="Kimyasal Oksijen İhtiyacı">
    <option value="pH">
    <option value="Sıcaklık">
    <option value="Askıda Katı Madde">
    <option value="Toplam Azot">
    <option value="Toplam Fosfor">
    <option value="Tuzluluk">
    <option value="İletkenlik">
    <option value="Amonyum Azotu">
    <option value="Nitrat Azotu">
    <option value="Toplam Kjeldahl Azotu">
    <option value="Çözünmüş Oksijen">
    <option value="Yağ ve Gres">
    <option value="Toplam Koliform">
    <option value="Fekal Koliform">
    <option value="Toplam Çözünmüş Madde">
    <option value="Bulanıklık">
    <option value="Renk">
    <option value="Deterjan">
</datalist>
<script>
let parametreIndex = {{ $rapor->parametreler->count() }};

const parametreDatabase = {
    'Biyokimyasal Oksijen İhtiyacı': { birim: 'mg/L',       metod: 'SM 5210 B' },
    'Kimyasal Oksijen İhtiyacı':     { birim: 'mg/L',       metod: 'SM 5220 D' },
    'pH':                             { birim: '-',          metod: 'SM 4500 H + B' },
    'Sıcaklık':                       { birim: '°C',         metod: 'SM 2550 B' },
    'Askıda Katı Madde':              { birim: 'mg/L',       metod: 'SM 2540 D' },
    'Toplam Azot':                    { birim: 'mg/L',       metod: 'TS EN ISO 20236' },
    'Toplam Fosfor':                  { birim: 'mg/L',       metod: 'SM 4500 P, B ve E' },
    'Tuzluluk':                       { birim: 'g/L',        metod: 'SM 2520 B' },
    'İletkenlik':                     { birim: 'µS/cm',      metod: 'SM 2510 B' },
    'Amonyum Azotu':                  { birim: 'mg/L',       metod: 'SM 4500-NH3 B+C' },
    'Nitrat Azotu':                   { birim: 'mg/L',       metod: 'SM 4500-NO3 E' },
    'Toplam Kjeldahl Azotu':          { birim: 'mg/L',       metod: 'SM 4500-Norg B-D' },
    'Çözünmüş Oksijen':               { birim: 'mg/L',       metod: 'SM 4500-O G' },
    'Yağ ve Gres':                    { birim: 'mg/L',       metod: 'SM 5520 D' },
    'Toplam Koliform':                { birim: 'EMS/100mL',  metod: 'SM 9221 B' },
    'Fekal Koliform':                 { birim: 'EMS/100mL',  metod: 'SM 9221 E' },
    'Toplam Çözünmüş Madde':          { birim: 'mg/L',       metod: 'SM 2540 C' },
    'Bulanıklık':                     { birim: 'NTU',        metod: 'SM 2130 B' },
    'Renk':                           { birim: 'Pt-Co',      metod: 'SM 2120 B' },
    'Deterjan':                       { birim: 'mg/L',       metod: 'SM 5540 C' },
};

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('parametre-adi-input')) {
        const deger = e.target.value.trim();
        const row = e.target.closest('.parametre-row');
        if (row && parametreDatabase[deger]) {
            const data = parametreDatabase[deger];
            const birim = row.querySelector('.birim-input');
            const metod = row.querySelector('.metod-input');
            if (birim) birim.value = data.birim;
            if (metod) metod.value = data.metod;
        }
    }
});

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
                    <label class="form-label">Parametre Adı</label>
                    <input type="text" name="parametreler[${parametreIndex}][parametre_adi]"
                           class="form-control form-control-sm parametre-adi-input"
                           list="parametreListesi">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Birim</label>
                    <input type="text" name="parametreler[${parametreIndex}][birim]"
                           class="form-control form-control-sm birim-input">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Sonuç</label>
                    <input type="number" step="0.0001" name="parametreler[${parametreIndex}][analiz_sonucu]"
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Limit Değeri</label>
                    <input type="text" name="parametreler[${parametreIndex}][limit_degeri]"
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Analiz Metodu</label>
                    <input type="text" name="parametreler[${parametreIndex}][analiz_metodu]"
                           class="form-control form-control-sm metod-input">
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
