@extends('layouts.app')

@section('title', 'İşi Düzenle')

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header - Kompakt -->
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('admin.arsivlenmis-isler.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0">
                        <i class="bi bi-pencil text-warning me-1"></i>İş Düzenle
                    </h5>
                    <small class="text-muted d-none d-md-block">İş bilgilerini güncelleyin</small>
                </div>
            </div>

            <!-- Form - Kompakt -->
            <div class="card shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('admin.arsivlenmis-isler.update', $arsivlenmisIs->id) }}" 
                          method="POST" 
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Bina Seçimi -->
                        <div class="mb-3">
                            <label for="bina_id" class="form-label small mb-1">
                                <i class="bi bi-building text-primary me-1"></i>Bina
                            </label>
                            <select name="bina_id" 
                                    id="bina_id" 
                                    class="form-select @error('bina_id') is-invalid @enderror">
                                <option value="">Bina Yok / Genel</option>
                                @foreach($binalar as $bina)
                                    <option value="{{ $bina->id }}" 
                                            {{ (old('bina_id', $arsivlenmisIs->bina_id) == $bina->id) ? 'selected' : '' }}>
                                        {{ $bina->bina_adi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bina_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- İş Tarihi -->
                        <div class="mb-3">
                            <label for="is_tarihi" class="form-label small mb-1">
                                <i class="bi bi-calendar text-primary me-1"></i>İş Tarihi <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="is_tarihi" 
                                   id="is_tarihi" 
                                   class="form-control @error('is_tarihi') is-invalid @enderror"
                                   value="{{ old('is_tarihi', $arsivlenmisIs->is_tarihi->format('Y-m-d')) }}"
                                   required>
                            @error('is_tarihi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- İş Açıklaması -->
                        <div class="mb-3">
                            <label for="is_aciklamasi" class="form-label small mb-1">
                                <i class="bi bi-card-text text-primary me-1"></i>İş Açıklaması <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="is_aciklamasi" 
                                   id="is_aciklamasi" 
                                   class="form-control @error('is_aciklamasi') is-invalid @enderror"
                                   value="{{ old('is_aciklamasi', $arsivlenmisIs->is_aciklamasi) }}"
                                   maxlength="255"
                                   required>
                            @error('is_aciklamasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Detaylı Açıklama -->
                        <div class="mb-3">
                            <label for="detayli_aciklama" class="form-label small mb-1">
                                <i class="bi bi-text-paragraph text-primary me-1"></i>Detaylı Açıklama
                            </label>
                            <textarea name="detayli_aciklama" 
                                      id="detayli_aciklama" 
                                      class="form-control form-control-sm @error('detayli_aciklama') is-invalid @enderror"
                                      rows="3">{{ old('detayli_aciklama', $arsivlenmisIs->detayli_aciklama) }}</textarea>
                            @error('detayli_aciklama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mevcut Fotoğraflar -->
                        @if($arsivlenmisIs->fotograflar && count($arsivlenmisIs->fotograflar) > 0)
                        <div class="mb-3">
                            <label class="form-label small mb-2">
                                    <i class="bi bi-images text-primary me-1"></i>Mevcut Fotoğraflar
                                </label>
                                <div class="row g-2" id="mevcutFotograflar">
                                    @foreach($arsivlenmisIs->fotograflar as $index => $fotograf)
                                        <div class="col-md-3 col-sm-4 col-6" data-fotograf="{{ $fotograf }}">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $fotograf) }}" 
                                                     class="img-fluid rounded" 
                                                     style="width: 100%; height: 150px; object-fit: cover;">
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 sil-fotograf"
                                                        data-fotograf="{{ $fotograf }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <input type="hidden" 
                                                       name="eski_fotograflar[]" 
                                                       value="{{ $fotograf }}" 
                                                       class="eski-fotograf-input">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Yeni Fotoğraflar -->
                        <div class="mb-3">
                            <label for="fotograflar" class="form-label small mb-1">
                                <i class="bi bi-camera text-primary me-1"></i>Yeni Fotoğraf Ekle
                            </label>
                            <input type="file" 
                                   name="fotograflar[]" 
                                   id="fotograflar" 
                                   class="form-control form-control-sm @error('fotograflar.*') is-invalid @enderror"
                                   accept="image/*"
                                   capture="environment"
                                   multiple>
                            @error('fotograflar.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle"></i> 
                                Fotoğraf çekin veya galeriden seçin (max 5MB)
                            </small>

                            <!-- Önizleme Alanı -->
                            <div id="fotografOnizleme" class="row mt-3 g-2"></div>
                        </div>

                        <hr class="my-3">

                        <!-- Butonlar - Kompakt -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.arsivlenmis-isler.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i><span class="d-none d-sm-inline">İptal</span>
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i>Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Yeni fotoğraf önizleme
document.getElementById('fotograflar').addEventListener('change', function(e) {
    const onizlemeAlani = document.getElementById('fotografOnizleme');
    onizlemeAlani.innerHTML = '';

    if (this.files) {
        Array.from(this.files).forEach((file, index) => {
            if (file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-4 col-6';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-fluid rounded" style="width: 100%; height: 150px; object-fit: cover;">
                            <span class="badge bg-success position-absolute top-0 end-0 m-1">YENİ</span>
                        </div>
                    `;
                    onizlemeAlani.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// Mevcut fotoğraf silme
document.querySelectorAll('.sil-fotograf').forEach(button => {
    button.addEventListener('click', function() {
        if (confirm('Bu fotoğrafı silmek istediğinize emin misiniz?')) {
            const fotografPath = this.dataset.fotograf;
            const parentDiv = this.closest('[data-fotograf]');
            
            // Hidden inputu kaldır
            const hiddenInput = parentDiv.querySelector('.eski-fotograf-input');
            if (hiddenInput) {
                hiddenInput.remove();
            }
            
            // Görsel olarak kaldır
            parentDiv.remove();
        }
    });
});
</script>
@endsection
