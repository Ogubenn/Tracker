@extends('layouts.app')

@section('title', 'Raporlar')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h1><i class="bi bi-file-bar-graph"></i> Raporlar</h1>
</div>

<div class="content-card mb-4">
    <div class="content-card-body">
        <form method="GET" action="{{ route('admin.raporlar.index') }}">
            <div class="row g-2">
                <div class="col-md-3 col-6">
                    <label class="form-label small">
                        <i class="bi bi-calendar-event"></i> Başlangıç
                    </label>
                    <input type="date" class="form-control form-control-sm" name="tarih_baslangic" 
                           value="{{ $tarihBaslangic }}" required>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small">
                        <i class="bi bi-calendar-check"></i> Bitiş
                    </label>
                    <input type="date" class="form-control form-control-sm" name="tarih_bitis" 
                           value="{{ $tarihBitis }}" required>
                </div>
                <div class="col-md-4 col-8">
                    <label class="form-label small">
                        <i class="bi bi-building"></i> Bina
                    </label>
                    <select class="form-select form-select-sm" name="bina_id">
                        <option value="">Seçiniz...</option>
                        <option value="all" {{ $binaId === 'all' ? 'selected' : '' }}>Tümü</option>
                        @foreach($binalar as $bina)
                            <option value="{{ $bina->id }}" {{ $binaId == $bina->id ? 'selected' : '' }}>
                                {{ $bina->bina_adi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-4">
                    <label class="form-label small d-none d-md-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-sm w-100 mt-md-0 mt-1">
                        <i class="bi bi-search"></i> Ara
                    </button>
                </div>
            </div>
            @if($kayitlar && $binaId)
                <div class="row mt-3">
                    <div class="col-12">
                        <a href="{{ route('admin.raporlar.pdf', ['tarih_baslangic' => $tarihBaslangic, 'tarih_bitis' => $tarihBitis, 'bina_id' => $binaId]) }}" 
                           class="btn btn-danger btn-sm w-100" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> PDF İndir
                        </a>
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>

@if($kayitlar)
    <div class="content-card">
        <div class="content-card-body">
            <h5 class="mb-3">
                @if($tarihBaslangic === $tarihBitis)
                    {{ \Carbon\Carbon::parse($tarihBaslangic)->format('d.m.Y') }} Tarihli Kontroller
                @else
                    {{ \Carbon\Carbon::parse($tarihBaslangic)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($tarihBitis)->format('d.m.Y') }}
                @endif
            </h5>

            @if($kayitlar->count() > 0)
                @foreach($kayitlar as $binaAdi => $binaKayitlari)
                    <h6 class="mt-4 mb-3 text-primary">
                        <i class="bi bi-building"></i> {{ $binaAdi }}
                    </h6>
                    
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>Kontrol</th>
                                        <th width="200">Değer</th>
                                        <th width="150">Yapan</th>
                                        <th width="80">Saat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($binaKayitlari as $kayit)
                                        <tr>
                                            <td>{{ $kayit->kontrolMaddesi->kontrol_adi }}</td>
                                            <td>
                                                @if($kayit->kontrolMaddesi->kontrol_tipi == 'checkbox')
                                                    @if($kayit->girilen_deger == '1' || $kayit->durum)
                                                        <span class="badge bg-success">✓ Yapıldı</span>
                                                    @else
                                                        <span class="badge bg-danger">✗ Yapılmadı</span>
                                                    @endif
                                                @elseif($kayit->kontrolMaddesi->kontrol_tipi == 'sayisal')
                                                    {{ $kayit->girilen_deger }}
                                                    @if($kayit->kontrolMaddesi->birim)
                                                        <strong class="text-primary">{{ $kayit->kontrolMaddesi->birim }}</strong>
                                                    @endif
                                                @else
                                                    {{ $kayit->girilen_deger }}
                                                @endif
                                                
                                                @if($kayit->baslangic_saati || $kayit->bitis_saati)
                                                    <br>
                                                    <small class="text-muted">
                                                        @if($kayit->baslangic_saati)
                                                            <i class="bi bi-clock text-success"></i> {{ \Carbon\Carbon::parse($kayit->baslangic_saati)->format('H:i') }}
                                                        @endif
                                                        @if($kayit->baslangic_saati && $kayit->bitis_saati)
                                                            -
                                                        @endif
                                                        @if($kayit->bitis_saati)
                                                            <i class="bi bi-clock-fill text-danger"></i> {{ \Carbon\Carbon::parse($kayit->bitis_saati)->format('H:i') }}
                                                        @endif
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $kayit->yapanKullanici->ad }}</td>
                                            <td>{{ $kayit->created_at->format('H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-lg-none">
                        @foreach($binaKayitlari as $kayit)
                            <div class="card mb-2 shadow-sm">
                                <div class="card-body p-3">
                                    <h6 class="mb-2">{{ $kayit->kontrolMaddesi->kontrol_adi }}</h6>
                                    <div class="mb-2">
                                        @if($kayit->kontrolMaddesi->kontrol_tipi == 'checkbox')
                                            @if($kayit->girilen_deger == '1' || $kayit->durum)
                                                <span class="badge bg-success">✓ Yapıldı</span>
                                            @else
                                                <span class="badge bg-danger">✗ Yapılmadı</span>
                                            @endif
                                        @elseif($kayit->kontrolMaddesi->kontrol_tipi == 'sayisal')
                                            <span class="fs-5 text-primary">
                                                {{ $kayit->girilen_deger }}
                                                @if($kayit->kontrolMaddesi->birim)
                                                    {{ $kayit->kontrolMaddesi->birim }}
                                                @endif
                                            </span>
                                        @else
                                            {{ $kayit->girilen_deger }}
                                        @endif
                                    </div>
                                    @if($kayit->baslangic_saati || $kayit->bitis_saati)
                                        <small class="text-muted d-block mb-1">
                                            @if($kayit->baslangic_saati)
                                                <i class="bi bi-clock text-success"></i> {{ \Carbon\Carbon::parse($kayit->baslangic_saati)->format('H:i') }}
                                            @endif
                                            @if($kayit->baslangic_saati && $kayit->bitis_saati)
                                                -
                                            @endif
                                            @if($kayit->bitis_saati)
                                                <i class="bi bi-clock-fill text-danger"></i> {{ \Carbon\Carbon::parse($kayit->bitis_saati)->format('H:i') }}
                                            @endif
                                        </small>
                                    @endif
                                    <small class="text-muted">
                                        <i class="bi bi-person"></i> {{ $kayit->yapanKullanici->ad }} • 
                                        <i class="bi bi-clock"></i> {{ $kayit->created_at->format('H:i') }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Seçilen tarihte kontrol kaydı bulunamadı.
                </div>
            @endif
        </div>
    </div>
@else
    <div class="alert alert-secondary">
        <i class="bi bi-funnel"></i> Rapor görmek için yukarıdaki filtrelerden tarih ve bina seçiniz.
    </div>
@endif
@endsection
