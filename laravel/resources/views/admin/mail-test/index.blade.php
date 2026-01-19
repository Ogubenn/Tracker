@extends('layouts.app')

@section('title', 'Mail Sistemi Test Paneli')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Başlık ve Bilgi Kartı -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="bi bi-envelope-check me-2"></i>Mail Sistemi Test Paneli</h5>
                            <small>Kapsamlı mail ve cron job test merkezi</small>
                        </div>
                        <a href="{{ route('admin.mail-ayarlari.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-gear me-1"></i>Mail Ayarları
                        </a>
                    </div>
                </div>
            </div>

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

            <div class="row">
                <!-- Sol Kolon: Sistem Durumu -->
                <div class="col-lg-4 mb-4">
                    <!-- SMTP Konfigürasyonu -->
                    <div class="card shadow-sm mb-4 h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-server me-2"></i>SMTP Konfigürasyonu</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;"><i class="bi bi-globe2 me-1"></i>Host:</td>
                                    <td><code>{{ $smtpConfig['mail_host'] }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bi bi-plug me-1"></i>Port:</td>
                                    <td><code>{{ $smtpConfig['mail_port'] }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bi bi-shield-lock me-1"></i>Encryption:</td>
                                    <td><span class="badge bg-success">{{ strtoupper($smtpConfig['mail_encryption']) }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bi bi-person me-1"></i>Username:</td>
                                    <td><code>{{ $smtpConfig['mail_username'] }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bi bi-envelope me-1"></i>From:</td>
                                    <td><code>{{ $smtpConfig['mail_from_address'] }}</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Sistem İstatistikleri -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Sistem İstatistikleri</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <h3 class="mb-0 text-primary">{{ $stats['toplam_kullanici'] }}</h3>
                                        <small class="text-muted">Aktif Kullanıcı</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <h3 class="mb-0 text-success">{{ $stats['mail_alan_kullanici'] }}</h3>
                                        <small class="text-muted">Mail Alan</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <h3 class="mb-0 text-info">{{ $stats['toplam_kontrol_maddesi'] }}</h3>
                                        <small class="text-muted">Kontrol Maddesi</small>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Eksik Kontrol Maili:</span>
                                @if($stats['eksik_kontrol_aktif'])
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Pasif</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Toplu Rapor Maili:</span>
                                @if($stats['toplu_rapor_aktif'])
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Pasif</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Bilgileri -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Zamanlanmış Görevler</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-sunrise text-warning me-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <strong>Sabah Bildirimi</strong><br>
                                        <code class="text-primary">{{ $scheduleInfo['sabah_saat'] }}</code>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-sunset text-danger me-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <strong>Akşam Uyarısı</strong><br>
                                        <code class="text-primary">{{ $scheduleInfo['aksam_saat'] }}</code>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-file-earmark-text text-info me-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <strong>Toplu Rapor</strong><br>
                                        <code class="text-primary">{{ $scheduleInfo['rapor_saat'] }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon: Test İşlemleri -->
                <div class="col-lg-8">
                    <!-- 1. SMTP Bağlantı Testi -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-send-check me-2"></i>1. SMTP Bağlantı Testi</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                SMTP sunucusuna bağlantıyı ve mail gönderim işlevini test eder. Basit bir test maili gönderir.
                            </p>
                            <form method="POST" action="{{ route('admin.mail-test.smtp') }}" class="row g-3">
                                @csrf
                                <div class="col-12 col-md-8">
                                    <label for="test_email" class="form-label">Test Email Adresi</label>
                                    <input type="email" class="form-control" id="test_email" name="test_email" 
                                           placeholder="test@example.com" required>
                                    <small class="text-muted">Mail gönderim testini yapacağınız email adresi</small>
                                </div>
                                <div class="col-12 col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-send me-2"></i>SMTP Test Gönder
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 2. Manuel Scheduled Mail Testleri -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="bi bi-envelope-paper me-2"></i>2. Scheduled Mail Manuel Test</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Zamanlanmış mail komutlarını manuel olarak tetikler. Gerçek kullanıcılara gerçek mail gönderir.
                            </p>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="card border-primary h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-sunrise text-warning" style="font-size: 3rem;"></i>
                                            <h6 class="mt-3">Sabah Bildirimi</h6>
                                            <p class="small text-muted">Günlük kontrol listesi gönderir</p>
                                            <form method="POST" action="{{ route('admin.mail-test.scheduled') }}" onsubmit="return confirm('Sabah eksik kontrol maili gönderilecek. Emin misiniz?')">
                                                @csrf
                                                <input type="hidden" name="mail_type" value="sabah">
                                                <button type="submit" class="btn btn-outline-primary w-100">
                                                    <i class="bi bi-play-fill me-1"></i>Çalıştır
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="card border-warning h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-sunset text-danger" style="font-size: 3rem;"></i>
                                            <h6 class="mt-3">Akşam Uyarısı</h6>
                                            <p class="small text-muted">Eksik kontrol hatırlatması</p>
                                            <form method="POST" action="{{ route('admin.mail-test.scheduled') }}" onsubmit="return confirm('Akşam eksik kontrol maili gönderilecek. Emin misiniz?')">
                                                @csrf
                                                <input type="hidden" name="mail_type" value="aksam">
                                                <button type="submit" class="btn btn-outline-warning w-100">
                                                    <i class="bi bi-play-fill me-1"></i>Çalıştır
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="card border-info h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-file-earmark-text text-info" style="font-size: 3rem;"></i>
                                            <h6 class="mt-3">Toplu Rapor</h6>
                                            <p class="small text-muted">Günlük özet rapor gönderir</p>
                                            <form method="POST" action="{{ route('admin.mail-test.scheduled') }}" onsubmit="return confirm('Günlük toplu rapor maili gönderilecek. Emin misiniz?')">
                                                @csrf
                                                <input type="hidden" name="mail_type" value="rapor">
                                                <button type="submit" class="btn btn-outline-info w-100">
                                                    <i class="bi bi-play-fill me-1"></i>Çalıştır
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Cron Job Testi -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="bi bi-gear me-2"></i>3. Cron Job (Schedule:run) Testi</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Laravel scheduler'ı manuel çalıştırır. Saat uygun ise otomatik mail gönderir.
                            </p>
                            <form method="POST" action="{{ route('admin.mail-test.cron') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-play-circle me-2"></i>Schedule:run Çalıştır
                                </button>
                            </form>
                            <a href="{{ $cronInfo['cron_url'] }}" target="_blank" class="btn btn-outline-secondary">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Cron URL Test (Yeni Sekme)
                            </a>
                        </div>
                    </div>

                    <!-- 4. Cron Job Kurulum Bilgileri -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0"><i class="bi bi-terminal me-2"></i>4. Cron Job Kurulum</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>External Cron Service için URL:</strong>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control font-monospace small" 
                                       value="{{ $cronInfo['cron_url'] }}" readonly id="cronUrl">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">
                                    <i class="bi bi-clipboard me-1"></i>Kopyala
                                </button>
                            </div>

                            <div class="alert alert-warning mb-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>cPanel Cron Job Komutu:</strong>
                            </div>
                            <div class="bg-dark text-light p-3 rounded">
                                <code class="text-light">cd /home/KULLANICI/public_html && php artisan schedule:run >> /dev/null 2>&1</code>
                            </div>

                            <div class="mt-3">
                                <a href="https://cron-job.org" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Cron-Job.org
                                </a>
                                <a href="{{ route('admin.mail-ayarlari.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-gear me-1"></i>Detaylı Kurulum Talimatları
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
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    border: none;
    border-radius: 15px;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    padding: 1rem 1.5rem;
}

.btn {
    border-radius: 10px;
}
</style>

<script>
function copyToClipboard() {
    const cronUrl = document.getElementById('cronUrl');
    cronUrl.select();
    cronUrl.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    alert('✅ Cron URL kopyalandı!');
}
</script>
@endsection
