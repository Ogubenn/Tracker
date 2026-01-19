<?php
// Cache temizleme dosyası - Tarayıcıdan çalıştırın
// URL: https://xn--atksutakip-yub.com.tr/clear-all-cache.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Cache Temizleme İşlemi</h2>";

// Laravel root path - Sunucuya özel
$basePath = '/home/ogubenn/domains/xn--atksutakip-yub.com.tr/laravel';

echo "<p style='color:gray;font-size:12px;'>Base Path: $basePath</p>";

try {
    // Laravel yüklü değilse, direkt dosya silme yap
    $useDirectDelete = false;
    
    if (!file_exists($basePath.'/vendor/autoload.php')) {
        echo "<p style='color:orange;'>⚠ Laravel autoload bulunamadı, direkt dosya silme kullanılıyor...</p>";
        $useDirectDelete = true;
    }
    
    if (!$useDirectDelete) {
        require $basePath.'/vendor/autoload.php';
        $app = require_once $basePath.'/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    }
    
    // Config cache sil
    $configCache = $basePath.'/bootstrap/cache/config.php';
    if (file_exists($configCache)) {
        unlink($configCache);
        echo "✓ Config cache silindi<br>";
    } else {
        echo "- Config cache zaten yok<br>";
    }
    
    // Route cache sil
    $routeCache = $basePath.'/bootstrap/cache/routes-v7.php';
    if (file_exists($routeCache)) {
        unlink($routeCache);
        echo "✓ Route cache silindi<br>";
    } else {
        echo "- Route cache zaten yok<br>";
    }
    
    // View cache temizle
    $viewCacheDir = $basePath.'/storage/framework/views';
    if (is_dir($viewCacheDir)) {
        $files = glob($viewCacheDir.'/*.php');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        echo "✓ View cache temizlendi ($count dosya)<br>";
    } else {
        echo "- View cache dizini bulunamadı<br>";
    }
    
    // Application cache temizle
    $cacheDir = $basePath.'/storage/framework/cache/data';
    if (is_dir($cacheDir)) {
        $dirs = glob($cacheDir.'/*', GLOB_ONLYDIR);
        $count = 0;
        foreach ($dirs as $dir) {
            $files = glob($dir.'/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $count++;
                }
            }
        }
        echo "✓ App cache temizlendi ($count dosya)<br>";
    } else {
        echo "- App cache dizini bulunamadı<br>";
    }
    
    echo "<br><strong style='color:green;font-size:18px;'>✓ Tüm cache'ler temizlendi!</strong><br><br>";
    echo "<a href='/' style='display:inline-block;padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;'>Ana Sayfaya Dön</a> ";
    echo "<a href='debug-check.php' style='display:inline-block;padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;'>Debug Sayfasına Git</a>";
    
} catch (\Exception $e) {
    echo "<strong style='color:red'>HATA:</strong><br>";
    echo "<pre>".htmlspecialchars($e->getMessage())."</pre>";
}
?>
