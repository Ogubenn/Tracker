@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2><i class="fas fa-stethoscope"></i> Sistem Teşhis Paneli</h2>
            <p class="text-muted">Sistem bileşenlerinin durumunu kontrol edin</p>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-warning" id="clearCacheBtn">
                <i class="fas fa-broom"></i> Cache Temizle
            </button>
            <button type="button" class="btn btn-secondary" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Yenile
            </button>
            <a href="{{ route('admin.system-test.logout') }}" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Çıkış
            </a>
        </div>
    </div>

    <!-- PHP Bilgileri -->
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fab fa-php"></i> PHP Bilgileri</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>PHP Versiyonu:</strong><br>
                    <span class="badge bg-success">{{ $tests['php']['version'] }}</span>
                </div>
                <div class="col-md-9">
                    <strong>Memory Limit:</strong> {{ $tests['php']['ini']['memory_limit'] }} |
                    <strong>Max Execution Time:</strong> {{ $tests['php']['ini']['max_execution_time'] }}s |
                    <strong>Upload Max:</strong> {{ $tests['php']['ini']['upload_max_filesize'] }} |
                    <strong>Post Max:</strong> {{ $tests['php']['ini']['post_max_size'] }}
                </div>
            </div>
            <hr>
            <h6>Yüklü Eklentiler:</h6>
            <div class="row">
                @foreach($tests['php']['extensions'] as $ext => $loaded)
                    <div class="col-md-3 mb-2">
                        @if($loaded)
                            <i class="fas fa-check-circle text-success"></i>
                        @else
                            <i class="fas fa-times-circle text-danger"></i>
                        @endif
                        {{ $ext }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Veritabanı Bilgileri -->
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-database"></i> Veritabanı Bilgileri</h5>
        </div>
        <div class="card-body">
            @if($tests['database']['status'] === 'success')
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Bağlantı:</strong> 
                        <span class="badge bg-success">{{ $tests['database']['connection'] }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Driver:</strong> {{ $tests['database']['driver'] }}
                    </div>
                    <div class="col-md-4">
                        <strong>Database:</strong> {{ $tests['database']['database'] }}
                    </div>
                </div>
                
                <h6>Tablo İstatistikleri:</h6>
                <div class="row">
                    @foreach($tests['database']['stats'] as $table => $count)
                        <div class="col-md-2 mb-2">
                            <div class="card text-center">
                                <div class="card-body p-2">
                                    <h4 class="mb-0">{{ $count }}</h4>
                                    <small>{{ ucfirst($table) }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> {{ $tests['database']['message'] }}
                </div>
            @endif
        </div>
    </div>

    <!-- Model Testleri -->
    <div class="card mb-3">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-cubes"></i> Model Testleri</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Model</th>
                            <th>Durum</th>
                            <th>Kayıt Sayısı</th>
                            <th>Son Kayıt ID</th>
                            <th>Son Kayıt Tarihi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tests['models'] as $model => $data)
                            <tr>
                                <td><strong>{{ $model }}</strong></td>
                                <td>
                                    @if($data['status'] === 'success')
                                        <span class="badge bg-success"><i class="fas fa-check"></i> Çalışıyor</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-times"></i> Hata</span>
                                    @endif
                                </td>
                                <td>{{ $data['count'] ?? '-' }}</td>
                                <td>{{ $data['latest_id'] ?? '-' }}</td>
                                <td>{{ $data['latest_created'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Storage Testleri -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-folder-open"></i> Storage Dizinleri</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Dizin</th>
                                    <th>Var mı?</th>
                                    <th>Yazılabilir mi?</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tests['storage'] as $data)
                                    <tr>
                                        <td><code>{{ $data['directory'] }}</code></td>
                                        <td>
                                            @if($data['exists'])
                                                <i class="fas fa-check text-success"></i> Evet
                                            @else
                                                <i class="fas fa-times text-danger"></i> Hayır
                                            @endif
                                        </td>
                                        <td>
                                            @if($data['writable'])
                                                <i class="fas fa-check text-success"></i> Evet
                                            @else
                                                <i class="fas fa-times text-danger"></i> Hayır
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- PDF ve Mail Testleri -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Servisler</h5>
                </div>
                <div class="card-body">
                    <!-- DomPDF -->
                    <div class="mb-3">
                        <h6><i class="fas fa-file-pdf"></i> DomPDF</h6>
                        @if($tests['pdf']['status'] === 'success')
                            <div class="alert alert-success mb-2">
                                <i class="fas fa-check-circle"></i> {{ $tests['pdf']['message'] }}
                            </div>
                        @else
                            <div class="alert alert-danger mb-2">
                                <i class="fas fa-exclamation-triangle"></i> {{ $tests['pdf']['message'] }}
                            </div>
                        @endif
                    </div>

                    <hr>

                    <!-- Mail -->
                    <div class="mb-3">
                        <h6><i class="fas fa-envelope"></i> Mail Yapılandırması</h6>
                        @if($tests['mail']['status'] === 'success')
                            @if($tests['mail']['configured'])
                                <div class="alert alert-success mb-2">
                                    <i class="fas fa-check-circle"></i> Mail yapılandırması tamamlanmış
                                </div>
                                <small>
                                    <strong>Host:</strong> {{ $tests['mail']['config']['MAIL_HOST'] }}<br>
                                    <strong>Port:</strong> {{ $tests['mail']['config']['MAIL_PORT'] }}<br>
                                    <strong>From:</strong> {{ $tests['mail']['config']['MAIL_FROM_ADDRESS'] }}
                                </small>
                            @else
                                <div class="alert alert-warning mb-2">
                                    <i class="fas fa-exclamation-triangle"></i> Mail yapılandırması eksik
                                </div>
                            @endif
                        @else
                            <div class="alert alert-danger mb-2">
                                <i class="fas fa-times-circle"></i> {{ $tests['mail']['message'] }}
                            </div>
                        @endif
                    </div>

                    <hr>

                    <!-- Cache -->
                    <div>
                        <h6><i class="fas fa-memory"></i> Cache Sistemi</h6>
                        @if($tests['cache']['status'] === 'success')
                            <div class="alert alert-info mb-2">
                                <i class="fas fa-info-circle"></i> Cache Driver: <strong>{{ $tests['cache']['driver'] }}</strong>
                            </div>
                        @else
                            <div class="alert alert-danger mb-2">
                                <i class="fas fa-times-circle"></i> {{ $tests['cache']['message'] }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hızlı Linkler -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-link"></i> Hızlı Erişim</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.mail-test.index') }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-envelope-open-text"></i> Mail Test Paneli
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.mail-ayarlari.index') }}" class="btn btn-outline-info btn-sm w-100">
                        <i class="fas fa-cog"></i> Mail Ayarları
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-warning btn-sm w-100">
                        <i class="fas fa-history"></i> Aktivite Logları
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="/cron-trigger?key={{ config('app.cron_secret_key') }}" class="btn btn-outline-success btn-sm w-100" target="_blank">
                        <i class="fas fa-clock"></i> Cron Tetikle
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.card-header h5 {
    margin: 0;
}
</style>

<script>
document.getElementById('clearCacheBtn').addEventListener('click', function() {
    const btn = this;
    const originalHtml = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Temizleniyor...';
    
    fetch('{{ route('admin.system-test.clear-cache') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message + '\n\n' + data.details.join('\n'));
        } else {
            alert('❌ ' + data.message);
        }
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    })
    .catch(error => {
        alert('❌ Hata: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
});
</script>
@endsection
