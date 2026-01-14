<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\TestBildirimi;
use Illuminate\Console\Command;

class TestMail extends Command
{
    /**
     * Artisan komut adı.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : E-posta adresi (boş ise ilk admin)}';

    /**
     * Komut açıklaması.
     *
     * @var string
     */
    protected $description = 'Mail sistemini test etmek için test bildirimi gönderir';

    /**
     * Komutu çalıştır.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        // Email belirtilmediyse ilk admin kullanıcıyı al
        if (!$email) {
            $user = User::where('rol', 'admin')
                ->where('aktif_mi', true)
                ->first();

            if (!$user) {
                $this->error('Aktif admin kullanıcı bulunamadı.');
                return 1;
            }

            $email = $user->email;
            $this->info("Email belirtilmedi, {$user->ad} ({$email}) adresine gönderiliyor...");
        } else {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("'{$email}' adresiyle kullanıcı bulunamadı.");
                return 1;
            }
        }

        try {
            $user->notify(new TestBildirimi());
            
            $this->newLine();
            $this->info('✅ Test maili başarıyla gönderildi!');
            $this->line("📧 Alıcı: {$user->ad} ({$user->email})");
            $this->line("⏰ Gönderim: " . now()->format('d.m.Y H:i:s'));
            $this->newLine();
            $this->warn('Mail kutunuzu kontrol edin (spam klasörüne de bakın).');
            
            return 0;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Test maili gönderilemedi!');
            $this->line("Hata: {$e->getMessage()}");
            $this->newLine();
            $this->warn('Mail yapılandırmanızı (.env) kontrol edin:');
            $this->line('- MAIL_MAILER');
            $this->line('- MAIL_HOST');
            $this->line('- MAIL_PORT');
            $this->line('- MAIL_USERNAME');
            $this->line('- MAIL_PASSWORD');
            $this->line('- MAIL_FROM_ADDRESS');
            
            return 1;
        }
    }
}
