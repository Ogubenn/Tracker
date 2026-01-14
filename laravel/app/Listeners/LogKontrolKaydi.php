<?php

namespace App\Listeners;

use App\Events\KontrolKaydiCreated;
use Illuminate\Support\Facades\Log;

class LogKontrolKaydi
{
    /**
     * Kontrol kaydı oluşturulduğunda log'a yaz.
     */
    public function handle(KontrolKaydiCreated $event): void
    {
        $kayit = $event->kontrolKaydi;

        Log::channel('daily')->info('Yeni kontrol kaydı oluşturuldu', [
            'kayit_id' => $kayit->id,
            'bina' => $kayit->bina->bina_adi ?? 'Bilinmiyor',
            'kontrol_maddesi' => $kayit->kontrolMaddesi->kontrol_adi ?? 'Bilinmiyor',
            'durum' => $kayit->durum,
            'yapan_kullanici' => $kayit->yapanKullanici->ad ?? 'Sistem',
            'tarih' => $kayit->tarih?->format('d.m.Y H:i'),
            'ip' => $kayit->ip_adresi,
        ]);
    }
}
