<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\BinaController;
use App\Http\Controllers\Admin\KontrolMaddesiController;
use App\Http\Controllers\Admin\KontrolKaydiController;
use App\Http\Controllers\Admin\GecmisTarihKontrolController;
use App\Http\Controllers\Admin\MailAyarlariController;
use App\Http\Controllers\Admin\MailTestController;
use App\Http\Controllers\Admin\RaporController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\IstatistiklerController;
use App\Http\Controllers\Admin\SystemTestController;
use App\Http\Controllers\Admin\IsTakvimiController;
use App\Http\Controllers\Admin\ArsivlenmisIsController;
use App\Http\Controllers\Admin\PersonelDevamController;
use App\Http\Controllers\Admin\LaboratuvarController;
use App\Http\Controllers\Admin\BinaCalismaDurumuController;
use App\Http\Controllers\PublicKontrolController;
use App\Http\Controllers\Personel\DashboardController as PersonelDashboard;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Migration Trigger Route (Geliştirme için)
// Kullanım: http://localhost/run-migration?key=atiksu2026
Route::get('/run-migration', function () {
    $secretKey = 'atiksu2026';
    
    if (request('key') !== $secretKey) {
        abort(403, 'Yetkisiz erişim');
    }
    
    try {
        Artisan::call('migrate', [
            '--force' => true
        ]);
        $output = Artisan::output();
        
        return response("<html>
            <head>
                <title>Migration Sonucu</title>
                <style>
                    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
                    .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; color: #155724; }
                    .output { background: #fff; padding: 15px; border-radius: 5px; margin-top: 20px; white-space: pre-wrap; font-family: monospace; }
                    h2 { margin: 0 0 10px 0; }
                </style>
            </head>
            <body>
                <div class='success'>
                    <h2>✅ Migration Başarıyla Çalıştırıldı</h2>
                    <p>Tarih: " . now()->format('d.m.Y H:i:s') . "</p>
                </div>
                <div class='output'>" . htmlspecialchars($output) . "</div>
                <p>
                    <a href='/admin/mail-ayarlari'>Mail Ayarları Sayfasına Git →</a><br>
                    <a href='/admin/arsivlenmis-isler'>Arşivlenmiş İşler Sayfasına Git →</a>
                </p>
            </body>
        </html>");
    } catch (\Exception $e) {
        return response("<html>
            <head>
                <title>Migration Hatası</title>
                <style>
                    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
                    .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; color: #721c24; }
                    h2 { margin: 0 0 10px 0; }
                </style>
            </head>
            <body>
                <div class='error'>
                    <h2>❌ Migration Hatası</h2>
                    <p>" . htmlspecialchars($e->getMessage()) . "</p>
                </div>
            </body>
        </html>", 500);
    }
});

// Bina ID nullable fix (TEK SEFERLIK)
Route::get('/fix-bina-nullable', function () {
    if (request('key') !== 'atiksu2026') {
        abort(403, 'Yetkisiz erişim');
    }
    
    try {
        DB::statement('ALTER TABLE `arsivlenmis_isler` MODIFY COLUMN `bina_id` bigint(20) UNSIGNED NULL');
        
        // Foreign key constraint'i yeniden oluştur
        try {
            DB::statement('ALTER TABLE `arsivlenmis_isler` DROP FOREIGN KEY `arsivlenmis_isler_bina_id_foreign`');
        } catch (\Exception $e) {
            // Foreign key yoksa devam et
        }
        
        DB::statement('ALTER TABLE `arsivlenmis_isler` ADD CONSTRAINT `arsivlenmis_isler_bina_id_foreign` FOREIGN KEY (`bina_id`) REFERENCES `binalar` (`id`) ON DELETE SET NULL');
        
        return response("<html>
            <head><title>Başarılı</title>
            <style>body{font-family:Arial;padding:20px;background:#f5f5f5;}
            .success{background:#d4edda;border:1px solid #c3e6cb;padding:20px;border-radius:8px;color:#155724;}</style>
            </head>
            <body>
                <div class='success'>
                    <h2>✅ Bina ID Nullable Yapıldı</h2>
                    <p>Artık bina olmadan iş ekleyebilirsiniz!</p>
                    <p><a href='/admin/arsivlenmis-isler'>Arşivlenmiş İşler Sayfasına Git →</a></p>
                </div>
            </body>
        </html>");
    } catch (\Exception $e) {
        return response("<html>
            <head><title>Hata</title>
            <style>body{font-family:Arial;padding:20px;background:#f5f5f5;}
            .error{background:#f8d7da;border:1px solid #f5c6cb;padding:20px;border-radius:8px;color:#721c24;}</style>
            </head>
            <body>
                <div class='error'>
                    <h2>❌ Hata</h2>
                    <p>" . htmlspecialchars($e->getMessage()) . "</p>
                </div>
            </body>
        </html>", 500);
    }
});

// Cron Trigger Route (External Cron Services için)
// Kullanım: https://siteniz.com/cron-trigger?key=GIZLI_ANAHTAR
Route::get('/cron-trigger', function () {
    $secretKey = env('CRON_SECRET_KEY', 'atiksu_cron_2026_secret');
    
    if (request('key') !== $secretKey) {
        abort(403, 'Unauthorized cron access');
    }
    
    try {
        Artisan::call('schedule:run');
        $output = Artisan::output();
        
        return response()->json([
            'success' => true,
            'message' => 'Scheduled tasks executed successfully',
            'output' => $output,
            'timestamp' => now()->toDateTimeString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

// Migration route (sadece acil durumlar için, sonra silinmeli)
Route::get('/migrate-run', function () {
    if (request('key') !== 'atiksu2026') {
        abort(403, 'Unauthorized');
    }
    
    try {
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();
        return response("<pre>Migration başarılı!\n\n$output</pre>");
    } catch (\Exception $e) {
        return response("<pre>Hata: " . $e->getMessage() . "</pre>", 500);
    }
});

// Storage link route (sadece bir kere çalıştırılmalı, sonra silinmeli)
Route::get('/storage-link', function () {
    if (request('key') !== 'atiksu2026') {
        abort(403, 'Unauthorized');
    }
    
    try {
        // public_html/storage mevcut mu kontrol et
        $linkPath = public_path('storage');
        
        if (file_exists($linkPath)) {
            // Eski linki/klasörü sil
            if (is_link($linkPath)) {
                unlink($linkPath);
            } else {
                // Klasörse içini temizle ve sil
                exec("rm -rf " . escapeshellarg($linkPath));
            }
        }
        
        // Laravel klasörünün tam yolunu al
        $storagePath = storage_path('app/public');
        
        // Symlink oluştur
        if (symlink($storagePath, $linkPath)) {
            $output = "✓ Symlink başarıyla oluşturuldu!\n\n";
            $output .= "Link: " . $linkPath . "\n";
            $output .= "Hedef: " . $storagePath . "\n\n";
            
            // Test et
            if (is_link($linkPath) && file_exists($linkPath)) {
                $output .= "✓ Symlink çalışıyor!\n";
                $output .= "✓ Hedef dizine erişilebilir: " . realpath($linkPath);
            } else {
                $output .= "⚠ Symlink oluşturuldu ama test başarısız!";
            }
            
            return response("<pre>$output</pre>");
        } else {
            throw new \Exception("Symlink oluşturulamadı! Sunucu izinlerini kontrol edin.");
        }
    } catch (\Exception $e) {
        return response("<pre>Hata: " . $e->getMessage() . "\n\n" . 
                       "Link: " . public_path('storage') . "\n" .
                       "Hedef: " . storage_path('app/public') . "</pre>", 500);
    }
});

// Cache temizleme route (sadece acil durumlar için)
Route::get('/cache-clear', function () {
    if (request('key') !== 'atiksu2026') {
        abort(403, 'Unauthorized');
    }
    
    try {
        $output = "=== CACHE TEMİZLEME ===\n\n";
        
        Artisan::call('config:clear');
        $output .= "✓ Config cache temizlendi\n";
        
        Artisan::call('cache:clear');
        $output .= "✓ Application cache temizlendi\n";
        
        Artisan::call('route:clear');
        $output .= "✓ Route cache temizlendi\n";
        
        Artisan::call('view:clear');
        $output .= "✓ View cache temizlendi\n";
        
        // Session dosyalarını temizle
        $sessionPath = storage_path('framework/sessions');
        if (file_exists($sessionPath)) {
            $files = glob($sessionPath . '/*');
            $count = 0;
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    @unlink($file);
                    $count++;
                }
            }
            $output .= "✓ {$count} session dosyası temizlendi\n";
        }
        
        $output .= "\n✅ Tüm cache'ler başarıyla temizlendi!";
        return response("<pre>$output</pre>");
    } catch (\Exception $e) {
        return response("<pre>❌ Hata: " . $e->getMessage() . "</pre>", 500);
    }
});

// Fotoğrafları public_html/storage'a taşı (TEK SEFERLIK)
Route::get('/move-photos', function () {
    if (request('key') !== 'atiksu2026') {
        abort(403, 'Unauthorized');
    }
    
    try {
        // Yeni storage yolu: public_html/storage
        $newStoragePath = public_path('storage');
        
        // Eski storage yolu: laravel/storage/app/public
        $oldStoragePath = storage_path('app/public');
        
        $output = "=== FOTOĞRAF TAŞIMA İŞLEMİ ===\n\n";
        $output .= "Kaynak: $oldStoragePath\n";
        $output .= "Hedef: $newStoragePath\n\n";
        
        // public_html/storage klasörünü oluştur
        if (!file_exists($newStoragePath)) {
            mkdir($newStoragePath, 0755, true);
            $output .= "✓ Hedef klasör oluşturuldu\n";
        }
        
        // kontrol_fotograflari klasörünü kontrol et
        $sourceDir = $oldStoragePath . '/kontrol_fotograflari';
        $targetDir = $newStoragePath . '/kontrol_fotograflari';
        
        if (file_exists($sourceDir)) {
            // Klasörü kopyala (recursive)
            function copyDirectory($src, $dst) {
                $dir = opendir($src);
                @mkdir($dst, 0755, true);
                while(false !== ($file = readdir($dir))) {
                    if (($file != '.') && ($file != '..')) {
                        if (is_dir($src . '/' . $file)) {
                            copyDirectory($src . '/' . $file, $dst . '/' . $file);
                        } else {
                            copy($src . '/' . $file, $dst . '/' . $file);
                        }
                    }
                }
                closedir($dir);
            }
            
            copyDirectory($sourceDir, $targetDir);
            
            // Kaç dosya kopyalandı?
            $count = 0;
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($targetDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    $count++;
                }
            }
            
            $output .= "✓ $count adet fotoğraf kopyalandı\n\n";
            $output .= "Test URL: " . url('storage/kontrol_fotograflari/2026/01') . "\n";
        } else {
            $output .= "⚠ Kaynak klasör bulunamadı: $sourceDir\n";
        }
        
        return response("<pre>$output</pre>");
    } catch (\Exception $e) {
        return response("<pre>Hata: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>", 500);
    }
});

// DEBUG: Fotoğraf yükleme testi
Route::get('/test-upload', function () {
    if (request('key') !== 'atiksu2026') {
        abort(403, 'Unauthorized');
    }
    
    $output = "=== FOTOĞRAF YÜKLEMEsi TEST ===\n\n";
    
    // 1. Storage yolu
    $storagePath = Storage::disk('public')->path('');
    $output .= "1. Storage Root: $storagePath\n";
    $output .= "   Yazılabilir mi? " . (is_writable($storagePath) ? "✓ EVET" : "✗ HAYIR") . "\n\n";
    
    // 2. Test klasörü oluştur
    $testDir = 'test_' . time();
    try {
        $fullPath = Storage::disk('public')->path($testDir);
        mkdir($fullPath, 0755, true);
        $output .= "2. Test klasörü oluşturuldu: $fullPath\n";
        $output .= "   Klasör var mı? " . (file_exists($fullPath) ? "✓ EVET" : "✗ HAYIR") . "\n\n";
        
        // 3. Test dosyası yaz
        $testFile = $testDir . '/test.txt';
        $testContent = 'Test içeriği: ' . date('Y-m-d H:i:s');
        file_put_contents(Storage::disk('public')->path($testFile), $testContent);
        $output .= "3. Test dosyası yazıldı: " . Storage::disk('public')->path($testFile) . "\n";
        $output .= "   Dosya var mı? " . (file_exists(Storage::disk('public')->path($testFile)) ? "✓ EVET" : "✗ HAYIR") . "\n";
        $output .= "   URL: " . Storage::disk('public')->url($testFile) . "\n\n";
        
        // 4. GD kütüphanesi kontrol
        $output .= "4. PHP GD Kütüphanesi:\n";
        if (function_exists('gd_info')) {
            $gdInfo = gd_info();
            $output .= "   ✓ GD yüklü - Versiyon: " . $gdInfo['GD Version'] . "\n";
            $output .= "   JPEG: " . ($gdInfo['JPEG Support'] ? "✓" : "✗") . "\n";
            $output .= "   PNG: " . ($gdInfo['PNG Support'] ? "✓" : "✗") . "\n";
            $output .= "   GIF: " . ($gdInfo['GIF Create Support'] ? "✓" : "✗") . "\n";
        } else {
            $output .= "   ✗ GD yüklü değil!\n";
        }
        $output .= "\n";
        
        // 5. .env kontrolü
        $output .= "5. Env Ayarları:\n";
        $output .= "   STORAGE_PUBLIC_PATH: " . (env('STORAGE_PUBLIC_PATH') ?: 'Tanımlı değil') . "\n";
        $output .= "   APP_URL: " . env('APP_URL') . "\n\n";
        
        // Temizlik
        @unlink(Storage::disk('public')->path($testFile));
        @rmdir($fullPath);
        
        $output .= "✓ Test tamamlandı!\n";
        
    } catch (\Exception $e) {
        $output .= "✗ HATA: " . $e->getMessage() . "\n";
    }
    
    return response("<pre>$output</pre>");
});

// Fotoğrafları serve et (PHP ile)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($fullPath);
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

Route::get('/', function () {
    if (auth()->check()) {
        // Tüm kullanıcılar (admin ve personel) admin paneline erişebilir
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    // Rate limiting: 5 login denemesi/dakika
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    
    Route::get('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'showForgotForm'])->name('password.request');
    // Rate limiting: 3 şifre sıfırlama talebi/dakika
    Route::post('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])
        ->name('password.email')
        ->middleware('throttle:3,1');
    Route::get('/reset-password/{token}', [App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    // Rate limiting: 5 şifre sıfırlama girişimi/dakika
    Route::post('/reset-password', [App\Http\Controllers\PasswordResetController::class, 'resetPassword'])
        ->name('password.update')
        ->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public QR Kod Kontrol Sistemi (Login gerektirmez)
// Rate limiting: 20 istek/dakika
Route::middleware('throttle:20,1')->group(function () {
    Route::get('/kontrol/bina/{uuid}', [PublicKontrolController::class, 'index'])->name('public.kontrol.index');
    Route::post('/kontrol/bina/{uuid}', [PublicKontrolController::class, 'store'])->name('public.kontrol.store');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::post('/dashboard/notes', [AdminDashboard::class, 'storeNote'])->name('dashboard.notes.store');
    Route::post('/dashboard/notes/{note}/send', [AdminDashboard::class, 'sendNoteToUsers'])->name('dashboard.notes.send');
    Route::delete('/dashboard/notes/{note}', [AdminDashboard::class, 'deleteNote'])->name('dashboard.notes.delete');
    Route::get('/dashboard/day-details', [AdminDashboard::class, 'getDayDetails'])->name('dashboard.day-details');
    
    // Kullanıcılar
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-qr', [UserController::class, 'toggleQrGorunur'])->name('users.toggle-qr');
    Route::post('/users/{user}/toggle-mail', [UserController::class, 'toggleMailAlsin'])->name('users.toggle-mail');
    
    // Mail Ayarları
    Route::get('/mail-ayarlari', [MailAyarlariController::class, 'index'])->name('mail-ayarlari.index');
    Route::post('/mail-ayarlari', [MailAyarlariController::class, 'update'])->name('mail-ayarlari.update');
    
    // Mail Test Paneli
    Route::get('/mail-test', [MailTestController::class, 'index'])->name('mail-test.index');
    Route::post('/mail-test/smtp', [MailTestController::class, 'testSmtp'])->name('mail-test.smtp');
    Route::post('/mail-test/scheduled', [MailTestController::class, 'testScheduledMail'])->name('mail-test.scheduled');
    Route::post('/mail-test/cron', [MailTestController::class, 'testCron'])->name('mail-test.cron');
    
    // Binalar - Bulk delete ÖNCELİKLE tanımlanmalı
    Route::delete('/binalar/bulk-delete', [BinaController::class, 'bulkDestroy'])->name('binalar.bulk-delete');
    Route::post('/binalar/{bina}/regenerate-qr', [BinaController::class, 'regenerateQr'])->name('binalar.regenerate-qr');
    Route::resource('binalar', BinaController::class)->parameters(['binalar' => 'bina']);
    
    // Bina Çalışma Durumu (Dekantör Çalışmadı Sistemi)
    Route::post('/bina-calisma-durumu', [BinaCalismaDurumuController::class, 'store'])->name('bina-calisma-durumu.store');
    Route::delete('/bina-calisma-durumu', [BinaCalismaDurumuController::class, 'destroy'])->name('bina-calisma-durumu.destroy');
    Route::get('/bina-calisma-durumu/check/{bina}/{tarih}', [BinaCalismaDurumuController::class, 'check'])->name('bina-calisma-durumu.check');
    Route::get('/bina-calisma-durumu/liste/{bina}', [BinaCalismaDurumuController::class, 'liste'])->name('bina-calisma-durumu.liste');
    
    // Kontrol Kayıtları (Onay Sistemi)
    Route::get('/kontrol-kayitlari', [KontrolKaydiController::class, 'index'])->name('kontrol-kayitlari.index');
    Route::get('/kontrol-kayitlari/{id}', [KontrolKaydiController::class, 'show'])->name('kontrol-kayitlari.show');
    Route::post('/kontrol-kayitlari/{id}/onayla', [KontrolKaydiController::class, 'onayla'])->name('kontrol-kayitlari.onayla');
    Route::post('/kontrol-kayitlari/{id}/reddet', [KontrolKaydiController::class, 'reddet'])->name('kontrol-kayitlari.reddet');
    Route::post('/kontrol-kayitlari/toplu-onayla', [KontrolKaydiController::class, 'topluOnayla'])->name('kontrol-kayitlari.toplu-onayla');
    Route::delete('/kontrol-kayitlari/{id}/fotograf', [KontrolKaydiController::class, 'deleteFotograf'])->name('kontrol-kayitlari.delete-fotograf');
    
    // Geçmiş Tarihli Kontrol Girişi
    Route::get('/kontroller/gecmis-tarih', [GecmisTarihKontrolController::class, 'index'])->name('kontroller.gecmis-tarih');
    Route::post('/kontroller/gecmis-tarih', [GecmisTarihKontrolController::class, 'store'])->name('kontroller.gecmis-tarih.store');
    
    // Personel Devam Takibi
    Route::get('/personel-devam', [PersonelDevamController::class, 'index'])->name('personel-devam.index');
    Route::post('/personel-devam', [PersonelDevamController::class, 'store'])->name('personel-devam.store');
    Route::get('/personel-devam/aylik', [PersonelDevamController::class, 'aylikGoruntule'])->name('personel-devam.aylik');
    Route::get('/personel-devam/pdf', [PersonelDevamController::class, 'pdfIndir'])->name('personel-devam.pdf');
    
    // Laboratuvar Analiz Raporları
    Route::get('/laboratuvar', [LaboratuvarController::class, 'index'])->name('laboratuvar.index');
    Route::get('/laboratuvar/create', [LaboratuvarController::class, 'create'])->name('laboratuvar.create');
    Route::post('/laboratuvar', [LaboratuvarController::class, 'store'])->name('laboratuvar.store');
    Route::get('/laboratuvar/{id}', [LaboratuvarController::class, 'show'])->name('laboratuvar.show');
    Route::get('/laboratuvar/{id}/edit', [LaboratuvarController::class, 'edit'])->name('laboratuvar.edit');
    Route::put('/laboratuvar/{id}', [LaboratuvarController::class, 'update'])->name('laboratuvar.update');
    Route::delete('/laboratuvar/{id}', [LaboratuvarController::class, 'destroy'])->name('laboratuvar.destroy');
    Route::get('/laboratuvar/{id}/pdf', [LaboratuvarController::class, 'pdfGoster'])->name('laboratuvar.pdf');
    Route::get('/laboratuvar-grafikler', [LaboratuvarController::class, 'grafikler'])->name('laboratuvar.grafikler');
    
    // Kontrol Maddeleri - Bulk delete ÖNCELİKLE tanımlanmalı
    Route::delete('/kontrol-maddeleri/bulk-delete', [KontrolMaddesiController::class, 'bulkDestroy'])->name('kontrol-maddeleri.bulk-delete');
    Route::resource('kontrol-maddeleri', KontrolMaddesiController::class)->parameters(['kontrol-maddeleri' => 'kontrol_maddesi']);
    
    // Sayısal Veri Analizi
    Route::get('/sayisal-analiz', [IstatistiklerController::class, 'sayisalAnaliz'])->name('sayisal-analiz');
    
    // İş Takvimi
    Route::get('/is-takvimi', [IsTakvimiController::class, 'index'])->name('is-takvimi.index');
    Route::get('/is-takvimi/events', [IsTakvimiController::class, 'getEvents'])->name('is-takvimi.events');
    Route::get('/is-takvimi/basliklar', [IsTakvimiController::class, 'getBasliklar'])->name('is-takvimi.basliklar');
    Route::post('/is-takvimi', [IsTakvimiController::class, 'store'])->name('is-takvimi.store');
    Route::put('/is-takvimi/{id}', [IsTakvimiController::class, 'update'])->name('is-takvimi.update');
    Route::delete('/is-takvimi/{id}', [IsTakvimiController::class, 'destroy'])->name('is-takvimi.destroy');
    Route::post('/is-takvimi/{id}/toggle-durum', [IsTakvimiController::class, 'toggleDurum'])->name('is-takvimi.toggle-durum');
    Route::patch('/is-takvimi/{id}/tarih', [IsTakvimiController::class, 'updateTarih'])->name('is-takvimi.update-tarih');
    Route::post('/is-takvimi/copy-tekrarli', [IsTakvimiController::class, 'copyTekrarliIsler'])->name('is-takvimi.copy-tekrarli');
    
    // Raporlar
    Route::get('/raporlar', [RaporController::class, 'index'])->name('raporlar.index');
    Route::get('/raporlar/pdf', [RaporController::class, 'exportPdf'])->name('raporlar.pdf');
    
    // Arşivlenmiş İşler
    Route::resource('arsivlenmis-isler', ArsivlenmisIsController::class)->parameters(['arsivlenmis-isler' => 'arsivlenmis_is']);
    Route::post('/arsivlenmis-isler/{arsivlenmis_is}/delete-fotograf', [ArsivlenmisIsController::class, 'deleteFotograf'])->name('arsivlenmis-isler.delete-fotograf');
    
    // Aktivite Logları
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::delete('/activity-logs/clear', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');
    
    // Sistem Teşhis Paneli
    Route::get('/system-test', [SystemTestController::class, 'index'])->name('system-test.index');
    Route::post('/system-test/authenticate', [SystemTestController::class, 'authenticate'])->name('system-test.authenticate');
    Route::get('/system-test/logout', [SystemTestController::class, 'logout'])->name('system-test.logout');
    Route::post('/system-test/clear-cache', [SystemTestController::class, 'clearCache'])->name('system-test.clear-cache');
});

// Personel Routes - Artık admin paneline yönlendiriliyor
Route::middleware(['auth'])->prefix('personel')->name('personel.')->group(function () {
    // Eski personel dashboard URL'i admin dashboard'a yönlendirilir
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
    
    // Kontrol kaydetme işlemi admin controller'a yönlendirilir
    Route::post('/kontrol-kaydet', [PersonelDashboard::class, 'store'])->name('kontrol.store');
});
