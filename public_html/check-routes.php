<?php
// Route'ları kontrol et
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Route Kontrolü</h2>";

$basePath = '/home/ogubenn/domains/xn--atksutakip-yub.com.tr/laravel';

try {
    require $basePath.'/vendor/autoload.php';
    $app = require_once $basePath.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<h3>System Test Route'ları:</h3>";
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    
    $systemTestRoutes = [];
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'system-test') !== false || strpos($uri, 'admin/system-test') !== false) {
            $systemTestRoutes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $uri,
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        }
    }
    
    if (count($systemTestRoutes) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
        echo "<tr><th>Method</th><th>URI</th><th>Name</th><th>Action</th></tr>";
        foreach ($systemTestRoutes as $r) {
            echo "<tr>";
            echo "<td>{$r['method']}</td>";
            echo "<td><strong>{$r['uri']}</strong></td>";
            echo "<td>{$r['name']}</td>";
            echo "<td style='font-size:11px;'>{$r['action']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<br><div style='background:#d4edda;padding:15px;border-left:4px solid #28a745;'>";
        echo "✓ System Test route'ları BULUNDU! Route dosyası yüklenmiş.";
        echo "</div>";
    } else {
        echo "<div style='background:#f8d7da;padding:15px;border-left:4px solid #dc3545;'>";
        echo "✗ System Test route'ları BULUNAMADI!<br><br>";
        echo "<strong>Çözüm:</strong><br>";
        echo "1. routes/web.php dosyasını FTP ile yükleyin<br>";
        echo "2. SystemTestController.php dosyasını app/Http/Controllers/Admin/ dizinine yükleyin<br>";
        echo "3. <a href='clear-all-cache.php'>Cache'leri temizleyin</a><br>";
        echo "</div>";
    }
    
    // Toplam route sayısı
    echo "<br><p style='color:gray;'>Toplam " . count($routes) . " route tanımlı.</p>";
    
    // Controller dosyası var mı?
    echo "<h3>Controller Dosya Kontrolü:</h3>";
    $controllerPath = $basePath . '/app/Http/Controllers/Admin/SystemTestController.php';
    if (file_exists($controllerPath)) {
        echo "✓ SystemTestController.php dosyası VAR<br>";
        echo "<small style='color:gray;'>$controllerPath</small>";
    } else {
        echo "<div style='background:#fff3cd;padding:10px;'>";
        echo "⚠ SystemTestController.php dosyası BULUNAMADI!<br>";
        echo "<small>Beklenen konum: $controllerPath</small>";
        echo "</div>";
    }
    
    // View dosyaları var mı?
    echo "<h3>View Dosya Kontrolü:</h3>";
    $viewPath1 = $basePath . '/resources/views/admin/system-test/index.blade.php';
    $viewPath2 = $basePath . '/resources/views/admin/system-test/login.blade.php';
    
    if (file_exists($viewPath1)) {
        echo "✓ index.blade.php VAR<br>";
    } else {
        echo "✗ index.blade.php YOK<br>";
    }
    
    if (file_exists($viewPath2)) {
        echo "✓ login.blade.php VAR<br>";
    } else {
        echo "✗ login.blade.php YOK<br>";
    }
    
} catch (\Exception $e) {
    echo "<div style='background:#f8d7da;padding:15px;'>";
    echo "<strong>HATA:</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
