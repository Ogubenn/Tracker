<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestBildirimi extends Notification
{
    use Queueable;

    /**
     * Bildirim kanallarını belirle.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Mail mesajını oluştur.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Atıksu Takip - Test Maili')
            ->greeting("Merhaba {$notifiable->ad}!")
            ->line('Bu bir test mailidir.')
            ->line('Mail sisteminiz başarıyla çalışıyor! ✅')
            ->line('Gönderim Zamanı: ' . now()->format('d.m.Y H:i:s'))
            ->action('Panele Git', url('/admin'))
            ->line('Mail bildirimleriniz düzgün çalışıyor.')
            ->salutation('İyi çalışmalar,')
            ->salutation('Atıksu Takip Sistemi');
    }
}
