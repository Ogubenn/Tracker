<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zamanlanmış Görevler Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .container { 
            max-width: 900px; 
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
        .header h1 { font-size: 28px; margin-bottom: 10px; }
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
        }
        .section-body { padding: 20px; }
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
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover { opacity: 0.9; }
        .btn-success { background: linear-gradient(135deg, #4caf50 0%, #45a049 100%); }
        .code-block { 
            background: #2d2d2d; 
            color: #f8f8f2; 
            padding: 15px; 
            border-radius: 8px; 
            overflow-x: auto; 
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
        }
        .task-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        .task-card h3 { margin-bottom: 10px; color: #333; }
        .task-card p { margin-bottom: 5px; color: #666; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background: #f5f5f5; font-weight: bold; }
        .badge { 
            padding: 4px 10px; 
            border-radius: 12px; 
            font-size: 11px; 
            font-weight: bold; 
        }
        .badge-success { background: #4caf50; color: white; }
        .badge-error { background: #f44336; color: white; }
        .badge-warning { background: #ff9800; color: white; }
        .badge-info { background: #2196F3; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Zamanlanmış Görevler Test Panel</h1>
            <p>Cron Jobs & Scheduled Tasks</p>
        </div>

        <div class="content">
<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

if (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['DOCUMENT_ROOT'], 'public_html') !== false) {
    $app->usePublicPath(realpath(__DIR__));
}

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArrayInput([]),
    $output = new Symfony\Component\Console\Output\BufferedOutput()
);

// Manuel çalıştırma işlemi
$runTask = $_GET['run'] ?? null;
$taskOutput = null;
$taskError = null;

if ($runTask) {
    try {
        $artisan = Artisan::getFacadeRoot();
        
        switch ($runTask) {
            case 'schedule:run':
                Artisan::call('schedule:run');
                $taskOutput = Artisan::output();
                break;
            case 'eksik-mail-sabah':
                Artisan::call('kontrol:eksik-mail', ['zaman' => 'sabah']);
                $taskOutput = Artisan::output();
                break;
            case 'eksik-mail-aksam':
                Artisan::call('kontrol:eksik-mail', ['zaman' => 'aksam']);
                $taskOutput = Artisan::output();
                break;
            case 'toplu-rapor':
                Artisan::call('kontrol:toplu-rapor');
                $taskOutput = Artisan::output();
                break;
            case 'schedule:list':
                Artisan::call('schedule:list');
                $taskOutput = Artisan::output();
                break;
        }
    } catch (Exception $e) {
        $taskError = $e->getMessage();
    }
}

// Site ayarlarını kontrol et
try {
    $siteAyarlari = DB::table('site_ayarlari')->pluck('deger', 'anahtar')->toArray();
    $eksikKontrolAktif = ($siteAyarlari['eksik_kontrol_mail_aktif'] ?? 'false') === 'true';
    $topluRaporAktif = ($siteAyarlari['toplu_rapor_mail_aktif'] ?? 'false') === 'true';
    $sabahSaat = $siteAyarlari['eksik_kontrol_sabah_saat'] ?? '07:00';
    $aksamSaat = $siteAyarlari['eksik_kontrol_aksam_saat'] ?? '19:00';
} catch (Exception $e) {
    $siteAyarlari = [];
    $eksikKontrolAktif = false;
    $topluRaporAktif = false;
    $sabahSaat = '07:00';
    $aksamSaat = '19:00';
}

// Command'ların varlığını kontrol et
$commandsExist = true;
try {
    $allCommands = Artisan::all();
    $hasEksikMail = isset($allCommands['kontrol:eksik-mail']);
    $hasTopluRapor = isset($allCommands['kontrol:toplu-rapor']);
} catch (Exception $e) {
    $commandsExist = false;
    $hasEksikMail = false;
    $hasTopluRapor = false;
}
?>

<?php if ($runTask && $taskOutput): ?>
    <div class="alert alert-success">
        <strong>✅ Görev Başarıyla Çalıştırıldı!</strong>
    </div>
    <div class="section">
        <div class="section-header">📋 Çıktı</div>
        <div class="section-body">
            <div class="code-block"><?= htmlspecialchars($taskOutput) ?: 'Çıktı yok' ?></div>
        </div>
    </div>
<?php elseif ($runTask && $taskError): ?>
    <div class="alert alert-error">
        <strong>❌ Görev Çalıştırılamadı!</strong><br>
        <?= htmlspecialchars($taskError) ?>
    </div>
<?php endif; ?>

<!-- TANIMLI GÖREVLER -->
<div class="section">
    <div class="section-header">📅 Tanımlı Zamanlanmış Görevler</div>
    <div class="section-body">
        
        <div class="task-card">
            <h3>🌅 1. Sabah Eksik Kontrol Maili</h3>
            <p><strong>Çalışma Saati:</strong> Her gün saat <?= $sabahSaat ?></p>
            <p><strong>Durum:</strong> 
                <span class="badge <?= $eksikKontrolAktif ? 'badge-success' : 'badge-error' ?>">
                    <?= $eksikKontrolAktif ? 'AKTİF' : 'PASİF' ?>
                </span>
            </p>
            <p><strong>Açıklama:</strong> Sabah yapılması gereken ama yapılmamış kontrolleri mail olarak gönderir.</p>
            <p><strong>Command:</strong> <code>kontrol:eksik-mail sabah</code></p>
            <div style="margin-top: 10px;">
                <a href="?run=eksik-mail-sabah" class="btn btn-success">▶ Manuel Çalıştır</a>
            </div>
        </div>

        <div class="task-card">
            <h3>🌙 2. Akşam Eksik Kontrol Maili</h3>
            <p><strong>Çalışma Saati:</strong> Her gün saat <?= $aksamSaat ?></p>
            <p><strong>Durum:</strong> 
                <span class="badge <?= $eksikKontrolAktif ? 'badge-success' : 'badge-error' ?>">
                    <?= $eksikKontrolAktif ? 'AKTİF' : 'PASİF' ?>
                </span>
            </p>
            <p><strong>Açıklama:</strong> Akşam yapılması gereken ama yapılmamış kontrolleri mail olarak gönderir.</p>
            <p><strong>Command:</strong> <code>kontrol:eksik-mail aksam</code></p>
            <div style="margin-top: 10px;">
                <a href="?run=eksik-mail-aksam" class="btn btn-success">▶ Manuel Çalıştır</a>
            </div>
        </div>

        <div class="task-card">
            <h3>📊 3. Toplu Günlük Rapor Maili</h3>
            <p><strong>Çalışma Saati:</strong> Her gün saat 19:00</p>
            <p><strong>Durum:</strong> 
                <span class="badge <?= $topluRaporAktif ? 'badge-success' : 'badge-error' ?>">
                    <?= $topluRaporAktif ? 'AKTİF' : 'PASİF' ?>
                </span>
            </p>
            <p><strong>Açıklama:</strong> Günlük tüm kontrol raporu mail ile gönderilir.</p>
            <p><strong>Command:</strong> <code>kontrol:toplu-rapor</code></p>
            <div style="margin-top: 10px;">
                <a href="?run=toplu-rapor" class="btn btn-success">▶ Manuel Çalıştır</a>
            </div>
        </div>

    </div>
</div>

<!-- COMMAND KONTROL -->
<div class="section">
    <div class="section-header">🔧 Artisan Command Kontrolü</div>
    <div class="section-body">
        <table>
            <thead>
                <tr>
                    <th>Command</th>
                    <th>Durum</th>
                    <th>Açıklama</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>kontrol:eksik-mail</code></td>
                    <td>
                        <span class="badge <?= $hasEksikMail ? 'badge-success' : 'badge-error' ?>">
                            <?= $hasEksikMail ? 'VAR' : 'YOK' ?>
                        </span>
                    </td>
                    <td>Eksik kontrolleri mail gönderen komut</td>
                </tr>
                <tr>
                    <td><code>kontrol:toplu-rapor</code></td>
                    <td>
                        <span class="badge <?= $hasTopluRapor ? 'badge-success' : 'badge-error' ?>">
                            <?= $hasTopluRapor ? 'VAR' : 'YOK' ?>
                        </span>
                    </td>
                    <td>Toplu rapor mail gönderen komut</td>
                </tr>
            </tbody>
        </table>

        <?php if (!$hasEksikMail || !$hasTopluRapor): ?>
        <div class="alert alert-error" style="margin-top: 20px;">
            <strong>⚠️ Uyarı:</strong> Bazı command dosyaları eksik! 
            <code>app/Console/Commands/</code> klasörünü kontrol edin.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- CRON JOB KURULUMU -->
<div class="section">
    <div class="section-header">⚙️ Cron Job Kurulumu (DirectAdmin)</div>
    <div class="section-body">
        <div class="alert alert-warning">
            <strong>⚠️ Önemli:</strong> Zamanlanmış görevlerin otomatik çalışması için DirectAdmin'de cron job kurulmalı!
        </div>

        <h3 style="margin-top: 20px; margin-bottom: 10px;">DirectAdmin Kurulum Adımları:</h3>
        <ol style="line-height: 2; margin-left: 20px;">
            <li><strong>DirectAdmin → Advanced Features → Cron Jobs</strong> açın</li>
            <li><strong>"Add New Cron Job"</strong> tıklayın</li>
            <li>Aşağıdaki ayarları girin:</li>
        </ol>

        <table style="margin-top: 15px;">
            <tr>
                <th style="width: 30%;">Alan</th>
                <th>Değer</th>
            </tr>
            <tr>
                <td><strong>Minute</strong></td>
                <td><code>*</code> (her dakika)</td>
            </tr>
            <tr>
                <td><strong>Hour</strong></td>
                <td><code>*</code> (her saat)</td>
            </tr>
            <tr>
                <td><strong>Day</strong></td>
                <td><code>*</code> (her gün)</td>
            </tr>
            <tr>
                <td><strong>Month</strong></td>
                <td><code>*</code> (her ay)</td>
            </tr>
            <tr>
                <td><strong>Weekday</strong></td>
                <td><code>*</code> (her gün)</td>
            </tr>
            <tr>
                <td><strong>Command</strong></td>
                <td><code>/usr/local/bin/php /home/ogubenn/domains/xn--atksutakip-yub.com.tr/laravel/artisan schedule:run >> /dev/null 2>&1</code></td>
            </tr>
        </table>

        <div class="alert alert-info" style="margin-top: 20px;">
            <strong>💡 Not:</strong> Cron job dakikada bir <code>schedule:run</code> komutunu çalıştırır. 
            Laravel otomatik olarak sadece zamanı gelen görevleri çalıştırır.
        </div>

        <h3 style="margin-top: 20px; margin-bottom: 10px;">Alternatif: Her Görev için Ayrı Cron</h3>
        <div class="code-block">
# Sabah 07:00 - Eksik kontrol maili
0 7 * * * /usr/local/bin/php /home/ogubenn/.../artisan kontrol:eksik-mail sabah

# Akşam 19:00 - Eksik kontrol maili  
0 19 * * * /usr/local/bin/php /home/ogubenn/.../artisan kontrol:eksik-mail aksam

# Akşam 19:00 - Toplu rapor
0 19 * * * /usr/local/bin/php /home/ogubenn/.../artisan kontrol:toplu-rapor
        </div>
    </div>
</div>

<!-- SCHEDULE:RUN TEST -->
<div class="section">
    <div class="section-header">🧪 Schedule:Run Testi</div>
    <div class="section-body">
        <p>Laravel'in zamanlanmış görevlerini şimdi çalıştırarak test edin:</p>
        <div style="margin-top: 15px;">
            <a href="?run=schedule:run" class="btn">▶ Schedule:Run Çalıştır</a>
            <a href="?run=schedule:list" class="btn">📋 Görev Listesini Göster</a>
        </div>
        
        <div class="alert alert-info" style="margin-top: 20px;">
            <strong>💡 Not:</strong> <code>schedule:run</code> sadece zamanı gelen görevleri çalıştırır. 
            Manuel test için yukarıdaki "Manuel Çalıştır" butonlarını kullanın.
        </div>
    </div>
</div>

<!-- SİTE AYARLARI -->
<div class="section">
    <div class="section-header">⚙️ İlgili Site Ayarları</div>
    <div class="section-body">
        <table>
            <thead>
                <tr>
                    <th>Ayar</th>
                    <th>Değer</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Eksik Kontrol Mail Aktif</strong></td>
                    <td>
                        <span class="badge <?= $eksikKontrolAktif ? 'badge-success' : 'badge-error' ?>">
                            <?= $eksikKontrolAktif ? 'AÇIK' : 'KAPALI' ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Toplu Rapor Mail Aktif</strong></td>
                    <td>
                        <span class="badge <?= $topluRaporAktif ? 'badge-success' : 'badge-error' ?>">
                            <?= $topluRaporAktif ? 'AÇIK' : 'KAPALI' ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Sabah Kontrol Saati</strong></td>
                    <td><code><?= $sabahSaat ?></code></td>
                </tr>
                <tr>
                    <td><strong>Akşam Kontrol Saati</strong></td>
                    <td><code><?= $aksamSaat ?></code></td>
                </tr>
            </tbody>
        </table>

        <div class="alert alert-info" style="margin-top: 20px;">
            <strong>ℹ️ Bilgi:</strong> Bu ayarları admin panelden 
            <strong>Site Ayarları</strong> bölümünden değiştirebilirsiniz.
        </div>
    </div>
</div>

<!-- HIZLI İŞLEMLER -->
<div class="section">
    <div class="section-header">⚡ Hızlı İşlemler</div>
    <div class="section-body" style="text-align: center;">
        <a href="?" class="btn">🔄 Sayfayı Yenile</a>
        <a href="/admin/site-ayarlari" class="btn">⚙️ Site Ayarları</a>
        <a href="sistem-test.php" class="btn">🧪 Genel Test</a>
    </div>
</div>

        </div>

        <div style="background: #f5f5f5; padding: 20px; text-align: center; color: #666; font-size: 13px; border-top: 2px solid #e0e0e0;">
            <strong>⏰ Zamanlanmış Görevler Test Panel</strong><br>
            Production'da bu dosyayı silmeyi unutmayın!<br>
            <small>Test Zamanı: <?= date('d.m.Y H:i:s') ?></small>
        </div>
    </div>

<?php $kernel->terminate($input, $status); ?>
</body>
</html>
