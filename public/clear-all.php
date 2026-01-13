<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '<pre>';
Artisan::call('view:clear');
Artisan::call('cache:clear');
Artisan::call('config:clear');
echo "✅ Tüm cache temizlendi!\n";
echo '</pre>';
