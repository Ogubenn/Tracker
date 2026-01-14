@extends('layouts.app')

@section('title', 'Raporlar')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Raporlar</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.raporlar.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="tarih_baslangic" class="form-label">
                    <i class="bi bi-calendar-event"></i> Başlangıç Tarihi
                </label>
                <input type="date" class="form-control" id="tarih_baslangic" name="tarih_baslangic" 
                       value="{{ $tarihBaslangic }}" required>
            </div>

            <div class="col-md-3">
                <label for="tarih_bitis" class="form-label">
                    <i class="bi bi-calendar-check"></i> Bitiş Tarihi
                </label>
                <input type="date" class="form-control" id="tarih_bitis" name="tarih_bitis" 
                       value="{{ $tarihBitis }}" required>
            </div>

            <div class="col-md-4">
                <label for="bina_id" class="form-label">
                    <i class="bi bi-building"></i> Bina
                </label>
                <select class="form-select" id="bina_id" name="bina_id">
                    <option value="">Seçiniz...</option>
                    <option value="all" {{ $binaId === 'all' ? 'selected' : '' }}>📊 Tümü</option>
                    @foreach($binalar as $bina)
                        <option value="{{ $bina->id }}" {{ $binaId == $bina->id ? 'selected' : '' }}>
                            {{ $bina->bina_adi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Ara
                </button>
            </div>

            @if($kayitlar && $binaId)
                <div class="col-md-12 text-end mt-3">
                    <a href="{{ route('admin.raporlar.pdf', ['tarih_baslangic' => $tarihBaslangic, 'tarih_bitis' => $tarihBitis, 'bina_id' => $binaId]) }}" 
                       class="btn btn-danger" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> PDF Olarak İndir
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

@if($kayitlar)
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                @if($tarihBaslangic === $tarihBitis)
                    {{ \Carbon\Carbon::parse($tarihBaslangic)->format('d.m.Y') }} Tarihli Kontroller
                @else
                    {{ \Carbon\Carbon::parse($tarihBaslangic)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($tarihBitis)->format('d.m.Y') }} Arası Kontroller
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if($kayitlar->count() > 0)
                @foreach($kayitlar as $binaAdi => $binaKayitlari)
                    <h6 class="mt-3 mb-3"><strong>{{ $binaAdi }}</strong></h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Kontrol</th>
                                    <th>Değer</th>
                                    <th>Yapan</th>
                                    <th>Saat</th>
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
