<?php
// DomPDF Alternatif Test - ServiceProvider bypass

define('LARAVEL_START', microtime(true));

require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

// Public path ZORLA
$app->usePublicPath(realpath(__DIR__));

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h3>✅ Laravel Başlatıldı</h3>";
echo "Public Path: " . public_path() . "<br>";
echo "Storage Path: " . storage_path() . "<br>";

// DomPDF'i DOĞRUDAN kullan (Facade değil)
try {
    // Dompdf sınıfını doğrudan kullan
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('fontDir', storage_path('fonts'));
    $options->set('fontCache', storage_path('fonts'));
    $options->set('tempDir', sys_get_temp_dir());
    $options->set('chroot', base_path());
    
    $dompdf = new \Dompdf\Dompdf($options);
    
    $html = '<html><body><h1>Test PDF</h1><p>DomPDF çalışıyor!</p></body></html>';
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    echo "<p>✅ DomPDF başarıyla çalıştı!</p>";
    echo "<p><a href='download-direct-pdf.php'>PDF İndir</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Hata: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

$kernel->terminate($request, $response);
