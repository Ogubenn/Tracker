<?php

namespace App\Console;

use App\Models\SiteAyarlari;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $sabahtSaat = SiteAyarlari::get('eksik_kontrol_sabah_saat', '07:00');
            $aktif = SiteAyarlari::getBool('eksik_kontrol_mail_aktif');
            
            if ($aktif) {
                \Artisan::call('kontrol:eksik-mail sabah');
            }
        })->dailyAt('07:00');

        $schedule->call(function () {
            $aksamSaat = SiteAyarlari::get('eksik_kontrol_aksam_saat', '19:00');
            $aktif = SiteAyarlari::getBool('eksik_kontrol_mail_aktif');
            
            if ($aktif) {
                \Artisan::call('kontrol:eksik-mail aksam');
            }
        })->dailyAt('19:00');

        $schedule->call(function () {
            $aktif = SiteAyarlari::getBool('toplu_rapor_mail_aktif');
            
            if ($aktif) {
                \Artisan::call('kontrol:toplu-rapor');
            }
        })->dailyAt('19:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
