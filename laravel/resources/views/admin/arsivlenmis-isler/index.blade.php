@extends('layouts.app')

@section('title', 'Arşivlenmiş İşler')

@section('content')
<div class="container-fluid">
    <!-- Success Mesajı -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="mb-2">
                <i class="bi bi-archive text-primary me-2"></i>Arşivlenmiş İşler
            </h2>
            <p class="text-muted mb-0 d-none d-md-block">Tesiste yapılan ve kayıt altına alınan tüm işler</p>
        </div>
        <a href="{{ route('admin.arsivlenmis-isler.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i><span class="d-none d-sm-inline">Yeni İş Ekle</span><span class="d-inline d-sm-none">Ekle</span>
        </a>
    </div>

    <!-- Filtreler - Kompakt Tasarım -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtrele</h6>
        </div>
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.arsivlenmis-isler.index') }}" class="row g-2">
                <div class="col-6 col-md-3">
                    <label class="form-label small mb-1">Bina</label>
                    <select name="bina_id" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        @foreach($binalar as $bina)
                            <option value="{{ $bina->id }}" {{ request('bina_id') == $bina->id ? 'selected' : '' }}>
                                {{ $bina->bina_adi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-6 col-md-3">
                    <label class="form-label small mb-1">Başlangıç</label>
                    <input type="date" name="baslangic_tarihi" class="form-control form-control-sm" 
                           value="{{ request('baslangic_tarihi') }}">
                </div>
                
                <div class="col-6 col-md-3">
                    <label class="form-label small mb-1">Bitiş</label>
                    <input type="date" name="bitis_tarihi" class="form-control form-control-sm" 
                           value="{{ request('bitis_tarihi') }}">
                </div>
                
                <div class="col-6 col-md-3">
                    <label class="form-label small mb-1">Ara</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="arama" class="form-control form-control-sm" 
                               placeholder="İş ara..." 
                               value="{{ request('arama') }}">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                
                @if(request()->hasAny(['bina_id', 'baslangic_tarihi', 'bitis_tarihi', 'arama']))
                    <div class="col-12">
                        <a href="{{ route('admin.arsivlenmis-isler.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Temizle
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- İşler Listesi -->
    @if($isler->count() > 0)
        <div class="row g-3">
            @foreach($isler as $is)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card shadow-sm h-100 hover-shadow">
                        <!-- Fotoğraf -->
                        @if($is->fotograflar && count($is->fotograflar) > 0)
                            <div style="height: 160px; overflow: hidden; position: relative;">
                                <img src="{{ asset('storage/' . $is->fotograflar[0]) }}" 
                                     class="card-img-top" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     alt="İş Fotoğrafı">
                                @if(count($is->fotograflar) > 1)
                                    <span class="badge bg-dark position-absolute top-0 end-0 m-2">
                                        <i class="bi bi-images"></i> {{ count($is->fotograflar) }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 160px;">
                                <i class="bi bi-image text-muted" style="font-size: 2.5rem;"></i>
                            </div>
                        @endif

                        <div class="card-body p-3">
                            <!-- Başlık -->
                            <h6 class="card-title mb-2" style="font-size: 0.95rem;">{{ $is->is_aciklamasi }}</h6>
                            
                            <!-- Bina ve Tarih -->
                            <div class="mb-2">
                                @if($is->bina)
                                    <span class="badge bg-primary me-1" style="font-size: 0.7rem;">
                                        <i class="bi bi-building"></i> {{ strlen($is->bina->bina_adi) > 15 ? substr($is->bina->bina_adi, 0, 15) . '...' : $is->bina->bina_adi }}
                                    </span>
                                @endif
                                <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                    <i class="bi bi-calendar"></i> {{ $is->is_tarihi->format('d.m.Y') }}
                                </span>
                            </div>

                            <!-- Detaylı Açıklama (Kısaltılmış) -->
                            @if($is->detayli_aciklama)
                                <p class="card-text text-muted mb-2" style="font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $is->detayli_aciklama }}
                                </p>
                            @endif

                            <!-- Oluşturan -->
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">
                                <i class="bi bi-person"></i> {{ $is->olusturan->name }}
                            </small>
                        </div>

                        <!-- Aksiyon Butonları -->
                        <div class="card-footer bg-white border-top-0 p-2">
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.arsivlenmis-isler.show', $is->id) }}" 
                                   class="btn btn-sm btn-outline-primary flex-fill py-1" style="font-size: 0.8rem;">
                                    <i class="bi bi-eye"></i><span class="d-none d-sm-inline"> Detay</span>
                                </a>
                                <a href="{{ route('admin.arsivlenmis-isler.edit', $is->id) }}" 
                                   class="btn btn-sm btn-outline-warning flex-fill py-1" style="font-size: 0.8rem;">
                                    <i class="bi bi-pencil"></i><span class="d-none d-sm-inline"> Düzenle</span>
                                </a>
                                <form action="{{ route('admin.arsivlenmis-isler.destroy', $is->id) }}" 
                                      method="POST" 
                                      class="flex-fill"
                                      onsubmit="return confirm('Bu işi silmek istediğinize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-1" style="font-size: 0.8rem;">
                                        <i class="bi bi-trash"></i><span class="d-none d-sm-inline"> Sil</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $isler->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>
            @if(request()->hasAny(['bina_id', 'baslangic_tarihi', 'bitis_tarihi', 'arama']))
                Filtrelere uygun iş bulunamadı. <a href="{{ route('admin.arsivlenmis-isler.index') }}">Tüm işleri görüntüle</a>
            @else
                Henüz arşivlenmiş iş bulunmuyor. <a href="{{ route('admin.arsivlenmis-isler.create') }}">İlk işi ekleyin</a>
            @endif
        </div>
    @endif
</div>

<style>
.hover-shadow {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Mobile için ekstra düzenlemeler */
@media (max-width: 576px) {
    .card-title {
        font-size: 0.9rem !important;
    }
    .hover-shadow:hover {
        transform: none;
    }
}
</style>
@endsection
