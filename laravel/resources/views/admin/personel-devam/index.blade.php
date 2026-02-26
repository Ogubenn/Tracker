@extends('layouts.app')

@section('title', 'Personel Devam Takibi - Haftalık')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-calendar-check"></i> Personel Devam Takibi
        </h1>
        <div>
            <a href="{{ route('admin.personel-devam.aylik') }}" class="btn btn-info">
                <i class="bi bi-calendar-month"></i> Aylık Görünüm
            </a>
        </div>
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

    <!-- Hafta Navigasyonu -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.personel-devam.index', ['tarih' => $oncekiHafta->format('Y-m-d')]) }}" 
                   class="btn btn-outline-primary">
                    <i class="bi bi-chevron-left"></i> Önceki Hafta
                </a>
                
                <h4 class="mb-0">
                    {{ $haftaBaslangic->translatedFormat('d F Y') }} - 
                    {{ $haftaBaslangic->copy()->endOfWeek(Carbon\Carbon::SUNDAY)->translatedFormat('d F Y') }}
                </h4>
                
                <a href="{{ route('admin.personel-devam.index', ['tarih' => $sonrakiHafta->format('Y-m-d')]) }}" 
                   class="btn btn-outline-primary">
                    Sonraki Hafta <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Devam Tablosu -->
    <form method="POST" action="{{ route('admin.personel-devam.store') }}" id="devamForm">
        @csrf
        
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Kullanım:</strong> Her personel için günlük giriş/çıkış yapıp yapmadığını işaretleyin. 
                    Durumu seçin (Çalıştı/İzinli/Raporlu/Gelmedi). Gerekirse not ekleyin.
                    <br><small><strong>Önemli:</strong> Vardiyacılar için giriş günü ile çıkış günü farklı olabilir.</small>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th style="min-width: 150px;">Personel</th>
                                @foreach($gunler as $gun)
                                    <th class="text-center" style="min-width: 120px;">
                                        {{ $gun->translatedFormat('D') }}<br>
                                        <small>{{ $gun->format('d.m') }}</small>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($personeller as $personel)
                                <tr>
                                    <td class="fw-bold">{{ $personel->ad }}</td>
                                    
                                    @foreach($gunler as $gun)
                                        @php
                                            $key = $personel->id . '_' . $gun->format('Y-m-d');
                                            $kayit = $devamKayitlari[$key] ?? null;
                                            $index = $personel->id . '_' . $gun->format('Ymd');
                                        @endphp
                                        
                                        <td class="p-2" style="vertical-align: top;">
                                            <input type="hidden" name="kayitlar[{{ $index }}][user_id]" value="{{ $personel->id }}">
                                            <input type="hidden" name="kayitlar[{{ $index }}][tarih]" value="{{ $gun->format('Y-m-d') }}">
                                            
                                            <div class="form-check form-check-inline" style="font-size: 0.85rem;">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="kayitlar[{{ $index }}][giris_yapti]" 
                                                       id="giris_{{ $index }}"
                                                       value="1"
                                                       {{ $kayit && $kayit->giris_yapti ? 'checked' : '' }}>
                                                <label class="form-check-label" for="giris_{{ $index }}">
                                                    G
                                                </label>
                                            </div>
                                            
                                            <div class="form-check form-check-inline" style="font-size: 0.85rem;">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="kayitlar[{{ $index }}][cikis_yapti]" 
                                                       id="cikis_{{ $index }}"
                                                       value="1"
                                                       {{ $kayit && $kayit->cikis_yapti ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cikis_{{ $index }}">
                                                    Ç
                                                </label>
                                            </div>
                                            
                                            <select class="form-select form-select-sm mt-1" 
                                                    name="kayitlar[{{ $index }}][durum]" 
                                                    style="font-size: 0.75rem;">
                                                <option value="calisma" {{ (!$kayit || $kayit->durum == 'calisma') ? 'selected' : '' }}>Çalıştı</option>
                                                <option value="izinli" {{ ($kayit && $kayit->durum == 'izinli') ? 'selected' : '' }}>İzinli</option>
                                                <option value="raporlu" {{ ($kayit && $kayit->durum == 'raporlu') ? 'selected' : '' }}>Raporlu</option>
                                                <option value="gelmedi" {{ ($kayit && $kayit->durum == 'gelmedi') ? 'selected' : '' }}>Gelmedi</option>
                                            </select>
                                            
                                            @if($kayit && $kayit->notlar)
                                                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                                    <i class="bi bi-chat-left-text"></i>
                                                </small>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-secondary me-2">G: Giriş</span>
                        <span class="badge bg-secondary me-2">Ç: Çıkış</span>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-save"></i> Kaydet
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
}

.table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}

.table td:first-child {
    position: sticky;
    left: 0;
    background-color: white;
    z-index: 5;
}

.table thead th:first-child {
    position: sticky;
    left: 0;
    z-index: 15;
}
</style>
@endsection
