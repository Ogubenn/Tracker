<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DashboardNoteNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $note,
        public string $senderName
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Yeni Not Bildirimi - Atıksu Takip')
            ->greeting("Merhaba {$notifiable->ad}!")
            ->line("**{$this->senderName}** tarafından yeni bir not paylaşıldı:")
            ->line('')
            ->line($this->note)
            ->line('')
            ->line('Bu notu dashboard üzerinden görüntüleyebilirsiniz.')
            ->action('Panele Git', url('/admin'))
            ->salutation('.,')
            ->salutation('Atıksu Takip Sistemi');
    }
}
