<?php
// PDF İndirme - Doğrudan DomPDF

define('LARAVEL_START', microtime(true));

require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
$app->usePublicPath(realpath(__DIR__));

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('fontDir', storage_path('fonts'));
    $options->set('fontCache', storage_path('fonts'));
    $options->set('tempDir', sys_get_temp_dir());
    $options->set('chroot', base_path());
    
    $dompdf = new \Dompdf\Dompdf($options);
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: DejaVu Sans, sans-serif; }
            h1 { color: #4CAF50; }
        </style>
    </head>
    <body>
        <h1>Test PDF Başarılı!</h1>
        <p>Tarih: ' . date('d.m.Y H:i:s') . '</p>
        <p>DomPDF doğrudan (Facade olmadan) çalışıyor.</p>
    </body>
    </html>
    ';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="test-direct.pdf"');
    echo $dompdf->output();
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
