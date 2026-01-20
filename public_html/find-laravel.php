<?php
// Laravel'in gerçek konumunu bul
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Laravel Dosya Arama</h2>";

$currentDir = __DIR__;
echo "<p><strong>Şu anki dizin:</strong> $currentDir</p>";

// Muhtemel Laravel root dizinleri
$possiblePaths = [
    '/home/ogubenn/domains/xn--atksutakip-yub.com.tr/laravel',  // ÖNEMLİ: Bu klasör var!
    $currentDir . '/..',  // public_html/..
    $currentDir . '/../..',  // public_html/../..
    dirname($currentDir),  // parent
    dirname(dirname($currentDir)),  // parent parent
    '/home/ogubenn/atiksu_takip',
    '/home/ogubenn/domains/xn--atksutakip-yub.com.tr',
    '/home/ogubenn/domains/xn--atksutakip-yub.com.tr/atiksu_takip',
    '/home/ogubenn/public_html',
];

echo "<h3>Dizin Taraması:</h3>";

foreach ($possiblePaths as $path) {
    $realPath = realpath($path);
    if ($realPath) {
        echo "<div style='border:1px solid #ddd;padding:10px;margin:5px 0;'>";
        echo "<strong>Path:</strong> $path<br>";
        echo "<strong>Real Path:</strong> $realPath<br>";
        
        // vendor/autoload.php var mı?
        if (file_exists($realPath . '/vendor/autoload.php')) {
            echo "<span style='color:green;font-weight:bold;'>✓ vendor/autoload.php BULUNDU!</span><br>";
            
            // bootstrap/app.php var mı?
            if (file_exists($realPath . '/bootstrap/app.php')) {
                echo "<span style='color:green;font-weight:bold;'>✓ bootstrap/app.php BULUNDU!</span><br>";
                echo "<span style='background:yellow;padding:5px;'>➜ LARAVEL ROOT: $realPath</span><br>";
                
                // Cache dosyalarını kontrol et
                echo "<br><strong>Cache Durumu:</strong><br>";
                
                if (file_exists($realPath . '/bootstrap/cache/config.php')) {
                    echo "⚠ Config cache VAR (silinmeli)<br>";
                }
                if (file_exists($realPath . '/bootstrap/cache/routes-v7.php')) {
                    echo "⚠ Route cache VAR (silinmeli)<br>";
                }
                
                $viewDir = $realPath . '/storage/framework/views';
                if (is_dir($viewDir)) {
                    $viewFiles = glob($viewDir . '/*.php');
                    if (count($viewFiles) > 0) {
                        echo "⚠ View cache VAR (" . count($viewFiles) . " dosya)<br>";
                    }
                }
                
                // .env dosyası var mı?
                if (file_exists($realPath . '/.env')) {
                    echo "<br><span style='color:green;'>✓ .env dosyası var</span><br>";
                }
                
                // Log dosyası var mı?
                if (file_exists($realPath . '/storage/logs/laravel.log')) {
                    echo "<span style='color:green;'>✓ Log dosyası var</span><br>";
                }
            } else {
                echo "<span style='color:orange;'>✗ bootstrap/app.php YOK</span><br>";
            }
        } else {
            echo "<span style='color:red;'>✗ vendor/autoload.php YOK</span><br>";
        }
        
        // İçindeki dosya ve klasörleri listele
        if (is_dir($realPath)) {
            $contents = scandir($realPath);
            $filtered = array_filter($contents, function($item) {
                return $item !== '.' && $item !== '..';
            });
            echo "<small style='color:gray;'>İçerik: " . implode(', ', array_slice($filtered, 0, 15)) . "</small><br>";
        }
        
        echo "</div>";
    }
}

// Mevcut dizinin içeriğini göster
echo "<h3>Mevcut Dizin İçeriği (__DIR__):</h3>";
echo "<pre style='background:#f5f5f5;padding:10px;'>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $fullPath = __DIR__ . '/' . $file;
        $type = is_dir($fullPath) ? '[DIR]' : '[FILE]';
        echo "$type $file\n";
    }
}
echo "</pre>";

?>
