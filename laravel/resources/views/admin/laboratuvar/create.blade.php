@extends('layouts.app')

@section('title', 'Yeni Laboratuvar Raporu')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0 fw-semibold">
            <i class="bi bi-plus-circle"></i> Yeni Laboratuvar Raporu
        </h1>
        <a href="{{ route('admin.laboratuvar.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Geri
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Hata!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.laboratuvar.store') }}" enctype="multipart/form-data" id="raporForm">
        @csrf

        <!-- Temel Bilgiler -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body p-3">
                <h6 class="mb-3 text-primary fw-semibold"><i class="bi bi-file-text"></i> Temel Rapor Bilgileri</h6>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label mb-1 small fw-semibold">Rapor No</label>
                        <input type="text" name="rapor_no" class="form-control"
                               placeholder="R-79051-2026-001" value="{{ old('rapor_no') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1 small fw-semibold">Rapor Tarihi</label>
                        <input type="date" name="rapor_tarihi" class="form-control"
                               value="{{ old('rapor_tarihi', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1 small fw-semibold">Raporu Yükleyen</label>
                        <select name="olusturan_id" id="olusturanSelect" class="form-select">
                            <option value="">Personel Seçin...</option>
                            @foreach(\App\Models\User::where('aktif_mi', true)->orderBy('ad')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->ad }} - {{ ucfirst($user->rol) }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1" style="font-size:0.75rem;"><i class="bi bi-info-circle"></i> Otomatik hatırlanır</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label mb-1 small fw-semibold">Müşteri Adı</label>
                        <input type="text" name="tesis_adi" class="form-control"
                               value="{{ old('tesis_adi', 'BULANCAK BELEDİYESİ SU VE KANALİZASYON İŞLETME MÜDÜRLÜĞÜ') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1 small fw-semibold">Teklif Tarihi</label>
                        <input type="date" name="teklif_tarihi" class="form-control"
                               value="{{ old('teklif_tarihi') }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label mb-1 small fw-semibold">Teklif Numarası</label>
                        <input type="text" name="teklif_no" class="form-control"
                               placeholder="T-79051-2025-03" value="{{ old('teklif_no') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Numune Bilgileri -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body p-3">
                <h6 class="mb-3 text-info fw-semibold"><i class="bi bi-droplet"></i> Numune Bilgileri</h6>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Numunenin Cinsi ve Adı</label>
                        <select name="numune_cinsi_adi" class="form-select">
                            <option value="">Seçiniz...</option>
                            <option value="Atıksu Arıtma Tesis Çıkış Numunesi" {{ old('numune_cinsi_adi') == 'Atıksu Arıtma Tesis Çıkış Numunesi' ? 'selected' : '' }}>
                                Atıksu Arıtma Tesis Çıkış Numunesi
                            </option>
                            <option value="Atıksu Arıtma Tesisi Giriş Numunesi" {{ old('numune_cinsi_adi') == 'Atıksu Arıtma Tesisi Giriş Numunesi' ? 'selected' : '' }}>
                                Atıksu Arıtma Tesisi Giriş Numunesi
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Numune Alma Noktası ve Sayısı</label>
                        <input type="text" name="numune_alma_noktasi_sayisi" class="form-control"
                               placeholder="Atıksu Arıtma Tesisi Çıkış 1" value="{{ old('numune_alma_noktasi_sayisi', 'Atıksu Arıtma Tesisi Çıkış 1') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Numune Alma Başlangıç Tarih ve Saati</label>
                        <input type="datetime-local" name="numune_alma_tarihi" class="form-control"
                               value="{{ old('numune_alma_tarihi') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Numune Alma Bitiş Tarih ve Saati</label>
                        <input type="datetime-local" name="numune_alma_tarihi_bitis" class="form-control"
                               value="{{ old('numune_alma_tarihi_bitis') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Numune Alınış Şekli</label>
                        <select name="numune_alma_sekli" class="form-select">
                            <option value="">Seçiniz...</option>
                            <option value="24 Saatlik Kompozit" {{ old('numune_alma_sekli', '24 Saatlik Kompozit') == '24 Saatlik Kompozit' ? 'selected' : '' }}>24 Saatlik Kompozit</option>
                            <option value="12 Saatlik Kompozit" {{ old('numune_alma_sekli') == '12 Saatlik Kompozit' ? 'selected' : '' }}>12 Saatlik Kompozit</option>
                            <option value="Anlık Numune" {{ old('numune_alma_sekli') == 'Anlık Numune' ? 'selected' : '' }}>Anlık Numune</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Numune Geliş Şekli</label>
                        <input type="text" name="numune_gelis_sekli" class="form-control"
                               value="{{ old('numune_gelis_sekli', 'Yerinde Alma-Korumalı Mühürlü') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Numune Ambalaj Durumu</label>
                        <input type="text" name="numune_ambalaj" class="form-control"
                               value="{{ old('numune_ambalaj', '1 Ad. 1L, 1 Ad. 0,5L Plastik Ambalajlarda') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Numune Numarası</label>
                        <input type="text" name="numune_numarasi" class="form-control"
                               placeholder="AS-29012026-005" value="{{ old('numune_numarasi') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Laboratuvar Geliş Tarihi ve Saati</label>
                        <input type="datetime-local" name="lab_gelis_tarihi" class="form-control"
                               value="{{ old('lab_gelis_tarihi') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Şahit Numune Durumu</label>
                        <select name="sahit_numune" class="form-select">
                            <option value="Yok" {{ old('sahit_numune', 'Yok') == 'Yok' ? 'selected' : '' }}>Yok</option>
                            <option value="Var" {{ old('sahit_numune') == 'Var' ? 'selected' : '' }}>Var</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analiz Tarihleri -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body p-3">
                <h6 class="mb-3 text-success fw-semibold"><i class="bi bi-calendar-range"></i> Analiz Tarih Aralığı</h6>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Analiz Başlangıç Tarihi</label>
                        <input type="date" name="analiz_baslangic" class="form-control"
                               value="{{ old('analiz_baslangic') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Analiz Bitiş Tarihi</label>
                        <input type="date" name="analiz_bitis" class="form-control"
                               value="{{ old('analiz_bitis') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Parametreler -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-warning fw-semibold"><i class="bi bi-list-check"></i> Analiz Parametreleri</h6>
                    <button type="button" class="btn btn-sm btn-dark" onclick="parametreEkle()">
                        <i class="bi bi-plus-circle"></i> Ekle
                    </button>
                </div>
                <div class="row g-1 mb-1 d-none d-md-flex px-1">
                    <div class="col-md-3"><small class="fw-semibold text-muted">Parametre Adı</small></div>
                    <div class="col-md-2"><small class="fw-semibold text-muted">Birim</small></div>
                    <div class="col-md-2"><small class="fw-semibold text-muted">Analiz Sonucu</small></div>
                    <div class="col-md-2"><small class="fw-semibold text-muted">Limit Değeri</small></div>
                    <div class="col-md-2"><small class="fw-semibold text-muted">Analiz Metodu</small></div>
                    <div class="col-md-1"></div>
                </div>
                <div id="parametreContainer">
                    <div class="parametre-row border rounded p-2 mb-2 bg-light">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <input type="text" name="parametreler[0][parametre_adi]"
                                       class="form-control form-control-sm parametre-adi-input"
                                       list="parametreListesi"
                                       placeholder="Parametre seçin veya yazın...">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="parametreler[0][birim]"
                                       class="form-control form-control-sm birim-input"
                                       placeholder="mg/L">
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.0001" name="parametreler[0][analiz_sonucu]"
                                       class="form-control form-control-sm" placeholder="0.00">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="parametreler[0][limit_degeri]"
                                       class="form-control form-control-sm" placeholder="25">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="parametreler[0][analiz_metodu]"
                                       class="form-control form-control-sm metod-input"
                                       placeholder="SM 5210 B">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-danger w-100" onclick="parametreSil(this)" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PDF & Notlar -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body p-3">
                        <label class="form-label mb-2 small fw-semibold text-secondary">
                            <i class="bi bi-file-pdf"></i> PDF Dosyası (Opsiyonel)
                        </label>
                        <input type="file" name="pdf_dosya" class="form-control form-control-sm" accept=".pdf">
                        <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Resmi rapor PDF'ini yükleyebilirsiniz</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body p-3">
                        <label class="form-label mb-2 small fw-semibold text-secondary">
                            <i class="bi bi-chat-left-text"></i> Notlar (Opsiyonel)
                        </label>
                        <textarea name="notlar" class="form-control form-control-sm" rows="2"
                                  placeholder="Varsa ek notlarınızı buraya yazabilirsiniz...">{{ old('notlar') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mb-3">
            <a href="{{ route('admin.laboratuvar.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> İptal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Raporu Kaydet
            </button>
        </div>
    </form>
</div>

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
let parametreSayisi = 1;

const parametreDatabase = {
    'Biyokimyasal Oksijen İhtiyacı': { birim: 'mg/L',        metod: 'SM 5210 B' },
    'Kimyasal Oksijen İhtiyacı':     { birim: 'mg/L',        metod: 'SM 5220 D' },
    'pH':                             { birim: '-',           metod: 'SM 4500 H + B' },
    'Sıcaklık':                       { birim: '°C',          metod: 'SM 2550 B' },
    'Askıda Katı Madde':              { birim: 'mg/L',        metod: 'SM 2540 D' },
    'Toplam Azot':                    { birim: 'mg/L',        metod: 'TS EN ISO 20236' },
    'Toplam Fosfor':                  { birim: 'mg/L',        metod: 'SM 4500 P, B ve E' },
    'Tuzluluk':                       { birim: 'g/L',         metod: 'SM 2520 B' },
    'İletkenlik':                     { birim: 'µS/cm',       metod: 'SM 2510 B' },
    'Amonyum Azotu':                  { birim: 'mg/L',        metod: 'SM 4500-NH3 B+C' },
    'Nitrat Azotu':                   { birim: 'mg/L',        metod: 'SM 4500-NO3 E' },
    'Toplam Kjeldahl Azotu':          { birim: 'mg/L',        metod: 'SM 4500-Norg B-D' },
    'Çözünmüş Oksijen':               { birim: 'mg/L',        metod: 'SM 4500-O G' },
    'Yağ ve Gres':                    { birim: 'mg/L',        metod: 'SM 5520 D' },
    'Toplam Koliform':                { birim: 'EMS/100mL',   metod: 'SM 9221 B' },
    'Fekal Koliform':                 { birim: 'EMS/100mL',   metod: 'SM 9221 E' },
    'Toplam Çözünmüş Madde':          { birim: 'mg/L',        metod: 'SM 2540 C' },
    'Bulanıklık':                     { birim: 'NTU',         metod: 'SM 2130 B' },
    'Renk':                           { birim: 'Pt-Co',       metod: 'SM 2120 B' },
    'Deterjan':                       { birim: 'mg/L',        metod: 'SM 5540 C' },
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

document.addEventListener('DOMContentLoaded', function() {
    const olusturanSelect = document.getElementById('olusturanSelect');
    const savedOlusturan = localStorage.getItem('laboratuvar_olusturan_id');
    if (savedOlusturan && olusturanSelect) {
        olusturanSelect.value = savedOlusturan;
    }
    if (olusturanSelect) {
        olusturanSelect.addEventListener('change', function() {
            if (this.value) {
                localStorage.setItem('laboratuvar_olusturan_id', this.value);
            }
        });
    }
});

function parametreEkle() {
    const container = document.getElementById('parametreContainer');
    const idx = parametreSayisi;
    const yeniRow = document.createElement('div');
    yeniRow.className = 'parametre-row border rounded p-2 mb-2 bg-light';
    yeniRow.innerHTML = `
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="parametreler[${idx}][parametre_adi]"
                       class="form-control form-control-sm parametre-adi-input"
                       list="parametreListesi"
                       placeholder="Parametre seçin veya yazın..." required>
            </div>
            <div class="col-md-2">
                <input type="text" name="parametreler[${idx}][birim]"
                       class="form-control form-control-sm birim-input"
                       placeholder="mg/L">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.0001" name="parametreler[${idx}][analiz_sonucu]"
                       class="form-control form-control-sm" placeholder="0.00">
            </div>
            <div class="col-md-2">
                <input type="text" name="parametreler[${idx}][limit_degeri]"
                       class="form-control form-control-sm" placeholder="25">
            </div>
            <div class="col-md-2">
                <input type="text" name="parametreler[${idx}][analiz_metodu]"
                       class="form-control form-control-sm metod-input"
                       placeholder="SM 5210 B">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger w-100" onclick="parametreSil(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(yeniRow);
    parametreSayisi++;
}

function parametreSil(btn) {
    const row = btn.closest('.parametre-row');
    if (document.querySelectorAll('.parametre-row').length > 1) {
        row.remove();
    } else {
        alert('En az bir parametre olmalıdır!');
    }
}
</script>
@endsection
