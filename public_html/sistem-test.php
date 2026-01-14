<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atıksu Takip Sistemi - Kapsamlı Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            padding: 30px; 
            text-align: center;
        }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .header p { opacity: 0.9; font-size: 14px; }
        .content { padding: 30px; }
        .section { 
            margin-bottom: 30px; 
            border: 2px solid #e0e0e0; 
            border-radius: 10px; 
            overflow: hidden;
        }
        .section-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            padding: 15px 20px; 
            font-size: 18px; 
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-body { padding: 20px; }
        .test-item { 
            padding: 12px 15px; 
            margin-bottom: 10px; 
            border-radius: 8px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            background: #f8f9fa;
        }
        .test-item:last-child { margin-bottom: 0; }
        .test-label { font-weight: 500; color: #333; flex: 1; }
        .test-value { 
            font-family: 'Courier New', monospace; 
            color: #555; 
            margin: 0 15px;
            flex: 2;
            word-break: break-all;
        }
        .badge { 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: bold; 
            text-transform: uppercase;
            min-width: 70px;
            text-align: center;
        }
        .badge-success { background: #4caf50; color: white; }
        .badge-error { background: #f44336; color: white; }
        .badge-warning { background: #ff9800; color: white; }
        .badge-info { background: #2196F3; color: white; }
        .code-block { 
            background: #2d2d2d; 
            color: #f8f8f2; 
            padding: 15px; 
            border-radius: 8px; 
            overflow-x: auto; 
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.5;
        }
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; 
            margin-top: 20px;
        }
        .stat-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center;
        }
        .stat-number { font-size: 32px; font-weight: bold; }
        .stat-label { font-size: 14px; opacity: 0.9; margin-top: 5px; }
        .footer { 
            background: #f5f5f5; 
            padding: 20px; 
            text-align: center; 
            color: #666; 
            font-size: 13px;
            border-top: 2px solid #e0e0e0;
        }
        .alert { 
            padding: 15px 20px; 
            border-radius: 8px; 
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        .alert-success { background: #e8f5e9; border-color: #4caf50; color: #2e7d32; }
        .alert-error { background: #ffebee; border-color: #f44336; color: #c62828; }
        .alert-warning { background: #fff3e0; border-color: #ff9800; color: #e65100; }
        .alert-info { background: #e3f2fd; border-color: #2196F3; color: #1565c0; }
        .btn { 
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 5px;
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background: #f5f5f5; font-weight: bold; color: #333; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Atıksu Takip Sistemi</h1>
            <p>Kapsamlı Sistem Test ve Diagnostic Panel</p>
            <p style="font-size: 12px; margin-top: 10px;">Test Zamanı: <?= date('d.m.Y H:i:s') ?></p>
        </div>

        <div class="content">
<?php
// ============================================
// 1. LARAVEL BOOTSTRAP TEST
// ============================================
$laravelBooted = false;
$laravelError = null;

try {
    define('LARAVEL_START', microtime(true));
    require __DIR__.'/../laravel/vendor/autoload.php';
    $app = require_once __DIR__.'/../laravel/bootstrap/app.php';
    
    // Public path'i zorla set et
    if (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['DOCUMENT_ROOT'], 'public_html') !== false) {
        $app->usePublicPath(realpath(__DIR__));
    }
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());
    
    $laravelBooted = true;
} catch (Exception $e) {
    $laravelError = $e->getMessage();
}

if ($laravelBooted): ?>
    <div class="alert alert-success">
        <strong>✅ Laravel Başarıyla Başlatıldı!</strong> Sistem çalışıyor.
    </div>
<?php else: ?>
    <div class="alert alert-error">
        <strong>❌ Laravel Başlatılamadı!</strong><br>
        <?= htmlspecialchars($laravelError) ?>
    </div>
<?php endif; ?>

<?php if ($laravelBooted): ?>

<!-- GENEL İSTATİSTİKLER -->
<?php
$dbConnected = false;
$binaCount = 0;
$maddelerCount = 0;
$kayitlarCount = 0;
$userCount = 0;

try {
    $dbConnected = DB::connection()->getPdo() !== null;
    $binaCount = DB::table('binalar')->count();
    $maddelerCount = DB::table('kontrol_maddeleri')->count();
    $kayitlarCount = DB::table('kontrol_kayitlari')->count();
    $userCount = DB::table('users')->count();
} catch (Exception $e) {
    $dbError = $e->getMessage();
}
?>

<div class="stats">
    <div class="stat-card">
        <div class="stat-number"><?= $binaCount ?></div>
        <div class="stat-label">Toplam Bina</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $maddelerCount ?></div>
        <div class="stat-label">Kontrol Maddesi</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $kayitlarCount ?></div>
        <div class="stat-label">Kontrol Kaydı</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $userCount ?></div>
        <div class="stat-label">Kullanıcı</div>
    </div>
</div>

<!-- 1. SUNUCU BİLGİLERİ -->
<div class="section">
    <div class="section-header">
        <span>🖥️ Sunucu Bilgileri</span>
        <span class="badge badge-info">Sistem</span>
    </div>
    <div class="section-body">
        <div class="test-item">
            <span class="test-label">PHP Versiyonu</span>
            <span class="test-value"><?= PHP_VERSION ?></span>
            <span class="badge <?= version_compare(PHP_VERSION, '8.1.0', '>=') ? 'badge-success' : 'badge-warning' ?>">
                <?= version_compare(PHP_VERSION, '8.1.0', '>=') ? 'OK' : 'UYARI' ?>
            </span>
        </div>
        <div class="test-item">
            <span class="test-label">Laravel Versiyonu</span>
            <span class="test-value"><?= app()->version() ?></span>
            <span class="badge badge-success">OK</span>
        </div>
        <div class="test-item">
            <span class="test-label">Server Software</span>
            <span class="test-value"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor' ?></span>
            <span class="badge badge-info">INFO</span>
        </div>
        <div class="test-item">
            <span class="test-label">Document Root</span>
            <span class="test-value"><?= $_SERVER['DOCUMENT_ROOT'] ?? 'Bilinmiyor' ?></span>
            <span class="badge badge-info">INFO</span>
        </div>
        <div class="test-item">
            <span class="test-label">Memory Limit</span>
            <span class="test-value"><?= ini_get('memory_limit') ?></span>
            <span class="badge badge-info">INFO</span>
        </div>
    </div>
</div>

<!-- 2. LARAVEL PATH YAPILANMASI -->
<div class="section">
    <div class="section-header">
        <span>📁 Laravel Path Yapılanması</span>
        <span class="badge badge-success">KONTROL</span>
    </div>
    <div class="section-body">
        <div class="test-item">
            <span class="test-label">Base Path</span>
            <span class="test-value"><?= base_path() ?></span>
            <span class="badge <?= file_exists(base_path()) ? 'badge-success' : 'badge-error' ?>">
                <?= file_exists(base_path()) ? 'VAR' : 'YOK' ?>
            </span>
        </div>
        <div class="test-item">
            <span class="test-label">Public Path</span>
            <span class="test-value"><?= public_path() ?></span>
            <span class="badge <?= file_exists(public_path()) ? 'badge-success' : 'badge-error' ?>">
                <?= file_exists(public_path()) ? 'VAR' : 'YOK' ?>
            </span>
        </div>
        <div class="test-item">
            <span class="test-label">Storage Path</span>
            <span class="test-value"><?= storage_path() ?></span>
            <span class="badge <?= file_exists(storage_path()) ? 'badge-success' : 'badge-error' ?>">
                <?= file_exists(storage_path()) ? 'VAR' : 'YOK' ?>
            </span>
        </div>
        <div class="test-item">
            <span class="test-label">Config Path</span>
            <span class="test-value"><?= config_path() ?></span>
            <span class="badge <?= file_exists(config_path()) ? 'badge-success' : 'badge-error' ?>">
                <?= file_exists(config_path()) ? 'VAR' : 'YOK' ?>
            </span>
        </div>
        <div class="test-item">
            <span class="test-label">Database Path</span>
            <span class="test-value"><?= database_path() ?></span>
            <span class="badge <?= file_exists(database_path()) ? 'badge-success' : 'badge-error' ?>">
                <?= file_exists(database_path()) ? 'VAR' : 'YOK' ?>
            </span>
        </div>
    </div>
</div>

<!-- 3. VERİTABANI BAĞLANTISI -->
<div class="section">
    <div class="section-header">
        <span>🗄️ Veritabanı Bağlantısı</span>
        <span class="badge <?= $dbConnected ? 'badge-success' : 'badge-error' ?>">
            <?= $dbConnected ? 'BAĞLI' : 'HATA' ?>
        </span>
    </div>
    <div class="section-body">
        <?php if ($dbConnected): ?>
            <div class="test-item">
                <span class="test-label">Bağlantı Durumu</span>
                <span class="test-value">Başarılı</span>
                <span class="badge badge-success">✓</span>
            </div>
            <div class="test-item">
                <span class="test-label">Database Driver</span>
                <span class="test-value"><?= config('database.default') ?></span>
                <span class="badge badge-info">INFO</span>
            </div>
            <div class="test-item">
                <span class="test-label">Database Name</span>
                <span class="test-value"><?= config('database.connections.mysql.database') ?></span>
                <span class="badge badge-info">INFO</span>
            </div>
            <div class="test-item">
                <span class="test-label">Database Host</span>
                <span class="test-value"><?= config('database.connections.mysql.host') ?></span>
                <span class="badge badge-info">INFO</span>
            </div>
            
            <h4 style="margin-top: 20px; margin-bottom: 10px;">Tablolar ve Kayıt Sayıları:</h4>
            <table>
                <thead>
                    <tr>
                        <th>Tablo Adı</th>
                        <th>Kayıt Sayısı</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $tables = ['binalar', 'kontrol_maddeleri', 'kontrol_kayitlari', 'users', 'migrations'];
                    foreach ($tables as $table):
                        try {
                            $count = DB::table($table)->count();
                            $exists = true;
                        } catch (Exception $e) {
                            $count = 0;
                            $exists = false;
                        }
                    ?>
                    <tr>
                        <td><strong><?= $table ?></strong></td>
                        <td><?= $exists ? number_format($count) : 'N/A' ?></td>
                        <td>
                            <span class="badge <?= $exists ? 'badge-success' : 'badge-error' ?>">
                                <?= $exists ? 'VAR' : 'YOK' ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-error">
                <strong>Veritabanı Bağlantı Hatası:</strong><br>
                <?= htmlspecialchars($dbError ?? 'Bilinmeyen hata') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 4. STORAGE İZİNLERİ -->
<div class="section">
    <div class="section-header">
        <span>🔒 Storage İzinleri</span>
        <span class="badge badge-warning">KRİTİK</span>
    </div>
    <div class="section-body">
        <?php
        $storageDirectories = [
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            'storage/fonts',
        ];
        
        foreach ($storageDirectories as $dir):
            $fullPath = base_path($dir);
            $exists = file_exists($fullPath);
            $writable = $exists && is_writable($fullPath);
            $permissions = $exists ? substr(sprintf('%o', fileperms($fullPath)), -4) : 'N/A';
        ?>
        <div class="test-item">
            <span class="test-label"><?= $dir ?></span>
            <span class="test-value">İzinler: <?= $permissions ?></span>
            <span class="badge <?= $writable ? 'badge-success' : 'badge-error' ?>">
                <?= $writable ? 'YAZILIR' : ($exists ? 'YAZILAMAZ' : 'YOK') ?>
            </span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- 5. MODEL İLİŞKİLERİ -->
<div class="section">
    <div class="section-header">
        <span>🔗 Model İlişkileri (Bina → KontrolMaddesi)</span>
        <span class="badge badge-info">YAPISI</span>
    </div>
    <div class="section-body">
        <?php
        try {
            // Bina model test
            $binaTest = App\Models\Bina::with('kontrolMaddeleri')->first();
            $binaModelOk = true;
            
            // KontrolMaddesi model test
            $maddeTest = App\Models\KontrolMaddesi::with('bina')->first();
            $maddeModelOk = true;
            
            // Bina_id kolonunu kontrol et
            $hasBinaId = Schema::hasColumn('kontrol_maddeleri', 'bina_id');
            $hasAlanId = Schema::hasColumn('kontrol_maddeleri', 'alan_id');
            
        } catch (Exception $e) {
            $modelError = $e->getMessage();
            $binaModelOk = false;
            $maddeModelOk = false;
        }
        ?>
        
        <div class="test-item">
            <span class="test-label">Bina Model</span>
            <span class="test-value"><?= $binaModelOk ? 'Çalışıyor' : 'Hata var' ?></span>
            <span class="badge <?= $binaModelOk ? 'badge-success' : 'badge-error' ?>">
                <?= $binaModelOk ? 'OK' : 'HATA' ?>
            </span>
        </div>
        
        <div class="test-item">
            <span class="test-label">KontrolMaddesi Model</span>
            <span class="test-value"><?= $maddeModelOk ? 'Çalışıyor' : 'Hata var' ?></span>
            <span class="badge <?= $maddeModelOk ? 'badge-success' : 'badge-error' ?>">
                <?= $maddeModelOk ? 'OK' : 'HATA' ?>
            </span>
        </div>
        
        <div class="test-item">
            <span class="test-label">kontrol_maddeleri.bina_id kolonu</span>
            <span class="test-value"><?= $hasBinaId ? 'Var (DOĞRU)' : 'Yok' ?></span>
            <span class="badge <?= $hasBinaId ? 'badge-success' : 'badge-error' ?>">
                <?= $hasBinaId ? '✓' : 'YOK' ?>
            </span>
        </div>
        
        <div class="test-item">
            <span class="test-label">kontrol_maddeleri.alan_id kolonu</span>
            <span class="test-value"><?= $hasAlanId ? 'Var (ESKİ)' : 'Yok (DOĞRU)' ?></span>
            <span class="badge <?= !$hasAlanId ? 'badge-success' : 'badge-warning' ?>">
                <?= !$hasAlanId ? '✓' : 'ESKİ' ?>
            </span>
        </div>
        
        <?php if ($binaModelOk && $binaTest): ?>
        <div style="margin-top: 15px;">
            <h4>İlk Bina ve Kontrol Maddeleri:</h4>
            <div class="code-block">
Bina: <?= $binaTest->bina_adi ?> (ID: <?= $binaTest->id ?>)
Kontrol Madde Sayısı: <?= $binaTest->kontrolMaddeleri->count() ?>

<?php foreach($binaTest->kontrolMaddeleri->take(3) as $madde): ?>
  - <?= $madde->kontrol_adi ?> (Bina ID: <?= $madde->bina_id ?>)
<?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- 6. DOMPDF TEST -->
<div class="section">
    <div class="section-header">
        <span>📄 DomPDF Kütüphanesi</span>
        <span class="badge badge-info">PDF</span>
    </div>
    <div class="section-body">
        <?php
        $dompdfExists = class_exists('Dompdf\Dompdf');
        $dompdfWorks = false;
        $pdfError = null;
        
        if ($dompdfExists) {
            try {
                $options = new \Dompdf\Options();
                $options->set('isRemoteEnabled', true);
                $options->set('fontDir', storage_path('fonts'));
                $options->set('fontCache', storage_path('fonts'));
                
                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml('<h1>Test</h1>');
                $dompdf->setPaper('A4');
                $dompdf->render();
                
                $dompdfWorks = true;
            } catch (Exception $e) {
                $pdfError = $e->getMessage();
            }
        }
        ?>
        
        <div class="test-item">
            <span class="test-label">DomPDF Sınıfı</span>
            <span class="test-value"><?= $dompdfExists ? 'Yüklü' : 'Yüklü değil' ?></span>
            <span class="badge <?= $dompdfExists ? 'badge-success' : 'badge-error' ?>">
                <?= $dompdfExists ? 'OK' : 'YOK' ?>
            </span>
        </div>
        
        <div class="test-item">
            <span class="test-label">PDF Oluşturma</span>
            <span class="test-value"><?= $dompdfWorks ? 'Çalışıyor' : 'Hata var' ?></span>
            <span class="badge <?= $dompdfWorks ? 'badge-success' : 'badge-error' ?>">
                <?= $dompdfWorks ? '✓' : 'HATA' ?>
            </span>
        </div>
        
        <div class="test-item">
            <span class="test-label">Font Klasörü</span>
            <span class="test-value"><?= storage_path('fonts') ?></span>
            <span class="badge <?= file_exists(storage_path('fonts')) ? 'badge-success' : 'badge-warning' ?>">
                <?= file_exists(storage_path('fonts')) ? 'VAR' : 'YOK' ?>
            </span>
        </div>
        
        <?php if ($dompdfWorks): ?>
        <div style="margin-top: 15px;">
            <a href="download-direct-pdf.php" class="btn">✓ Test PDF İndir</a>
        </div>
        <?php elseif ($pdfError): ?>
        <div class="alert alert-error" style="margin-top: 15px;">
            <strong>PDF Hatası:</strong> <?= htmlspecialchars($pdfError) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- 7. YAPILAN DEĞİŞİKLİKLER -->
<div class="section">
    <div class="section-header">
        <span>📝 Yapılan Önemli Değişiklikler</span>
        <span class="badge badge-info">LOG</span>
    </div>
    <div class="section-body">
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Değişiklik</th>
                    <th>Açıklama</th>
                    <th style="width: 15%;">Durum</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Alan Modeli Kaldırıldı</strong></td>
                    <td>Bina → Alan → KontrolMaddesi yerine Bina → KontrolMaddesi direkt ilişki</td>
                    <td><span class="badge badge-success">TAMAMLANDI</span></td>
                </tr>
                <tr>
                    <td><strong>Database Kolonu</strong></td>
                    <td>kontrol_maddeleri.alan_id → kontrol_maddeleri.bina_id olarak değiştirildi</td>
                    <td><span class="badge <?= $hasBinaId ? 'badge-success' : 'badge-error' ?>">
                        <?= $hasBinaId ? 'TAMAMLANDI' : 'BEKLEMEDE' ?>
                    </span></td>
                </tr>
                <tr>
                    <td><strong>DomPDF Facade</strong></td>
                    <td>Pdf::loadView() yerine Dompdf sınıfı doğrudan kullanılıyor</td>
                    <td><span class="badge <?= $dompdfWorks ? 'badge-success' : 'badge-warning' ?>">
                        <?= $dompdfWorks ? 'ÇALIŞIYOR' : 'TEST ET' ?>
                    </span></td>
                </tr>
                <tr>
                    <td><strong>Public Path Fix</strong></td>
                    <td>bootstrap/app.php'de production sunucu için public path düzeltmesi</td>
                    <td><span class="badge badge-success">TAMAMLANDI</span></td>
                </tr>
                <tr>
                    <td><strong>Rapor Tarihleri</strong></td>
                    <td>Sadece günlük → Tarih aralığı seçimi eklendi</td>
                    <td><span class="badge badge-success">TAMAMLANDI</span></td>
                </tr>
                <tr>
                    <td><strong>Admin Kullanıcı</strong></td>
                    <td>Email: admin@atiksu.com / Password: admin123</td>
                    <td><span class="badge badge-info">OLUŞTURULDU</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- 8. ENVIRONMENT CONFIG -->
<div class="section">
    <div class="section-header">
        <span>⚙️ Environment Ayarları</span>
        <span class="badge badge-info">CONFIG</span>
    </div>
    <div class="section-body">
        <div class="test-item">
            <span class="test-label">APP_ENV</span>
            <span class="test-value"><?= config('app.env') ?></span>
            <span class="badge badge-info">
                <?= strtoupper(config('app.env')) ?>
            </span>
        </div>
        <div class="test-item">
            <span class="test-label">APP_DEBUG</span>
            <span class="test-value"><?= config('app.debug') ? 'Açık' : 'Kapalı' ?></span>
            <span class="badge <?= config('app.debug') ? 'badge-warning' : 'badge-success' ?>">
                <?= config('app.debug') ? 'DEV' : 'PROD' ?>
            </span>
        </div>
        <div class="test-item">
            <span class="test-label">APP_URL</span>
            <span class="test-value"><?= config('app.url') ?></span>
            <span class="badge badge-info">INFO</span>
        </div>
        <div class="test-item">
            <span class="test-label">Timezone</span>
            <span class="test-value"><?= config('app.timezone') ?></span>
            <span class="badge badge-info">INFO</span>
        </div>
        <div class="test-item">
            <span class="test-label">Locale</span>
            <span class="test-value"><?= config('app.locale') ?></span>
            <span class="badge badge-info">INFO</span>
        </div>
    </div>
</div>

<!-- 9. HIZLI ERİŞİM LİNKLERİ -->
<div class="section">
    <div class="section-header">
        <span>🔗 Hızlı Erişim</span>
        <span class="badge badge-info">LINKLER</span>
    </div>
    <div class="section-body" style="text-align: center;">
        <a href="/" class="btn">🏠 Ana Sayfa</a>
        <a href="/admin/binalar" class="btn">🏢 Binalar</a>
        <a href="/admin/kontrol-maddeleri" class="btn">📋 Kontrol Maddeleri</a>
        <a href="/admin/raporlar" class="btn">📊 Raporlar</a>
        <a href="/login" class="btn">🔐 Giriş</a>
        <a href="?refresh=1" class="btn">🔄 Yenile</a>
    </div>
</div>

<?php endif; ?>

<?php if (!$laravelBooted): ?>
<div class="section">
    <div class="section-header">
        <span>❌ Hata Detayları</span>
        <span class="badge badge-error">HATA</span>
    </div>
    <div class="section-body">
        <div class="code-block">
<?= htmlspecialchars($laravelError) ?>
        </div>
        <div style="margin-top: 20px;">
            <h4>Olası Çözümler:</h4>
            <ul style="margin-top: 10px; line-height: 2;">
                <li>vendor/ klasörünün tam yüklendiğinden emin olun</li>
                <li>.env dosyasının doğru yapılandırıldığından emin olun</li>
                <li>storage/ klasörü izinlerinin 777 olduğundan emin olun</li>
                <li>clear-all.php çalıştırarak cache'i temizleyin</li>
            </ul>
        </div>
    </div>
</div>
<?php endif; ?>

        </div>

        <div class="footer">
            <strong>Atıksu Takip Sistemi</strong> - Kapsamlı Test Panel<br>
            Bu dosyayı güvenlik nedeniyle production'da silmeyi unutmayın!<br>
            <small>Dosya: <?= __FILE__ ?></small>
        </div>
    </div>

<?php if ($laravelBooted): $kernel->terminate($request, $response); endif; ?>
</body>
</html>
