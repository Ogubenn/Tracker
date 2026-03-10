@extends('layouts.app')

@section('title', 'Rapor Detayı')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="bi bi-clipboard2-data"></i> Rapor Detayı</h1>
        <a href="{{ route('admin.laboratuvar.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Geri
        </a>
    </div>
</div>

<div class="row">
    <!-- Rapor Bilgileri -->
    <div class="col-lg-4 mb-4">
        <div class="content-card">
            <div class="content-card-header">
                <h5><i class="bi bi-info-circle"></i> Rapor Bilgileri</h5>
            </div>
            <div class="content-card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Rapor No:</th>
                        <td><strong>{{ $rapor->rapor_no }}</strong></td>
                    </tr>
                    <tr>
                        <th>Rapor Tarihi:</th>
                        <td>{{ \Carbon\Carbon::parse($rapor->rapor_tarihi)->format('d.m.Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tesis Adı:</th>
                        <td>{{ $rapor->tesis_adi }}</td>
                    </tr>
                    <tr>
                        <th>Numunenin Cinsi ve Adı:</th>
                        <td>{{ $rapor->numune_cinsi_adi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Numune Alma Noktası ve Sayısı:</th>
                        <td>{{ $rapor->numune_alma_noktasi_sayisi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Numune Alma Başlangıç:</th>
                        <td>{{ $rapor->numune_alma_tarihi ? \Carbon\Carbon::parse($rapor->numune_alma_tarihi)->format('d.m.Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Numune Alma Bitiş:</th>
                        <td>{{ $rapor->numune_alma_tarihi_bitis ? \Carbon\Carbon::parse($rapor->numune_alma_tarihi_bitis)->format('d.m.Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Numune Alma Şekli:</th>
                        <td>{{ $rapor->numune_alma_sekli ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Numune Geliş Şekli:</th>
                        <td>{{ $rapor->numune_gelis_sekli ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Numune Ambalaj:</th>
                        <td>{{ $rapor->numune_ambalaj ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Numune Numarası:</th>
                        <td>{{ $rapor->numune_numarasi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lab Geliş Tarihi:</th>
                        <td>{{ $rapor->lab_gelis_tarihi ? \Carbon\Carbon::parse($rapor->lab_gelis_tarihi)->format('d.m.Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Şahit Numune:</th>
                        <td>{{ $rapor->sahit_numune ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Analiz Başlangıç:</th>
                        <td>{{ $rapor->analiz_baslangic ? \Carbon\Carbon::parse($rapor->analiz_baslangic)->format('d.m.Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Analiz Bitiş:</th>
                        <td>{{ $rapor->analiz_bitis ? \Carbon\Carbon::parse($rapor->analiz_bitis)->format('d.m.Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Oluşturan:</th>
                        <td>{{ $rapor->olusturan->ad ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Oluşturma Tarihi:</th>
                        <td>{{ $rapor->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                </table>

                @if($rapor->notlar)
                <div class="alert alert-info mt-3">
                    <strong><i class="bi bi-sticky"></i> Notlar:</strong><br>
                    {{ $rapor->notlar }}
                </div>
                @endif

                @if($rapor->hasPdf())
                <div class="mt-3">
                    <a href="{{ $rapor->getPdfUrl() }}" target="_blank" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-file-pdf"></i> PDF'i Görüntüle
                    </a>
                </div>
                @endif

                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('admin.laboratuvar.edit', $rapor->id) }}" class="btn btn-warning btn-sm flex-fill">
                        <i class="bi bi-pencil"></i> Düzenle
                    </a>
                    <form action="{{ route('admin.laboratuvar.destroy', $rapor->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Bu raporu silmek istediğinizden emin misiniz?')"
                          class="flex-fill">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="bi bi-trash"></i> Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Analiz Parametreleri -->
    <div class="col-lg-8 mb-4">
        <div class="content-card">
            <div class="content-card-header">
                <h5><i class="bi bi-list-check"></i> Analiz Parametreleri ({{ $rapor->parametreler->count() }})</h5>
            </div>
            <div class="content-card-body">
                @if($rapor->parametreler->isEmpty())
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Bu rapor için henüz parametre eklenmemiş.
                    </div>
                @else
                    <div class="table-wrapper">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Parametre</th>
                                    <th>Birim</th>
                                    <th>Sonuç</th>
                                    <th>Limit</th>
                                    <th>Uygunluk</th>
                                    <th>Metod</th>
                                    <th>Tablo No</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rapor->parametreler as $param)
                                <tr>
                                    <td><strong>{{ $param->parametre_adi }}</strong></td>
                                    <td>{{ $param->birim ?? '-' }}</td>
                                    <td><strong>{{ $param->analiz_sonucu }}</strong></td>
                                    <td>{{ $param->limit_degeri ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $param->getUygunlukBadgeClass() }}">
                                            {{ $param->getUygunlukText() }}
                                        </span>
                                    </td>
                                    <td>{{ $param->analiz_metodu ?? '-' }}</td>
                                    <td>{{ $param->tablo_no ?? '-' }}</td>
                                </tr>
                                @if($param->notlar)
                                <tr>
                                    <td colspan="7" class="bg-light">
                                        <small><i class="bi bi-sticky"></i> <em>{{ $param->notlar }}</em></small>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Özet İstatistikler -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-card-content">
                                    <div class="stat-card-info">
                                        <h6>Toplam Parametre</h6>
                                        <div class="stat-number">{{ $rapor->parametreler->count() }}</div>
                                    </div>
                                    <div class="stat-card-icon blue">
                                        <i class="bi bi-list-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-card-content">
                                    <div class="stat-card-info">
                                        <h6>Uygun</h6>
                                        <div class="stat-number">{{ $rapor->parametreler->where('uygunluk', 'uygun')->count() }}</div>
                                    </div>
                                    <div class="stat-card-icon green">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-card-content">
                                    <div class="stat-card-info">
                                        <h6>Uygun Değil</h6>
                                        <div class="stat-number">{{ $rapor->parametreler->where('uygunluk', 'uygun_degil')->count() }}</div>
                                    </div>
                                    <div class="stat-card-icon yellow">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
