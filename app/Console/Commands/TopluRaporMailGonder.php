<?php

namespace App\Console\Commands;

use App\Models\Bina;
use App\Models\KontrolKaydi;
use App\Models\SiteAyarlari;
use App\Models\User;
use App\Notifications\TopluRaporBildirimi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class TopluRaporMailGonder extends Command
{
    protected $signature = 'kontrol:toplu-rapor';
    protected $description = 'Günlük kontrol raporu admin kullanıcılara gönderir';

    public function handle(): int
    {
        if (!SiteAyarlari::getBool('toplu_rapor_mail_aktif')) {
            $this->info('Toplu rapor mail bildirimleri kapalı.');
            return 0;
        }

        $adminler = $this->getMailAlacakAdminler();

        if ($adminler->isEmpty()) {
            $this->warn('Mail alacak admin kullanıcı bulunamadı.');
            return 0;
        }

        $tarih = Carbon::today()->format('d.m.Y');
        $tamamlananKontroller = $this->getTamamlananKontroller();
        $eksikKontroller = $this->getEksikKontroller();

        foreach ($adminler as $admin) {
            $admin->notify(new TopluRaporBildirimi($tamamlananKontroller, $eksikKontroller, $tarih));
        }

        $this->info($adminler->count() . ' kullanıcıya günlük rapor gönderildi.');
        $this->info('Tamamlanan: ' . $tamamlananKontroller->count() . ', Eksik: ' . $eksikKontroller->count());

        return 0;
    }

    private function getMailAlacakAdminler(): Collection
    {
        return User::where('aktif_mi', true)
            ->where('mail_alsin', true)
            ->get();
    }

    private function getTamamlananKontroller(): Collection
    {
        return KontrolKaydi::with(['bina', 'kontrolMaddesi', 'yapanKullanici'])
            ->whereDate('tarih', Carbon::today())
            ->get()
            ->map(function ($kayit) {
                return [
                    'bina' => $kayit->bina->bina_adi,
                    'kontrol' => $kayit->kontrolMaddesi->kontrol_adi,
                    'durum' => $kayit->durum,
                    'yapan' => $kayit->yapanKullanici->ad,
                ];
            });
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
                    ]);
                }
            }
        }

        return $eksikKontroller;
    }
}
