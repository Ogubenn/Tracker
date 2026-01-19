<?php

namespace App\Console;

use App\Models\SiteAyarlari;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Sabah eksik kontrol maili - Dinamik saat
        $schedule->call(function () {
            $aktif = SiteAyarlari::getBool('eksik_kontrol_mail_aktif');
            
            if ($aktif) {
                \Artisan::call('kontrol:eksik-mail', ['tur' => 'sabah']);
            }
        })->dailyAt(SiteAyarlari::get('eksik_kontrol_sabah_saat', '07:00'));

        // Akşam eksik kontrol maili - Dinamik saat
        $schedule->call(function () {
            $aktif = SiteAyarlari::getBool('eksik_kontrol_mail_aktif');
            
            if ($aktif) {
                \Artisan::call('kontrol:eksik-mail', ['tur' => 'aksam']);
            }
        })->dailyAt(SiteAyarlari::get('eksik_kontrol_aksam_saat', '19:00'));

        // Günlük toplu rapor - Dinamik saat
        $schedule->call(function () {
            $aktif = SiteAyarlari::getBool('toplu_rapor_mail_aktif');
            
            if ($aktif) {
                \Artisan::call('kontrol:toplu-rapor');
            }
        })->dailyAt(SiteAyarlari::get('toplu_rapor_saat', '19:00'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
