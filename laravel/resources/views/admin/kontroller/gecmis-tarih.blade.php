@extends('layouts.app')

@section('title', 'Geçmiş Tarihli Kontrol Girişi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-calendar-week"></i> Geçmiş Tarihli Kontrol Girişi
        </h1>
        <a href="{{ route('admin.kontrol-kayitlari.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Geri
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtreleme Formu -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Tarih ve Bina Seçimi</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.kontroller.gecmis-tarih') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="tarih" class="form-label">
                            <i class="bi bi-calendar3"></i> Kontrol Tarihi <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               class="form-control" 
                               id="tarih" 
                               name="tarih" 
                               value="{{ request('tarih') }}"
                               max="{{ date('Y-m-d') }}"
                               required>
                        <small class="text-muted">Geçmiş bir tarih seçin</small>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="bina_id" class="form-label">
                            <i class="bi bi-building"></i> Bina <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="bina_id" name="bina_id" required>
                            <option value="">Bina Seçin</option>
                            @foreach($binalar as $bina)
                                <option value="{{ $bina->id }}" {{ request('bina_id') == $bina->id ? 'selected' : '' }}>
                                    {{ $bina->bina_adi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="personel_id" class="form-label">
                            <i class="bi bi-person"></i> Kontrol Yapan Personel <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="personel_id" name="personel_id" required>
                            <option value="">Personel Seçin</option>
                            @foreach($personeller as $personel)
                                <option value="{{ $personel->id }}" {{ request('personel_id') == $personel->id ? 'selected' : '' }}>
                                    {{ $personel->ad }} {{ $personel->soyad }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Kontrol Maddelerini Göster
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($kontrolMaddeleri) && isset($seciliBina) && request('tarih') && request('bina_id') && request('personel_id'))
        @if($kontrolMaddeleri->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Bilgi:</strong> Seçilen tarih ve bina için yapılacak kontrol maddesi bulunamadı veya tüm kontroller zaten yapılmış.
            </div>
        @else
            <!-- Kontrol Formu -->
            <form method="POST" action="{{ route('admin.kontroller.gecmis-tarih.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tarih" value="{{ request('tarih') }}">
                <input type="hidden" name="bina_id" value="{{ request('bina_id') }}">
                <input type="hidden" name="personel_id" value="{{ request('personel_id') }}">
                
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-clipboard-check"></i> 
                            {{ $seciliBina->bina_adi }} - {{ \Carbon\Carbon::parse(request('tarih'))->translatedFormat('d F Y') }}
                            <span class="badge bg-light text-dark ms-2">{{ $kontrolMaddeleri->count() }} Kontrol</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($kontrolMaddeleri as $index => $madde)
                            <div class="card mb-3 border-start border-4 border-primary">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">
                                        {{ $index + 1 }}. {{ $madde->kontrol_adi }}
                                        <span class="badge bg-info ms-2">{{ ucfirst($madde->periyot) }}</span>
                                    </h6>
                                    
                                    <input type="hidden" name="kayitlar[{{ $index }}][kontrol_maddesi_id]" value="{{ $madde->id }}">
                                    
                                    <div class="row g-3 mt-2">
                                        <!-- Değer Girişi -->
                                        @if($madde->kontrol_tipi === 'sayisal')
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="bi bi-123"></i> Ölçüm Değeri
                                                    @if($madde->birim) ({{ $madde->birim }}) @endif
                                                </label>
                                                <input type="number" 
                                                       step="0.01" 
                                                       class="form-control"
                                                       name="kayitlar[{{ $index }}][girilen_deger]"
                                                       placeholder="Değer girin">
                                            </div>
                                        @elseif($madde->kontrol_tipi === 'metin')
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="bi bi-pencil"></i> Kontrol Sonucu
                                                </label>
                                                <input type="text" 
                                                       class="form-control"
                                                       name="kayitlar[{{ $index }}][girilen_deger]"
                                                       placeholder="Gözlem yazın">
                                            </div>
                                        @endif
                                        
                                        <!-- Zaman Seçimi -->
                                        @if($madde->zaman_secimi)
                                            <div class="col-md-2">
                                                <label class="form-label">
                                                    <i class="bi bi-clock"></i> Başlangıç
                                                </label>
                                                <input type="time" 
                                                       class="form-control"
                                                       name="kayitlar[{{ $index }}][baslangic_saati]">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">
                                                    <i class="bi bi-clock-fill"></i> Bitiş
                                                </label>
                                                <input type="time" 
                                                       class="form-control"
                                                       name="kayitlar[{{ $index }}][bitis_saati]">
                                            </div>
                                        @endif
                                        
                                        <!-- Durum -->
                                        <div class="col-md-4">
                                            <label class="form-label">
                                                <i class="bi bi-clipboard-check"></i> Durum <span class="text-danger">*</span>
                                            </label>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" 
                                                       class="btn-check" 
                                                       name="kayitlar[{{ $index }}][durum]" 
                                                       id="uygun_{{ $madde->id }}" 
                                                       value="uygun">
                                                <label class="btn btn-outline-success" for="uygun_{{ $madde->id }}">
                                                    <i class="bi bi-check-circle"></i> Uygun
                                                </label>

                                                <input type="radio" 
                                                       class="btn-check" 
                                                       name="kayitlar[{{ $index }}][durum]" 
                                                       id="duzeltme_{{ $madde->id }}" 
                                                       value="duzeltme_gerekli">
                                                <label class="btn btn-outline-warning" for="duzeltme_{{ $madde->id }}">
                                                    <i class="bi bi-exclamation-triangle"></i> Düzeltme
                                                </label>

                                                <input type="radio" 
                                                       class="btn-check" 
                                                       name="kayitlar[{{ $index }}][durum]" 
                                                       id="uygun_degil_{{ $madde->id }}" 
                                                       value="uygun_degil">
                                                <label class="btn btn-outline-danger" for="uygun_degil_{{ $madde->id }}">
                                                    <i class="bi bi-x-circle"></i> Uygun Değil
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <!-- Fotoğraf -->
                                        <div class="col-12">
                                            <label class="form-label">
                                                <i class="bi bi-camera"></i> Fotoğraf Ekle (İsteğe Bağlı)
                                            </label>
                                            <input type="file" 
                                                   class="form-control"
                                                   name="kayitlar[{{ $index }}][fotograflar][]"
                                                   accept="image/*"
                                                   multiple>
                                            <small class="text-muted">Birden fazla fotoğraf seçebilirsiniz</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Genel Açıklama -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-chat-left-text"></i> Genel Açıklama (İsteğe Bağlı)
                                </label>
                                <textarea class="form-control" 
                                          name="genel_aciklama" 
                                          rows="3"
                                          placeholder="Genel notlarınızı yazın..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-save"></i> Kontrolleri Kaydet
                        </button>
                    </div>
                </div>
            </form>
        @endif
    @else
        <div class="alert alert-primary">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Başlamak için:</strong> Yukarıdaki formdan tarih, bina ve personel seçerek kontrol maddelerini görüntüleyin.
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Form validation
    document.getElementById('filterForm')?.addEventListener('submit', function(e) {
        const tarih = document.getElementById('tarih').value;
        const binaId = document.getElementById('bina_id').value;
        const personelId = document.getElementById('personel_id').value;
        
        if (!tarih || !binaId || !personelId) {
            e.preventDefault();
            alert('Lütfen tüm alanları doldurun!');
        }
    });
</script>
@endpush
@endsection
