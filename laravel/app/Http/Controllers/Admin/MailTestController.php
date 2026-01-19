<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KontrolMaddesi;
use App\Models\SiteAyarlari;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Exception;

class MailTestController extends Controller
{
    public function index(): View
    {
        // SMTP Konfigürasyonu
        $smtpConfig = [
            'mail_mailer' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ];

        // Cron Job Bilgileri
        $cronInfo = [
            'cron_url' => url('/cron-trigger?key=' . config('app.cron_secret_key')),
            'secret_key' => config('app.cron_secret_key'),
        ];

        // Sistem İstatistikleri
        $stats = [
            'toplam_kullanici' => User::where('aktif_mi', true)->count(),
            'mail_alan_kullanici' => User::where('aktif_mi', true)->where('mail_alsin', true)->count(),
            'toplam_kontrol_maddesi' => KontrolMaddesi::count(),
            'eksik_kontrol_aktif' => SiteAyarlari::getBool('eksik_kontrol_mail_aktif'),
            'toplu_rapor_aktif' => SiteAyarlari::getBool('toplu_rapor_mail_aktif'),
        ];

        // Schedule Saatleri
        $scheduleInfo = [
            'sabah_saat' => SiteAyarlari::get('eksik_kontrol_sabah_saat', '07:00'),
            'aksam_saat' => SiteAyarlari::get('eksik_kontrol_aksam_saat', '19:00'),
            'rapor_saat' => SiteAyarlari::get('toplu_rapor_saat', '19:00'),
        ];

        return view('admin.mail-test.index', compact('smtpConfig', 'cronInfo', 'stats', 'scheduleInfo'));
    }

    public function testSmtp(Request $request): RedirectResponse
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            Mail::raw('Bu bir SMTP test mailidir. Mail yapılandırmanız doğru çalışıyor! ✅\n\nGönderim Zamanı: ' . now()->format('d.m.Y H:i:s') . '\n\nBulancak Belediyesi Atıksu Takip Sistemi', function($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('SMTP Test - Atıksu Takip Sistemi');
            });

            return redirect()->route('admin.mail-test.index')
                ->with('success', '✅ SMTP test maili başarıyla gönderildi! Lütfen ' . $request->test_email . ' adresini kontrol edin.');
        } catch (Exception $e) {
            return redirect()->route('admin.mail-test.index')
                ->with('error', '❌ SMTP bağlantısı başarısız! Hata: ' . $e->getMessage());
        }
    }

    public function testScheduledMail(Request $request): RedirectResponse
    {
        $request->validate([
            'mail_type' => 'required|in:sabah,aksam,rapor'
        ]);

        try {
            $mailType = $request->mail_type;
            
            if ($mailType === 'sabah' || $mailType === 'aksam') {
                Artisan::call('kontrol:eksik-mail', ['tur' => $mailType]);
                $message = '📧 ' . ucfirst($mailType) . ' eksik kontrol maili gönderildi!';
            } else {
                Artisan::call('kontrol:toplu-rapor');
                $message = '📊 Günlük toplu rapor maili gönderildi!';
            }

            $output = Artisan::output();
            
            return redirect()->route('admin.mail-test.index')
                ->with('success', $message . ' Detay: ' . $output);
        } catch (Exception $e) {
            return redirect()->route('admin.mail-test.index')
                ->with('error', '❌ Scheduled mail gönderilemedi! Hata: ' . $e->getMessage());
        }
    }

    public function testCron(): RedirectResponse
    {
        try {
            Artisan::call('schedule:run');
            $output = Artisan::output();
            
            return redirect()->route('admin.mail-test.index')
                ->with('success', '🔄 Schedule:run komutu çalıştırıldı! Çıktı: ' . $output);
        } catch (Exception $e) {
            return redirect()->route('admin.mail-test.index')
                ->with('error', '❌ Schedule:run başarısız! Hata: ' . $e->getMessage());
        }
    }
}
