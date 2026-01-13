<?php
// Storage klasörlerini oluştur ve izinleri ayarla

$basePath = __DIR__ . '/../laravel';
$storagePath = $basePath . '/storage';

$directories = [
    'fonts',
    'framework/cache',
    'framework/sessions',
    'framework/views',
    'logs',
];

echo "<h3>Storage Klasörleri Kontrol</h3>";

foreach ($directories as $dir) {
    $fullPath = $storagePath . '/' . $dir;
    
    if (!file_exists($fullPath)) {
        if (mkdir($fullPath, 0777, true)) {
            echo "✅ Oluşturuldu: $dir<br>";
        } else {
            echo "❌ Oluşturulamadı: $dir<br>";
        }
    } else {
        echo "✅ Var: $dir<br>";
        
        // İzinleri kontrol et
        $perms = fileperms($fullPath);
        echo "&nbsp;&nbsp;&nbsp;İzinler: " . substr(sprintf('%o', $perms), -4) . "<br>";
        
        // 777 yap
        chmod($fullPath, 0777);
    }
}

echo "<br><p><strong>✅ Tamamlandı! Şimdi fix-pdf.php'yi test edin.</strong></p>";
