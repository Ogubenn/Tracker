<?php
// Test PDF İndir

define('LARAVEL_START', microtime(true));

require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

// Public path'i manuel set et
$app->usePublicPath(__DIR__);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadHTML('
        <h1>Test PDF Başarılı</h1>
        <p>PDF export çalışıyor!</p>
        <p>Tarih: ' . date('d.m.Y H:i') . '</p>
    ');
    
    $pdf->download('test.pdf');
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
