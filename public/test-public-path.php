<?php
// Public path kontrolü ve düzeltmesi

$publicPath = __DIR__;
echo "Public Path: " . $publicPath . "<br>";
echo "realpath: " . realpath($publicPath) . "<br>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

// Laravel'i başlat
define('LARAVEL_START', microtime(true));

require __DIR__.'/../laravel/vendor/autoload.php';

$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

// Public path'i zorla set et
$app->usePublicPath(realpath(__DIR__));

echo "<br>App public path: " . $app->publicPath() . "<br>";
echo "public_path() helper: " . public_path() . "<br>";

// Kernel başlat
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<br><strong>✅ Laravel başarıyla başlatıldı!</strong><br>";
echo "Base Path: " . base_path() . "<br>";
echo "Storage Path: " . storage_path() . "<br>";

$kernel->terminate($request, $response);
