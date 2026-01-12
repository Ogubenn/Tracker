@extends('layouts.app')

@section('title', 'Raporlar')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Raporlar</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.raporlar.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="tarih" class="form-label">Tarih</label>
                <input type="date" class="form-control" id="tarih" name="tarih" value="{{ $tarih }}">
            </div>

            <div class="col-md-6">
                <label for="bina_id" class="form-label">Bina</label>
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
        </form>
    </div>
</div>

@if($kayitlar)
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ \Carbon\Carbon::parse($tarih)->format('d.m.Y') }} Tarihli Kontroller</h5>
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
                                                @if($kayit->girilen_deger == '1')
                                                    <span class="badge bg-success">✓ Yapıldı</span>
                                                @else
                                                    <span class="badge bg-danger">✗ Yapılmadı</span>
                                                @endif
                                            @else
                                                {{ $kayit->girilen_deger }}
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
