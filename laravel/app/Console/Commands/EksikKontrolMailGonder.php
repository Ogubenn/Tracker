<?php

namespace App\Console\Commands;

use App\Models\SiteAyarlari;
use App\Models\User;
use App\Notifications\EksikKontrolBildirimi;
use App\Services\KontrolService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class EksikKontrolMailGonder extends Command
{
    protected $signature = 'kontrol:eksik-mail {tur=sabah}';
    protected $description = 'Eksik kontroller için personele mail gönderir';

    public function __construct(private KontrolService $kontrolService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (!SiteAyarlari::getBool('eksik_kontrol_mail_aktif')) {
            $this->info('Eksik kontrol mail bildirimleri kapalı.');
            return 0;
        }

        $tur = $this->argument('tur');
        $personeller = $this->getMailAlacakPersoneller();

        if ($personeller->isEmpty()) {
            $this->warn('Mail alacak aktif kullanıcı bulunamadı.');
            return 0;
        }

        $eksikKontroller = $this->kontrolService->getEksikKontroller();

        if ($eksikKontroller->isEmpty()) {
            $this->info('Eksik kontrol yok. Mail gönderilmedi.');
            return 0;
        }

        foreach ($personeller as $personel) {
            $personel->notify(new EksikKontrolBildirimi($eksikKontroller, $tur));
        }

        $this->info($personeller->count() . ' kullanıcıya toplam ' . $eksikKontroller->count() . ' eksik kontrol bildirimi gönderildi.');

        return 0;
    }

    private function getMailAlacakPersoneller(): Collection
    {
        return User::where('aktif_mi', true)
            ->where('mail_alsin', true)
            ->get();
    }
}
