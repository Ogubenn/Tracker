@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- İstatistik Kartları -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <h6>Binalar</h6>
                    <div class="stat-number">{{ $binaSayisi }}</div>
                </div>
                <div class="stat-card-icon blue">
                    <i class="bi bi-building"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <h6>Kontrol Maddeleri</h6>
                    <div class="stat-number">{{ $kontrolMaddesiSayisi }}</div>
                </div>
                <div class="stat-card-icon blue">
                    <i class="bi bi-check2-square"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <h6>Aktif Personel</h6>
                    <div class="stat-number">{{ $personelSayisi }}</div>
                </div>
                <div class="stat-card-icon green">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Bugün Yapılan Kontroller -->
    <div class="col-md-6">
        <div class="content-card">
            <div class="content-card-header">
                <h5><i class="bi bi-calendar-check me-2"></i>Bugün Yapılan Kontroller</h5>
            </div>
            <div class="content-card-body">
                @if($bugunYapilanKontroller > 0)
                    <div class="text-center py-3">
                        <div class="d-flex align-items-center justify-content-center gap-4 mb-3">
                            <div class="stat-number" style="font-size: 4rem; color: var(--primary); font-weight: 800;">
                                {{ $bugunYapilanKontroller }}
                            </div>
                            <div class="text-start">
                                <div class="fw-semibold text-dark mb-1" style="font-size: 1.125rem;">
                                    Kontrol Tamamlandı
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ now()->translatedFormat('d F Y') }}
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.kontrol-kayitlari.index', ['tarih_baslangic' => now()->format('Y-m-d'), 'tarih_bitis' => now()->format('Y-m-d')]) }}" 
                           class="btn btn-primary">
                            <i class="bi bi-eye me-2"></i>Kayıtları Görüntüle
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-clock-history text-muted" style="font-size: 3.5rem;"></i>
                        <p class="text-muted mt-3 mb-1 fw-semibold">Henüz kontrol yapılmadı</p>
                        <p class="text-muted small mb-0">Bugün için kontrol kaydı bulunmuyor</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Hızlı Erişim -->
    <div class="col-md-6">
        <div class="content-card">
            <div class="content-card-header">
                <h5><i class="bi bi-lightning-charge me-2"></i>Hızlı Erişim</h5>
            </div>
            <div class="content-card-body">
                <div class="d-flex flex-column gap-3">
                    <a href="{{ route('admin.binalar.create') }}" class="quick-action-btn">
                        <i class="bi bi-plus-circle"></i>
                        <span>Yeni Bina Ekle</span>
                    </a>
                    <a href="{{ route('admin.kontrol-maddeleri.create') }}" class="quick-action-btn">
                        <i class="bi bi-plus-circle"></i>
                        <span>Yeni Kontrol Maddesi Ekle</span>
                    </a>
                    <a href="{{ route('admin.raporlar.index') }}" class="quick-action-btn">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Raporları Görüntüle</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
