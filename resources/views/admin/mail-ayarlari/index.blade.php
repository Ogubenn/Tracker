@extends('layouts.app')

@section('title', 'Mail Ayarları')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="bi bi-envelope-gear me-2"></i>
                    <h5 class="mb-0">Mail Bildirim Ayarları</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.mail-ayarlari.update') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Eksik Kontrol Bildirimleri</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="eksik_kontrol_mail_aktif" 
                                                   id="eksik_kontrol_mail_aktif" value="1"
                                                   {{ $ayarlar['eksik_kontrol_mail_aktif'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="eksik_kontrol_mail_aktif">
                                                <strong>Eksik Kontrol Maillerini Gönder</strong>
                                            </label>
                                        </div>

                                        <div class="mb-3">
                                            <label for="eksik_kontrol_sabah_saat" class="form-label">
                                                <i class="bi bi-sunrise me-1"></i>Sabah Bildirimi Saati
                                            </label>
                                            <input type="time" class="form-control" name="eksik_kontrol_sabah_saat" 
                                                   id="eksik_kontrol_sabah_saat" 
                                                   value="{{ $ayarlar['eksik_kontrol_sabah_saat'] }}" required>
                                            <small class="text-muted">Personele bugün yapılacak kontroller bildirilir</small>
                                        </div>

                                        <div class="mb-0">
                                            <label for="eksik_kontrol_aksam_saat" class="form-label">
                                                <i class="bi bi-moon me-1"></i>Akşam Uyarısı Saati
                                            </label>
                                            <input type="time" class="form-control" name="eksik_kontrol_aksam_saat" 
                                                   id="eksik_kontrol_aksam_saat" 
                                                   value="{{ $ayarlar['eksik_kontrol_aksam_saat'] }}" required>
                                            <small class="text-muted">Eksik kontroller personele hatırlatılır</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-file-earmark-text text-info me-2"></i>Toplu Rapor Bildirimleri</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="toplu_rapor_mail_aktif" 
                                                   id="toplu_rapor_mail_aktif" value="1"
                                                   {{ $ayarlar['toplu_rapor_mail_aktif'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="toplu_rapor_mail_aktif">
                                                <strong>Günlük Rapor Maillerini Gönder</strong>
                                            </label>
                                        </div>

                                        <div class="mb-3">
                                            <label for="toplu_rapor_saat" class="form-label">
                                                <i class="bi bi-clock me-1"></i>Rapor Gönderim Saati
                                            </label>
                                            <input type="time" class="form-control" name="toplu_rapor_saat" 
                                                   id="toplu_rapor_saat" 
                                                   value="{{ $ayarlar['toplu_rapor_saat'] }}" required>
                                            <small class="text-muted">Admin kullanıcılara günlük özet rapor gönderilir</small>
                                        </div>

                                        <div class="alert alert-info mb-0">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Rapor İçeriği:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Tamamlanan kontroller</li>
                                                <li>Eksik kalan kontroller</li>
                                                <li>Tamamlanma yüzdesi</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-people text-success me-2"></i>Mail Alıcıları</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-2">
                                    <i class="bi bi-arrow-right me-2"></i>
                                    Hangi kullanıcıların mail alacağını <strong>Kullanıcılar</strong> sayfasından yönetebilirsiniz.
                                </p>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-people me-1"></i>Kullanıcıları Yönet
                                </a>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Ayarları Kaydet
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="card border-warning">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h6 class="mb-0 text-warning">
                                <i class="bi bi-gear me-2"></i>Scheduled Tasks (Zamanlanmış Görevler)
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Mail gönderimi için Laravel Scheduler çalışmalıdır:</strong></p>
                            <div class="bg-dark text-light p-3 rounded">
                                <code>* * * * * cd c:\laragon\www\atiksu_takip && php artisan schedule:run >> /dev/null 2>&1</code>
                            </div>
                            <p class="text-muted mt-2 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                <strong>Development:</strong> Manuel test için: 
                                <code>php artisan kontrol:eksik-mail sabah</code> veya 
                                <code>php artisan kontrol:toplu-rapor</code>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.card {
    border: none;
    border-radius: 15px;
}
</style>
@endsection
