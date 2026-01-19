<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class EksikKontrolBildirimi extends Notification implements ShouldQueue
{
    use Queueable;

    private Collection $eksikKontroller;
    private string $bildirimTuru;

    public function __construct(Collection $eksikKontroller, string $bildirimTuru = 'sabah')
    {
        $this->eksikKontroller = $eksikKontroller;
        $this->bildirimTuru = $bildirimTuru;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $baslik = $this->bildirimTuru === 'sabah' 
            ? 'Bugün Yapılacak Kontroller' 
            : 'Eksik Kontroller Uyarısı';

        $mesaj = $this->bildirimTuru === 'sabah'
            ? 'Bugün yapılması gereken kontroller:'
            : 'Bugün yapılması gereken ancak henüz tamamlanmamış kontroller:';

        $mail = (new MailMessage)
            ->subject($baslik . ' - ' . now()->format('d.m.Y'))
            ->greeting('Merhaba ' . $notifiable->ad)
            ->line($mesaj);

        foreach ($this->eksikKontroller as $item) {
            $mail->line('• **' . $item['bina'] . '** - ' . $item['kontrol'] . ' (' . $item['periyot'] . ')');
        }

        $mail->line('Toplam ' . $this->eksikKontroller->count() . ' adet kontrol yapılması gerekiyor.');

        if ($this->bildirimTuru === 'sabah') {
            $mail->action('Kontrolleri Görüntüle', route('personel.dashboard'));
        } else {
            $mail->line('**Lütfen en kısa sürede bu kontrolleri tamamlayın.**');
        }

        return $mail->salutation('., ' . config('app.name'));
    }
}
