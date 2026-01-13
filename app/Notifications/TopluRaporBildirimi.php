<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class TopluRaporBildirimi extends Notification implements ShouldQueue
{
    use Queueable;

    private Collection $tamamlananKontroller;
    private Collection $eksikKontroller;
    private string $tarih;

    public function __construct(Collection $tamamlananKontroller, Collection $eksikKontroller, string $tarih)
    {
        $this->tamamlananKontroller = $tamamlananKontroller;
        $this->eksikKontroller = $eksikKontroller;
        $this->tarih = $tarih;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $toplamKontrol = $this->tamamlananKontroller->count() + $this->eksikKontroller->count();
        $tamamlanmaOrani = $toplamKontrol > 0 
            ? round(($this->tamamlananKontroller->count() / $toplamKontrol) * 100) 
            : 0;

        $mail = (new MailMessage)
            ->subject('Günlük Kontrol Raporu - ' . $this->tarih)
            ->greeting('Merhaba ' . $notifiable->ad)
            ->line('**' . $this->tarih . '** tarihli kontrol raporu hazır.')
            ->line('---')
            ->line('📊 **Özet Bilgiler:**')
            ->line('✅ Tamamlanan: ' . $this->tamamlananKontroller->count() . ' kontrol')
            ->line('❌ Eksik Kalan: ' . $this->eksikKontroller->count() . ' kontrol')
            ->line('📈 Tamamlanma Oranı: %' . $tamamlanmaOrani);

        if ($this->tamamlananKontroller->isNotEmpty()) {
            $mail->line('---')
                 ->line('✅ **Tamamlanan Kontroller:**');

            foreach ($this->tamamlananKontroller->take(10) as $item) {
                $durum = $item['durum'] === 'uygun' ? '✓' : '⚠';
                $mail->line($durum . ' ' . $item['bina'] . ' - ' . $item['kontrol'] . ' (Yapan: ' . $item['yapan'] . ')');
            }

            if ($this->tamamlananKontroller->count() > 10) {
                $mail->line('... ve ' . ($this->tamamlananKontroller->count() - 10) . ' kontrol daha');
            }
        }

        if ($this->eksikKontroller->isNotEmpty()) {
            $mail->line('---')
                 ->line('❌ **Eksik Kalan Kontroller:**');

            foreach ($this->eksikKontroller->take(5) as $item) {
                $mail->line('• ' . $item['bina'] . ' - ' . $item['kontrol']);
            }

            if ($this->eksikKontroller->count() > 5) {
                $mail->line('... ve ' . ($this->eksikKontroller->count() - 5) . ' kontrol daha');
            }
        }

        $mail->action('Detaylı Raporu Görüntüle', route('admin.raporlar.index', ['tarih' => $this->tarih]));

        return $mail->salutation('Saygılarımızla, ' . config('app.name'));
    }
}
