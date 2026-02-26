@extends('layouts.app')

@section('title', 'Personel Devam - Aylık Rapor')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-calendar-month"></i> Aylık Devam Raporu
        </h1>
        <div>
            <a href="{{ route('admin.personel-devam.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-calendar-week"></i> Haftalık Görünüm
            </a>
            <a href="{{ route('admin.personel-devam.pdf', ['yil' => $tarih->year, 'ay' => $tarih->month]) }}" 
               class="btn btn-danger"
               target="_blank">
                <i class="bi bi-file-pdf"></i> PDF İndir
            </a>
        </div>
    </div>

    <!-- Ay Navigasyonu -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.personel-devam.aylik', ['yil' => $oncekiAy->year, 'ay' => $oncekiAy->month]) }}" 
                   class="btn btn-outline-primary">
                    <i class="bi bi-chevron-left"></i> Önceki Ay
                </a>
                
                <h4 class="mb-0">
                    {{ $tarih->translatedFormat('F Y') }}
                </h4>
                
                <a href="{{ route('admin.personel-devam.aylik', ['yil' => $sonrakiAy->year, 'ay' => $sonrakiAy->month]) }}" 
                   class="btn btn-outline-primary">
                    Sonraki Ay <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Aylık Devam Tablosu -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" style="font-size: 0.85rem;">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" style="min-width: 120px; vertical-align: middle;">Personel</th>
                            <th colspan="{{ count($gunler) }}" class="text-center">Günler</th>
                            <th colspan="4" class="text-center">Özet</th>
                        </tr>
                        <tr>
                            @foreach($gunler as $gun)
                                <th class="text-center" style="min-width: 35px;">
                                    {{ $gun->format('d') }}
                                    <br>
                                    <small style="font-size: 0.7rem;">{{ $gun->translatedFormat('D') }}</small>
                                </th>
                            @endforeach
                            <th class="text-center" style="background-color: #28a745; color: white;">Ç</th>
                            <th class="text-center" style="background-color: #17a2b8; color: white;">İ</th>
                            <th class="text-center" style="background-color: #ffc107; color: white;">R</th>
                            <th class="text-center" style="background-color: #dc3545; color: white;">G</th>
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
                                        $haftaSonu = in_array($gun->dayOfWeek, [0, 6]); // Pazar=0, Cumartesi=6
                                    @endphp
                                    
                                    <td class="text-center p-1" 
                                        style="background-color: {{ $haftaSonu ? '#f8f9fa' : 'white' }};">
                                        @if($kayit)
                                            @if($kayit->durum == 'calisma')
                                                <span style="color: #28a745;" title="Çalıştı">
                                                    @if($kayit->giris_yapti && $kayit->cikis_yapti)
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    @elseif($kayit->giris_yapti)
                                                        <i class="bi bi-arrow-right-circle"></i>
                                                    @elseif($kayit->cikis_yapti)
                                                        <i class="bi bi-arrow-left-circle"></i>
                                                    @else
                                                        <i class="bi bi-dash-circle"></i>
                                                    @endif
                                                </span>
                                            @elseif($kayit->durum == 'izinli')
                                                <span style="color: #17a2b8;" title="İzinli">İ</span>
                                            @elseif($kayit->durum == 'raporlu')
                                                <span style="color: #ffc107;" title="Raporlu">R</span>
                                            @elseif($kayit->durum == 'gelmedi')
                                                <span style="color: #dc3545;" title="Gelmedi">G</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                
                                <!-- Özet -->
                                <td class="text-center fw-bold" style="background-color: #d4edda;">
                                    {{ $istatistikler[$personel->id]['calisma'] }}
                                </td>
                                <td class="text-center fw-bold" style="background-color: #d1ecf1;">
                                    {{ $istatistikler[$personel->id]['izin'] }}
                                </td>
                                <td class="text-center fw-bold" style="background-color: #fff3cd;">
                                    {{ $istatistikler[$personel->id]['rapor'] }}
                                </td>
                                <td class="text-center fw-bold" style="background-color: #f8d7da;">
                                    {{ $istatistikler[$personel->id]['gelmedi'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <h6>Açıklamalar:</h6>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li><i class="bi bi-check-circle-fill text-success"></i> Giriş ve Çıkış yapıldı</li>
                            <li><i class="bi bi-arrow-right-circle text-success"></i> Sadece Giriş yapıldı</li>
                            <li><i class="bi bi-arrow-left-circle text-success"></i> Sadece Çıkış yapıldı</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li><span class="badge bg-info">İ</span> İzinli</li>
                            <li><span class="badge bg-warning">R</span> Raporlu</li>
                            <li><span class="badge bg-danger">G</span> Gelmedi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-responsive {
    max-height: 70vh;
    overflow: auto;
}

.table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}

.table tbody td:first-child {
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
