<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CacheClear extends Command
{
    /**
     * Artisan komut adı.
     *
     * @var string
     */
    protected $signature = 'atiksu:cache-clear {type?=all : Cache tipi (all, reports, binalar, kontroller, laravel)}';

    /**
     * Komut açıklaması.
     *
     * @var string
     */
    protected $description = 'Atıksu Takip sistemi cache\'lerini temizler';

    /**
     * Komutu çalıştır.
     */
    public function __construct(private CacheService $cacheService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $type = $this->argument('type');

        $this->info("🧹 Cache temizleme başlıyor...\n");

        $success = match($type) {
            'all' => $this->clearAll(),
            'reports', 'rapor', 'raporlar' => $this->clearReports(),
            'binalar', 'bina' => $this->clearBinalar(),
            'kontroller', 'kontrol' => $this->clearKontroller(),
            'laravel' => $this->clearLaravel(),
            default => $this->invalidType($type),
        };

        if ($success === false) {
            return 1;
        }

        $this->newLine();
        $this->info('✅ Cache temizleme tamamlandı!');
        $this->line('⏰ ' . now()->format('d.m.Y H:i:s'));

        return 0;
    }

    /**
     * Tüm cache'leri temizle.
     */
    private function clearAll(): bool
    {
        $this->line('📦 Tüm cache\'ler temizleniyor...');
        
        $this->cacheService->flush();
        $this->task('Uygulama cache', fn() => true);
        
        Artisan::call('config:clear');
        $this->task('Config cache', fn() => true);
        
        Artisan::call('route:clear');
        $this->task('Route cache', fn() => true);
        
        Artisan::call('view:clear');
        $this->task('View cache', fn() => true);

        return true;
    }

    /**
     * Rapor cache'lerini temizle.
     */
    private function clearReports(): bool
    {
        $this->line('📊 Rapor cache\'leri temizleniyor...');
        $this->cacheService->clearReports();
        $this->task('Rapor cache', fn() => true);

        return true;
    }

    /**
     * Bina cache'lerini temizle.
     */
    private function clearBinalar(): bool
    {
        $this->line('🏢 Bina cache\'leri temizleniyor...');
        $this->cacheService->clearBina();
        $this->task('Bina cache', fn() => true);

        return true;
    }

    /**
     * Kontrol cache'lerini temizle.
     */
    private function clearKontroller(): bool
    {
        $this->line('✅ Kontrol cache\'leri temizleniyor...');
        $this->cacheService->clearKontroller();
        $this->task('Kontrol cache', fn() => true);

        return true;
    }

    /**
     * Sadece Laravel cache'lerini temizle.
     */
    private function clearLaravel(): bool
    {
        $this->line('🔧 Laravel cache\'leri temizleniyor...');
        
        Artisan::call('cache:clear');
        $this->task('Application cache', fn() => true);
        
        Artisan::call('config:clear');
        $this->task('Config cache', fn() => true);
        
        Artisan::call('route:clear');
        $this->task('Route cache', fn() => true);
        
        Artisan::call('view:clear');
        $this->task('View cache', fn() => true);

        return true;
    }

    /**
     * Geçersiz tip uyarısı.
     */
    private function invalidType(string $type): bool
    {
        $this->error("❌ Geçersiz cache tipi: {$type}");
        $this->newLine();
        $this->warn('Kullanılabilir tipler:');
        $this->line('  - all         : Tüm cache\'ler');
        $this->line('  - reports     : Rapor cache\'leri');
        $this->line('  - binalar     : Bina cache\'leri');
        $this->line('  - kontroller  : Kontrol cache\'leri');
        $this->line('  - laravel     : Laravel sistem cache\'leri');
        $this->newLine();
        $this->info('Örnek: php artisan atiksu:cache-clear reports');

        return false;
    }
}
