@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-clipboard-check"></i> Kontrol Kaydı Detayı</h1>
</div>

<div class="content-card mb-4">
    <div class="content-card-header">
        <h5>Genel Bilgiler</h5>
    </div>
    <div class="content-card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <strong>Bina:</strong>
                <p>{{ $kayit->bina->bina_adi }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Kontrol Maddesi:</strong>
                <p>{{ $kayit->kontrolMaddesi->kontrol_adi }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Tarih:</strong>
                <p>{{ $kayit->tarih->format('d.m.Y') }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Personel:</strong>
                <p>{{ $kayit->yapanKullanici->name }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>IP Adresi:</strong>
                <p>{{ $kayit->ip_adresi }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Kayıt Zamanı:</strong>
                <p>{{ $kayit->created_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="content-card mb-4">
    <div class="content-card-header">
        <h5>Kontrol Bilgileri</h5>
    </div>
    <div class="content-card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <strong>Girilen Değer:</strong>
                <p>
                    @if($kayit->girilen_deger)
                        {{ $kayit->girilen_deger }}
                        @if($kayit->kontrolMaddesi && $kayit->kontrolMaddesi->birim)
                            <strong class="text-primary">{{ $kayit->kontrolMaddesi->birim }}</strong>
                        @endif
                    @else
                        -
                    @endif
                </p>
            </div>
            
            @if($kayit->baslangic_saati || $kayit->bitis_saati)
                <div class="col-md-4 mb-3">
                    <strong>Çalışma Saatleri:</strong>
                    <p>
                        @if($kayit->baslangic_saati)
                            <i class="bi bi-clock text-success"></i> {{ \Carbon\Carbon::parse($kayit->baslangic_saati)->format('H:i') }}
                        @endif
                        @if($kayit->baslangic_saati && $kayit->bitis_saati)
                            -
                        @endif
                        @if($kayit->bitis_saati)
                            <i class="bi bi-clock-fill text-danger"></i> {{ \Carbon\Carbon::parse($kayit->bitis_saati)->format('H:i') }}
                        @endif
                    </p>
                </div>
            @endif
            
            <div class="col-md-4 mb-3">
                <strong>Durum:</strong>
                <p>
                    @if($kayit->durum === 'uygun')
                        <span class="badge bg-success">Uygun</span>
                    @elseif($kayit->durum === 'uygun_degil')
                        <span class="badge bg-danger">Uygun Değil</span>
                    @else
                        <span class="badge bg-warning">Düzeltme Gerekli</span>
                    @endif
                </p>
            </div>
            <div class="col-md-4 mb-3">
                <strong>Onay Durumu:</strong>
                <p>
                    @if($kayit->onay_durumu === 'bekliyor')
                        <span class="badge bg-warning">Bekliyor</span>
                    @elseif($kayit->onay_durumu === 'onaylandi')
                        <span class="badge bg-success">Onaylandı</span>
                    @else
                        <span class="badge bg-danger">Reddedildi</span>
                    @endif
                </p>
            </div>
            @if($kayit->aciklama)
                <div class="col-12 mb-3">
                    <strong>Açıklama:</strong>
                    <p>{{ $kayit->aciklama }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($kayit->fotograflar && count($kayit->fotograflar) > 0)
    <div class="content-card mb-4">
        <div class="content-card-header">
            <h5><i class="bi bi-camera-fill me-2"></i>Kontrol Fotoğrafları ({{ count($kayit->fotograflar) }})</h5>
        </div>
        <div class="content-card-body">
            <div class="row g-3">
                @foreach($kayit->fotograflar as $foto)
                    <div class="col-6 col-md-3">
                        <a href="{{ Storage::disk('public')->url($foto) }}" 
                           data-lightbox="kontrol-fotograflar" 
                           data-title="Kontrol Fotoğrafı">
                            <img src="{{ Storage::disk('public')->url($foto) }}" 
                                 class="img-thumbnail foto-thumbnail" 
                                 alt="Kontrol Fotoğrafı"
                                 loading="lazy">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@if($kayit->dosyalar && count($kayit->dosyalar) > 0)
    <div class="content-card mb-4">
        <div class="content-card-header">
            <h5><i class="bi bi-images me-2"></i>Genel Fotoğraflar ({{ count($kayit->dosyalar) }})</h5>
        </div>
        <div class="content-card-body">
            <div class="row g-3">
                @foreach($kayit->dosyalar as $dosya)
                    <div class="col-6 col-md-3">
                        <a href="{{ Storage::disk('public')->url($dosya) }}" 
                           data-lightbox="genel-fotograflar" 
                           data-title="Genel Fotoğraf">
                            <img src="{{ Storage::disk('public')->url($dosya) }}" 
                                 class="img-thumbnail foto-thumbnail" 
                                 alt="Genel Fotoğraf"
                                 loading="lazy">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@if($kayit->onay_durumu !== 'bekliyor')
    <div class="content-card mb-4">
        <div class="content-card-header">
            <h5>Onay Bilgileri</h5>
        </div>
        <div class="content-card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Onaylayan:</strong>
                    <p>{{ $kayit->onaylayan->name ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Onay Tarihi:</strong>
                    <p>{{ $kayit->onay_tarihi ? $kayit->onay_tarihi->format('d.m.Y H:i') : '-' }}</p>
                </div>
                @if($kayit->admin_notu)
                    <div class="col-12">
                        <strong>Admin Notu:</strong>
                        <p>{{ $kayit->admin_notu }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

<div class="d-flex gap-2">
    <a href="{{ route('admin.kontrol-kayitlari.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Geri Dön
    </a>
    
    @if($kayit->onay_durumu === 'bekliyor')
        <form action="{{ route('admin.kontrol-kayitlari.onayla', $kayit->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-lg"></i> Onayla
            </button>
        </form>
        
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#redModal">
            <i class="bi bi-x-lg"></i> Reddet
        </button>
    @endif
</div>

<!-- Reddetme Modal -->
<div class="modal fade" id="redModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kaydı Reddet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kontrol-kayitlari.reddet', $kayit->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reddetme Sebebi *</label>
                        <textarea name="admin_notu" class="form-control" rows="4" 
                                  required placeholder="Neden reddedildiğini açıklayın"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">Reddet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.foto-thumbnail {
    width: 100%;
    height: 200px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.foto-thumbnail:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<!-- Lightbox2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">

<!-- Lightbox2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script>
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': "Fotoğraf %1 / %2"
    });
</script>
@endsection
