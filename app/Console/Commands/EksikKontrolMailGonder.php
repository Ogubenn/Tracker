<?php

namespace App\Console\Commands;

use App\Models\Bina;
use App\Models\SiteAyarlari;
use App\Models\User;
use App\Notifications\EksikKontrolBildirimi;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class EksikKontrolMailGonder extends Command
{
    protected $signature = 'kontrol:eksik-mail {tur=sabah}';
    protected $description = 'Eksik kontroller için personele mail gönderir';

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

        $eksikKontroller = $this->getEksikKontroller();

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

    private function getEksikKontroller(): Collection
    {
        $eksikKontroller = collect();

        $binalar = Bina::aktif()
            ->with(['aktifKontrolMaddeleri' => fn($q) => $q->where('aktif_mi', true)])
            ->get();

        foreach ($binalar as $bina) {
            foreach ($bina->aktifKontrolMaddeleri as $kontrolMaddesi) {
                if ($kontrolMaddesi->bugunYapilmaliMi() && !$kontrolMaddesi->bugunKaydiVarMi()) {
                    $eksikKontroller->push([
                        'bina' => $bina->bina_adi,
                        'kontrol' => $kontrolMaddesi->kontrol_adi,
                        'periyot' => $this->periyotCevir($kontrolMaddesi->periyot),
                    ]);
                }
            }
        }

        return $eksikKontroller;
    }

    private function periyotCevir(string $periyot): string
    {
        return match($periyot) {
            'gunluk' => 'Günlük',
            'haftalik' => 'Haftalık',
            '15_gun' => '15 Günde Bir',
            'aylik' => 'Aylık',
            default => $periyot,
        };
    }
}
