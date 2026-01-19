@extends('layouts.app')

@section('title', 'Mail Bildirim Ayarları')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="bi bi-envelope-gear me-2"></i>Mail Bildirim Ayarları</h5>
                            <small>Otomatik mail gönderim zamanlarını ve bildirim türlerini yönetin</small>
                        </div>
                        <a href="{{ route('admin.mail-test.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-speedometer2 me-1"></i>Test Paneli
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Mail Bildirim Ayarları Formu -->
                    <form method="POST" action="{{ route('admin.mail-ayarlari.update') }}">
                        @csrf

                        <!-- Eksik Kontrol Bildirimleri -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Eksik Kontrol Bildirimleri</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="eksik_kontrol_mail_aktif" 
                                           id="eksik_kontrol_mail_aktif" value="1"
                                           {{ $ayarlar['eksik_kontrol_mail_aktif'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="eksik_kontrol_mail_aktif">
                                        <strong>Eksik Kontrol Maillerini Gönder</strong>
                                        <p class="text-muted small mb-0">Aktif edildiğinde personele otomatik bildirim maili gönderilir</p>
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="eksik_kontrol_sabah_saat" class="form-label">
                                            <i class="bi bi-sunrise me-1"></i>Sabah Bildirimi Saati
                                        </label>
                                        <input type="time" class="form-control form-control-lg" name="eksik_kontrol_sabah_saat" 
                                               id="eksik_kontrol_sabah_saat" 
                                               value="{{ $ayarlar['eksik_kontrol_sabah_saat'] }}" required>
                                        <small class="text-muted">Personele bugün yapılacak kontroller bildirilir</small>
                                    </div>

                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="eksik_kontrol_aksam_saat" class="form-label">
                                            <i class="bi bi-moon me-1"></i>Akşam Uyarısı Saati
                                        </label>
                                        <input type="time" class="form-control form-control-lg" name="eksik_kontrol_aksam_saat" 
                                               id="eksik_kontrol_aksam_saat" 
                                               value="{{ $ayarlar['eksik_kontrol_aksam_saat'] }}" required>
                                        <small class="text-muted">Eksik kontroller personele hatırlatılır</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Toplu Rapor Bildirimleri -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-file-earmark-text text-info me-2"></i>Toplu Rapor Bildirimleri</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="toplu_rapor_mail_aktif" 
                                           id="toplu_rapor_mail_aktif" value="1"
                                           {{ $ayarlar['toplu_rapor_mail_aktif'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="toplu_rapor_mail_aktif">
                                        <strong>Günlük Rapor Maillerini Gönder</strong>
                                        <p class="text-muted small mb-0">Aktif edildiğinde admin kullanıcılara günlük özet gönderilir</p>
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="toplu_rapor_saat" class="form-label">
                                            <i class="bi bi-clock me-1"></i>Rapor Gönderim Saati
                                        </label>
                                        <input type="time" class="form-control form-control-lg" name="toplu_rapor_saat" 
                                               id="toplu_rapor_saat" 
                                               value="{{ $ayarlar['toplu_rapor_saat'] }}" required>
                                        <small class="text-muted">Admin kullanıcılara günlük özet rapor gönderilir</small>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Rapor İçeriği</label>
                                        <div class="alert alert-info mb-0">
                                            <ul class="mb-0 small">
                                                <li>Tamamlanan kontroller</li>
                                                <li>Eksik kalan kontroller</li>
                                                <li>Tamamlanma yüzdesi</li>
                                                <li>Personel performansı</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kullanıcı Yönetimi Bilgisi -->
                        <div class="card border-primary shadow-sm mb-4">
                            <div class="card-header bg-primary bg-opacity-10">
                                <h6 class="mb-0"><i class="bi bi-people text-primary me-2"></i>Mail Alıcıları Yönetimi</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Hangi kullanıcıların mail alacağını <strong>Kullanıcılar</strong> sayfasından yönetebilirsiniz. 
                                    Her kullanıcı için "Mail Alsın" seçeneğini aktif/pasif yapabilirsiniz.
                                </p>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-people me-1"></i>Kullanıcıları Yönet
                                </a>
                            </div>
                        </div>

                        <!-- Kaydet Butonu -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.mail-test.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-speedometer2 me-1"></i>Test Paneline Git
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-2"></i>Ayarları Kaydet
                            </button>
                        </div>
                    </form>

                    <hr class="my-5">

                    <!-- SMTP Bilgileri (Sadece Gösterim) -->
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary bg-opacity-10">
                            <h6 class="mb-0"><i class="bi bi-server text-secondary me-2"></i>SMTP Yapılandırması</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                SMTP ayarları <code>.env</code> dosyasından okunmaktadır. Değişiklik yapmak için sunucu erişimi gereklidir.
                            </p>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <strong class="small text-muted d-block">Host</strong>
                                    <code class="text-primary">{{ $smtpConfig['mail_host'] ?? 'Tanımlı değil' }}</code>
                                </div>
                                <div class="col-6 col-md-3">
                                    <strong class="small text-muted d-block">Port</strong>
                                    <code class="text-primary">{{ $smtpConfig['mail_port'] ?? 'Tanımlı değil' }}</code>
                                </div>
                                <div class="col-6 col-md-3">
                                    <strong class="small text-muted d-block">Encryption</strong>
                                    <code class="text-primary">{{ strtoupper($smtpConfig['mail_encryption'] ?? 'Yok') }}</code>
                                </div>
                                <div class="col-12 col-md-3">
                                    <strong class="small text-muted d-block">From Address</strong>
                                    <code class="text-primary">{{ $smtpConfig['mail_from_address'] ?? 'Tanımlı değil' }}</code>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.mail-test.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-gear me-1"></i>Test & Detaylı Bilgiler
                                </a>
                            </div>
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

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.btn {
    border-radius: 10px;
}

.form-control-lg {
    font-size: 1.1rem;
    padding: 0.75rem;
}
</style>
@endsection
