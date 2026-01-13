<?php
echo '<pre>';

$vendorPath = __DIR__ . '/../laravel/vendor';
$dompdfPath = $vendorPath . '/barryvdh/laravel-dompdf';
$autoloadPath = $vendorPath . '/autoload.php';

echo "=== VENDOR KONTROLÜ ===\n\n";

if (file_exists($vendorPath)) {
    echo "✅ Vendor klasörü var\n";
    
    if (file_exists($autoloadPath)) {
        echo "✅ autoload.php var\n";
    } else {
        echo "❌ autoload.php YOK\n";
    }
    
    if (file_exists($dompdfPath)) {
        echo "✅ DomPDF paketi var\n";
    } else {
        echo "❌ DomPDF paketi YOK - Composer install gerekli!\n";
    }
    
    // Vendor boyutu
    $size = 0;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($vendorPath));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    echo "\nVendor boyutu: " . round($size / 1024 / 1024, 2) . " MB\n";
    
} else {
    echo "❌ Vendor klasörü YOK!\n";
    echo "ZIP dosyasını çıkartırken vendor eksik kalmış olabilir.\n";
}

echo "\n=== ÇÖZ ÜM ===\n";
echo "1. Local'den vendor klasörünü yeniden ZIP'le\n";
echo "2. Sunucuya yükle ve çıkart\n";
echo "VEYA\n";
echo "3. Sunucuda SSH ile composer install çalıştır\n";

echo '</pre>';
