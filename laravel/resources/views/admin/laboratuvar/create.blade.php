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
                        <label class="form-label mb-1 small fw-semibold">Rapor No <span class="text-danger">*</span></label>
                        <input type="text" name="rapor_no" class="form-control" 
                               placeholder="T-79051-2025-03" value="{{ old('rapor_no') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1 small fw-semibold">Rapor Tarihi <span class="text-danger">*</span></label>
                        <input type="date" name="rapor_tarihi" class="form-control" 
                               value="{{ old('rapor_tarihi', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1 small fw-semibold">Raporu Yükleyen <span class="text-danger">*</span></label>
                        <select name="olusturan_id" id="olusturanSelect" class="form-select" required>
                            <option value="">Personel Seçin...</option>
                            @foreach(\App\Models\User::where('aktif_mi', true)->orderBy('ad')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->ad }} - {{ ucfirst($user->rol) }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="bi bi-info-circle"></i> Otomatik hatırlanır</small>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label mb-1 small fw-semibold">Tesis Adı <span class="text-danger">*</span></label>
                        <input type="text" name="tesis_adi" class="form-control" 
                               placeholder="Örn: Bulancak Belediyesi Su ve Kanalizasyon İşletme Müdürlüğü" 
                               value="{{ old('tesis_adi') }}" required>
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
                        <label class="form-label mb-1 small fw-semibold">Numune Cinsi <span class="text-danger">*</span></label>
                        <select name="numune_alma_noktasi" class="form-select" required>
                            <option value="">Numune Cinsini Seçiniz...</option>
                            <option value="Giriş" {{ old('numune_alma_noktasi') == 'Giriş' ? 'selected' : '' }}>Giriş</option>
                            <option value="Çıkış" {{ old('numune_alma_noktasi') == 'Çıkış' ? 'selected' : '' }}>Çıkış</option>
                            <option value="Havalandırma Havuzu" {{ old('numune_alma_noktasi') == 'Havalandırma Havuzu' ? 'selected' : '' }}>Havalandırma Havuzu</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Numune Alma Tarihi ve Saati <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="numune_alma_tarihi" class="form-control" 
                               value="{{ old('numune_alma_tarihi') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analiz Bilgileri -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body p-3">
                <h6 class="mb-3 text-success fw-semibold"><i class="bi bi-calendar-range"></i> Analiz Tarih Aralığı</h6>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Analiz Başlangıç Tarihi <span class="text-danger">*</span></label>
                        <input type="date" name="analiz_baslangic" class="form-control" 
                               value="{{ old('analiz_baslangic', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 small fw-semibold">Analiz Bitiş Tarihi <span class="text-danger">*</span></label>
                        <input type="date" name="analiz_bitis" class="form-control" 
                               value="{{ old('analiz_bitis', date('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parametreler -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-warning fw-semibold"><i class="bi bi-list-check"></i> Analiz Parametreleri</h6>
                    <button type="button" class="btn btn-sm btn-dark" onclick="parametreEkle()">
                        <i class="bi bi-plus-circle"></i> Ekle
                    </button>
                </div>
                <div id="parametreContainer">
                    <!-- İlk parametre satırı -->
                    <div class="parametre-row border rounded p-2 mb-2 bg-light">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label mb-1 small fw-semibold">Parametre Adı <span class="text-danger">*</span></label>
                                <input type="text" name="parametreler[0][parametre_adi]" class="form-control form-control-sm" 
                                       placeholder="pH, BOİ5, KOİ..." required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1 small fw-semibold">Birim</label>
                                <input type="text" name="parametreler[0][birim]" class="form-control form-control-sm" 
                                       placeholder="mg/L">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1 small fw-semibold">Analiz Sonucu <span class="text-danger">*</span></label>
                                <input type="number" step="0.0001" name="parametreler[0][analiz_sonucu]" 
                                       class="form-control form-control-sm" placeholder="4.05" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1 small fw-semibold">Limit Değeri</label>
                                <input type="text" name="parametreler[0][limit_degeri]" class="form-control form-control-sm" 
                                       placeholder="25">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1 small fw-semibold">Analiz Metodu</label>
                                <input type="text" name="parametreler[0][analiz_metodu]" class="form-control form-control-sm" 
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
                        <label class="form-label mb-2 small fw-semibold text-secondary"><i class="bi bi-file-pdf"></i> PDF Dosyası (Opsiyonel)</label>
                        <input type="file" name="pdf_dosya" class="form-control form-control-sm" accept=".pdf">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Resmi rapor PDF'ini yükleyebilirsiniz</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body p-3">
                        <label class="form-label mb-2 small fw-semibold text-secondary"><i class="bi bi-chat-left-text"></i> Notlar (Opsiyonel)</label>
                        <textarea name="notlar" class="form-control form-control-sm" rows="2" 
                                  placeholder="Varsa ek notlarınızı buraya yazabilirsiniz...">{{ old('notlar') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kaydet Butonları -->
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

<script>
let parametreSayisi = 1;

// Personel seçimini localStorage'a kaydet ve otomatik yükle
document.addEventListener('DOMContentLoaded', function() {
    const olusturanSelect = document.getElementById('olusturanSelect');
    
    // Daha önce seçilmiş personel varsa otomatik seç
    const savedOlusturan = localStorage.getItem('laboratuvar_olusturan_id');
    if (savedOlusturan && olusturanSelect) {
        olusturanSelect.value = savedOlusturan;
    }
    
    // Seçim değiştiğinde kaydet
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
    const yeniRow = document.createElement('div');
    yeniRow.className = 'parametre-row border rounded p-2 mb-2 bg-light';
    yeniRow.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold">Parametre Adı <span class="text-danger">*</span></label>
                <input type="text" name="parametreler[${parametreSayisi}][parametre_adi]" class="form-control form-control-sm" 
                       placeholder="pH, BOİ5, KOİ..." required>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1 small fw-semibold">Birim</label>
                <input type="text" name="parametreler[${parametreSayisi}][birim]" class="form-control form-control-sm" 
                       placeholder="mg/L">
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1 small fw-semibold">Analiz Sonucu <span class="text-danger">*</span></label>
                <input type="number" step="0.0001" name="parametreler[${parametreSayisi}][analiz_sonucu]" 
                       class="form-control form-control-sm" placeholder="4.05" required>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1 small fw-semibold">Limit Değeri</label>
                <input type="text" name="parametreler[${parametreSayisi}][limit_degeri]" class="form-control form-control-sm" 
                       placeholder="25">
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1 small fw-semibold">Analiz Metodu</label>
                <input type="text" name="parametreler[${parametreSayisi}][analiz_metodu]" class="form-control form-control-sm" 
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
