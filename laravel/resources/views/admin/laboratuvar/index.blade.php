@extends('layouts.app')

@section('title', 'Laboratuvar Analiz Raporları')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-clipboard2-data"></i> Laboratuvar Analiz Raporları
        </h1>
        <div>
            <a href="{{ route('admin.laboratuvar.grafikler') }}" class="btn btn-info me-2">
                <i class="bi bi-graph-up"></i> Grafikler
            </a>
            <a href="{{ route('admin.laboratuvar.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Yeni Rapor
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

    @if(session('hatalar'))
        <div class="alert alert-warning alert-dismissible fade show">
            <strong>Import Hataları:</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('hatalar') as $hata)
                    <li>{{ $hata }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtreler -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtrele</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laboratuvar.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Başlangıç Tarihi</label>
                        <input type="date" name="baslangic_tarihi" class="form-control" value="{{ request('baslangic_tarihi') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bitiş Tarihi</label>
                        <input type="date" name="bitis_tarihi" class="form-control" value="{{ request('bitis_tarihi') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tesis Adı</label>
                        <input type="text" name="tesis" class="form-control" placeholder="Tesis ara..." value="{{ request('tesis') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Rapor No</label>
                        <input type="text" name="rapor_no" class="form-control" placeholder="Rapor No..." value="{{ request('rapor_no') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Ara
                        </button>
                        <a href="{{ route('admin.laboratuvar.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Temizle
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Raporlar Listesi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Rapor No</th>
                            <th>Rapor Tarihi</th>
                            <th>Tesis Adı</th>
                            <th>Parametre Sayısı</th>
                            <th>Uygun Olmayan</th>
                            <th>PDF</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($raporlar as $rapor)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.laboratuvar.show', $rapor->id) }}" class="fw-bold text-primary">
                                        {{ $rapor->rapor_no }}
                                    </a>
                                </td>
                                <td>{{ $rapor->rapor_tarihi->format('d.m.Y') }}</td>
                                <td>{{ Str::limit($rapor->tesis_adi, 40) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $rapor->parametreler->count() }}</span>
                                </td>
                                <td>
                                    @php
                                        $uygunOlmayanSayisi = $rapor->parametreler->where('uygunluk', 'uygun_degil')->count();
                                    @endphp
                                    @if($uygunOlmayanSayisi > 0)
                                        <span class="badge bg-danger">{{ $uygunOlmayanSayisi }}</span>
                                    @else
                                        <span class="badge bg-success">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rapor->hasPdf())
                                        <a href="{{ $rapor->getPdfUrl() }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.laboratuvar.show', $rapor->id) }}" class="btn btn-outline-primary" title="Görüntüle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.laboratuvar.edit', $rapor->id) }}" class="btn btn-outline-warning" title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.laboratuvar.destroy', $rapor->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Bu raporu silmek istediğinizden emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Sil">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2">Henüz rapor bulunmuyor.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $raporlar->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
