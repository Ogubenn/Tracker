@extends('layouts.app')

@section('title', 'İstatistikler')

@section('content')
<div class="page-header">
    <h1>İstatistikler</h1>
</div>

<div class="content-card">
    <div class="content-card-body text-center py-5">
        <div class="empty-state">
            <i class="bi bi-bar-chart-line" style="font-size: 5rem; color: var(--gray-300);"></i>
            <h3 class="mt-4 mb-3">İstatistikler Proje Aşamasında</h3>
            <p class="text-muted mb-4">
                Bu sayfa şu anda geliştirilme aşamasındadır.<br>
                Yakında detaylı istatistikler ve grafikler burada görüntülenecektir.
            </p>
            <div class="alert alert-info d-inline-block">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Gelecek Özellikler:</strong>
                <ul class="text-start mt-2 mb-0">
                    <li>Günlük/Haftalık/Aylık kontrol istatistikleri</li>
                    <li>Bina bazında performans grafikleri</li>
                    <li>Personel performans raporları</li>
                    <li>Trend analizleri ve tahminler</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
