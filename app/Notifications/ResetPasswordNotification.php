<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = $this->generateResetUrl($notifiable);

        return (new MailMessage)
            ->subject('Şifre Sıfırlama Talebi')
            ->greeting('Merhaba ' . $notifiable->ad)
            ->line('Hesabınız için şifre sıfırlama talebi aldık.')
            ->line('Şifrenizi sıfırlamak için aşağıdaki butona tıklayın:')
            ->action('Şifremi Sıfırla', $resetUrl)
            ->line('Bu bağlantı 60 dakika süreyle geçerlidir.')
            ->line('Eğer şifre sıfırlama talebinde bulunmadıysanız, bu e-postayı görmezden gelebilirsiniz.')
            ->salutation('Saygılarımızla, ' . config('app.name') . ' Ekibi');
    }

    private function generateResetUrl(object $notifiable): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));
    }
}
