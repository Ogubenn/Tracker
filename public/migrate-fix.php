<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '<pre>';
echo "=== DATABASE FIX: alan_id -> bina_id ===\n\n";

try {
    // Önce foreign key kontrolü
    $hasAlanId = DB::select("SHOW COLUMNS FROM kontrol_maddeleri LIKE 'alan_id'");
    
    if (!empty($hasAlanId)) {
        echo "✅ alan_id kolonu bulundu, dönüştürülüyor...\n\n";
        
        // Foreign key'i sil
        try {
            DB::statement("ALTER TABLE kontrol_maddeleri DROP FOREIGN KEY kontrol_maddeleri_alan_id_foreign");
            echo "✓ Foreign key silindi\n";
        } catch (Exception $e) {
            echo "⚠ Foreign key silinemedi (zaten silinmiş olabilir)\n";
        }
        
        // Kolonu yeniden adlandır
        DB::statement("ALTER TABLE kontrol_maddeleri CHANGE alan_id bina_id BIGINT UNSIGNED NOT NULL");
        echo "✓ alan_id -> bina_id dönüştürüldü\n";
        
        // Yeni foreign key ekle
        DB::statement("ALTER TABLE kontrol_maddeleri ADD CONSTRAINT kontrol_maddeleri_bina_id_foreign FOREIGN KEY (bina_id) REFERENCES binalar(id) ON DELETE CASCADE");
        echo "✓ Yeni foreign key eklendi\n";
        
        echo "\n✅ BAŞARIYLA TAMAMLANDI!\n";
    } else {
        echo "ℹ alan_id kolonu yok, muhtemelen zaten bina_id var.\n";
        
        // bina_id var mı kontrol et
        $hasBinaId = DB::select("SHOW COLUMNS FROM kontrol_maddeleri LIKE 'bina_id'");
        if (!empty($hasBinaId)) {
            echo "✅ bina_id kolonu mevcut, her şey hazır!\n";
        } else {
            echo "❌ Ne alan_id ne de bina_id var!\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ HATA: " . $e->getMessage() . "\n";
}

echo '</pre>';
