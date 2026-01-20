<?php
// Bu dosyayı tarayıcıdan çağırarak hatayı görebilirsiniz
// URL: https://xn--atksutakip-yub.com.tr/debug-check.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Laravel Debug Check</h2>";

// Laravel root path - Sunucuya özel
$basePath = '/home/ogubenn/domains/xn--atksutakip-yub.com.tr/laravel';

echo "<p style='color:gray;font-size:12px;'>Base Path: $basePath</p>";
echo "<p style='color:gray;font-size:12px;'>Current Dir: ".__DIR__."</p>";

// 1. Laravel bootstrap dene
try {
    if (!file_exists($basePath.'/vendor/autoload.php')) {
        throw new Exception("vendor/autoload.php bulunamadı! Path: ".$basePath.'/vendor/autoload.php');
    }
    
    require $basePath.'/vendor/autoload.php';
    echo "✓ Autoload başarılı<br>";
    
    if (!file_exists($basePath.'/bootstrap/app.php')) {
        throw new Exception("bootstrap/app.php bulunamadı! Path: ".$basePath.'/bootstrap/app.php');
    }
    
    $app = require_once $basePath.'/bootstrap/app.php';
    echo "✓ App bootstrap başarılı<br>";
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    echo "✓ Kernel başarılı<br>";
    
    // Config cache kontrol
    if (file_exists($basePath.'/bootstrap/cache/config.php')) {
        echo "<br><strong style='color:orange'>⚠ Config cache var - Temizlenmeli!</strong><br>";
        echo "Terminal'den: php artisan config:clear<br>";
    } else {
        echo "✓ Config cache yok<br>";
    }
    
    // Route cache kontrol
    if (file_exists($basePath.'/bootstrap/cache/routes-v7.php')) {
        echo "<strong style='color:orange'>⚠ Route cache var - Temizlenmeli!</strong><br>";
        echo "Terminal'den: php artisan route:clear<br>";
    } else {
        echo "✓ Route cache yok<br>";
    }
    
    // View cache kontrol
    $viewCacheDir = $basePath.'/storage/framework/views';
    if (is_dir($viewCacheDir)) {
        $files = glob($viewCacheDir.'/*.php');
        if (count($files) > 0) {
            echo "<br><strong style='color:orange'>⚠ View cache var (".count($files)." dosya) - Temizlenmeli!</strong><br>";
            echo "Terminal'den: php artisan view:clear<br>";
        } else {
            echo "✓ View cache temiz<br>";
        }
    }
    
    echo "<br><h3>Son Laravel Hatası (Son 150 satır):</h3>";
    $logFile = $basePath.'/storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $lines = file($logFile);
        $lastLines = array_slice($lines, -150);
        echo "<pre style='background:#f5f5f5;padding:10px;overflow:auto;max-height:600px;font-size:10px;'>";
        echo htmlspecialchars(implode('', $lastLines));
        echo "</pre>";
        
        // Son hatayı özel olarak bul
        $logContent = implode('', $lastLines);
        if (preg_match('/production\.ERROR: (.+?) \{/', $logContent, $match)) {
            echo "<div style='background:#ffe6e6;padding:15px;margin:10px 0;border-left:4px solid #dc3545;'>";
            echo "<strong style='color:#dc3545;font-size:16px;'>🔴 SON HATA:</strong><br><br>";
            echo "<span style='font-size:14px;'>".htmlspecialchars($match[1])."</span>";
            echo "</div>";
        }
    } else {
        echo "<p style='color:red;'>Log dosyası bulunamadı: $logFile</p>";
    }
    
} catch (\Exception $e) {
    echo "<br><strong style='color:red'>✗ HATA:</strong><br>";
    echo "<pre style='background:#ffe6e6;padding:10px;'>";
    echo htmlspecialchars($e->getMessage());
    echo "\n\n";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "<hr>";
echo "<h3>Cache Temizleme Butonu</h3>";
echo "<a href='clear-all-cache.php' style='display:inline-block;padding:10px 20px;background:#dc3545;color:white;text-decoration:none;border-radius:5px;'>Tüm Cache'leri Temizle</a>";
?>
