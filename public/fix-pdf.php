<?php
// PDF Sorunu Test ve Çözüm Scripti

define('LARAVEL_START', microtime(true));

// Laravel bootstrap
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

// Public path'i manuel set et
$app->usePublicPath(__DIR__);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h3>PDF Test</h3>";
echo "<p>Public Path: " . public_path() . "</p>";
echo "<p>Storage Path: " . storage_path() . "</p>";
echo "<p>Base Path: " . base_path() . "</p>";

// DomPDF test
try {
    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test PDF</h1><p>Bu bir test.</p>');
    echo "<p>✅ DomPDF çalışıyor!</p>";
    
    // Font cache klasörünü kontrol et
    $fontCache = storage_path('fonts');
    if (!file_exists($fontCache)) {
        mkdir($fontCache, 0777, true);
        echo "<p>✅ Font cache klasörü oluşturuldu: $fontCache</p>";
    } else {
        echo "<p>✅ Font cache var: $fontCache</p>";
    }
    
    echo "<p><a href='download-test-pdf.php'>Test PDF İndir</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Hata: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

$kernel->terminate($request, $response);
