@extends('layouts.app')

@section('title', 'İş Detayı')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.arsivlenmis-isler.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="mb-0">{{ $arsivlenmisIs->is_aciklamasi }}</h2>
                        <p class="text-muted mb-0">
                            @if($arsivlenmisIs->bina)
                                <i class="bi bi-building"></i> {{ $arsivlenmisIs->bina->bina_adi }} • 
                            @endif
                            <i class="bi bi-calendar"></i> {{ $arsivlenmisIs->is_tarihi->format('d.m.Y') }}
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.arsivlenmis-isler.edit', $arsivlenmisIs->id) }}" 
                       class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Düzenle
                    </a>
                    <form action="{{ route('admin.arsivlenmis-isler.destroy', $arsivlenmisIs->id) }}" 
                          method="POST"
                          onsubmit="return confirm('Bu işi silmek istediğinize emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Sil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bilgi Kartları -->
            <div class="row mb-4">
                @if($arsivlenmisIs->bina)
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-building text-primary" style="font-size: 2rem;"></i>
                                <h6 class="mt-2 mb-1">Bina</h6>
                                <p class="mb-0">{{ $arsivlenmisIs->bina->bina_adi }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-{{ $arsivlenmisIs->bina ? '4' : '6' }}">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-check text-success" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">İş Tarihi</h6>
                            <p class="mb-0">{{ $arsivlenmisIs->is_tarihi->format('d.m.Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-{{ $arsivlenmisIs->bina ? '4' : '6' }}">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-person text-info" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">Oluşturan</h6>
                            <p class="mb-0">{{ $arsivlenmisIs->olusturan->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detaylı Açıklama -->
            @if($arsivlenmisIs->detayli_aciklama)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-text-paragraph text-primary me-2"></i>Detaylı Açıklama
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0" style="white-space: pre-line;">{{ $arsivlenmisIs->detayli_aciklama }}</p>
                    </div>
                </div>
            @endif

            <!-- Fotoğraflar -->
            @if($arsivlenmisIs->fotograflar && count($arsivlenmisIs->fotograflar) > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-images text-primary me-2"></i>Fotoğraflar 
                            <span class="badge bg-primary">{{ count($arsivlenmisIs->fotograflar) }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($arsivlenmisIs->fotograflar as $index => $fotograf)
                                <div class="col-md-4 col-sm-6">
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $fotograf) }}" 
                                             class="img-fluid rounded shadow-sm cursor-pointer fotograf-item" 
                                             style="width: 100%; height: 250px; object-fit: cover; cursor: pointer;"
                                             data-index="{{ $index }}"
                                             alt="İş Fotoğrafı {{ $index + 1 }}">
                                        <span class="badge bg-dark position-absolute bottom-0 end-0 m-2">
                                            {{ $index + 1 }} / {{ count($arsivlenmisIs->fotograflar) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Bu iş için fotoğraf eklenmemiş.
                </div>
            @endif

            <!-- Kayıt Bilgisi -->
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> Oluşturulma: {{ $arsivlenmisIs->created_at->format('d.m.Y H:i') }}
                        @if($arsivlenmisIs->updated_at != $arsivlenmisIs->created_at)
                            • <i class="bi bi-pencil"></i> Son Güncelleme: {{ $arsivlenmisIs->updated_at->format('d.m.Y H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fotoğraf Modal (Lightbox) -->
<div class="modal fade" id="fotografModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">
                    Fotoğraf <span id="modalFotoNo"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalFotograf" src="" class="img-fluid" style="max-height: 80vh;">
            </div>
            <div class="modal-footer border-0 justify-content-between">
                <button type="button" class="btn btn-light" id="oncekiFotograf">
                    <i class="bi bi-chevron-left"></i> Önceki
                </button>
                <button type="button" class="btn btn-light" id="sonrakiFotograf">
                    Sonraki <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fotograflar = @json($arsivlenmisIs->fotograflar);
    let aktifIndex = 0;
    const modal = new bootstrap.Modal(document.getElementById('fotografModal'));

    // Fotoğrafa tıklama
    document.querySelectorAll('.fotograf-item').forEach(item => {
        item.addEventListener('click', function() {
            aktifIndex = parseInt(this.dataset.index);
            fotografGoster(aktifIndex);
            modal.show();
        });
    });

    // Önceki fotoğraf
    document.getElementById('oncekiFotograf').addEventListener('click', function() {
        aktifIndex = (aktifIndex - 1 + fotograflar.length) % fotograflar.length;
        fotografGoster(aktifIndex);
    });

    // Sonraki fotoğraf
    document.getElementById('sonrakiFotograf').addEventListener('click', function() {
        aktifIndex = (aktifIndex + 1) % fotograflar.length;
        fotografGoster(aktifIndex);
    });

    // Klavye navigasyonu
    document.addEventListener('keydown', function(e) {
        if (document.getElementById('fotografModal').classList.contains('show')) {
            if (e.key === 'ArrowLeft') {
                document.getElementById('oncekiFotograf').click();
            } else if (e.key === 'ArrowRight') {
                document.getElementById('sonrakiFotograf').click();
            }
        }
    });

    function fotografGoster(index) {
        document.getElementById('modalFotograf').src = "{{ asset('storage') }}/" + fotograflar[index];
        document.getElementById('modalFotoNo').textContent = (index + 1) + ' / ' + fotograflar.length;
    }
});
</script>
@endsection
